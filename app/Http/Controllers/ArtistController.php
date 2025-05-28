<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ArtistController extends Controller
{

    /**
     * Muestra la página principal del área de artistas.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Estadísticas del artista
        $totalArtworks = $user->artworks()->count();
        $approvedArtworks = $user->artworks()->approved()->count();
        $pendingArtworks = $user->artworks()->where('status', 'pending')->count();
        $rejectedArtworks = $user->artworks()->where('status', 'rejected')->count();
        
        // Obras recientes
        $recentArtworks = $user->artworks()->latest()->take(6)->get();
        
        // Obra más vista (si tienes sistema de vistas)
        $featuredArtwork = $user->artworks()->approved()->latest()->first();
        
        return view('artist.index', compact(
            'totalArtworks', 
            'approvedArtworks', 
            'pendingArtworks', 
            'rejectedArtworks',
            'recentArtworks',
            'featuredArtwork'
        ));
    }
    
    /**
     * Muestra el perfil del artista.
     */
    public function perfil()
    {
        $user = Auth::user();
        $artworks = $user->artworks()->approved()->latest()->paginate(12);
        
        return view('artist.perfil', compact('user', 'artworks'));
    }
    
    /**
     * Muestra el formulario para editar el perfil del artista.
     */
    public function editarPerfil()
    {
        $user = Auth::user();
        return view('artist.editar-perfil', compact('user'));
    }
    
    /**
     * Actualiza el perfil del artista.
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|max:45',
            'description' => 'nullable|string|max:1000',
            'tlf' => 'nullable|string|max:45',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'social_media_type' => 'nullable|in:instagram,facebook',
            'instagram_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9_.]+\/?$/',
            'facebook_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?facebook\.com\/[a-zA-Z0-9_.]+\/?$/',
        ], [
            'instagram_url.regex' => 'La URL de Instagram debe ser válida (ejemplo: https://instagram.com/usuario)',
            'facebook_url.regex' => 'La URL de Facebook debe ser válida (ejemplo: https://facebook.com/usuario)',
        ]);
        
        // Validación personalizada: solo una red social a la vez
        if ($request->filled('instagram_url') && $request->filled('facebook_url')) {
            return back()->withErrors(['social_media' => 'Solo puedes agregar una red social a la vez (Instagram o Facebook).'])->withInput();
        }
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->description = $request->description;
        $user->tlf = $request->tlf;
        
        // Limpiar ambas redes sociales primero
        $user->instagram_url = null;
        $user->facebook_url = null;
        
        // Asignar la red social seleccionada
        if ($request->filled('instagram_url')) {
            $user->instagram_url = $request->instagram_url;
        } elseif ($request->filled('facebook_url')) {
            $user->facebook_url = $request->facebook_url;
        }
        
        // Procesar imagen de perfil si se ha subido
        if ($request->hasFile('profile_picture')) {
            // Eliminar imagen anterior si existe
            if ($user->profile_picture) {
                $oldPath = str_replace('/storage/', '', $user->profile_picture);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = '/storage/' . $path;
        }
        
        // Procesar banner si se ha subido
        if ($request->hasFile('banner_url')) {
            // Eliminar banner anterior si existe
            if ($user->banner_url) {
                $oldPath = str_replace('/storage/', '', $user->banner_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('banner_url')->store('banners', 'public');
            $user->banner_url = '/storage/' . $path;
        }
        
        $user->save();
        
        return redirect()->route('artist.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Muestra todas las obras del artista
     */
    public function misObras()
    {
        $user = Auth::user();
        $artworks = $user->artworks()->latest()->paginate(12);
        
        return view('artist.mis-obras', compact('artworks'));
    }

    /**
     * Muestra el formulario para crear una nueva obra
     */
    public function crearObra()
    {
        return view('artist.crear-obra');
    }

    /**
     * Guarda una nueva obra
     */
    public function guardarObra(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'is_portfolio_piece' => 'boolean',
        ], [
            'title.required' => 'El título de la obra es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'image.required' => 'Debes subir una imagen de la obra.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'image.max' => 'La imagen no puede ser mayor a 5MB.',
        ]);

        try {
            $user = Auth::user();
            $imageFile = $request->file('image');
            
            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
            
            // Guardar en storage/app/public/artworks
            $imagePath = $imageFile->storeAs('artworks', $fileName, 'public');

            // Crear registro de la obra
            Artwork::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'original_filename' => $imageFile->getClientOriginalName(),
                'mime_type' => $imageFile->getMimeType(),
                'file_size' => $imageFile->getSize(),
                'is_portfolio_piece' => $request->boolean('is_portfolio_piece', true),
                'status' => 'pending', // Requiere aprobación
            ]);

            return redirect()->route('artist.mis-obras')
                ->with('success', 'Obra subida exitosamente. Está pendiente de aprobación.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al subir la obra. Por favor intenta nuevamente.');
        }
    }

    /**
     * Muestra una obra específica del artista
     */
    public function verObra($id)
    {
        $user = Auth::user();
        $artwork = $user->artworks()->findOrFail($id);
        
        return view('artist.ver-obra', compact('artwork'));
    }

    /**
     * Muestra el formulario para editar una obra
     */
    public function editarObra($id)
    {
        $user = Auth::user();
        $artwork = $user->artworks()->findOrFail($id);
        
        return view('artist.editar-obra', compact('artwork'));
    }

    /**
     * Actualiza una obra existente
     */
    public function actualizarObra(Request $request, $id)
    {
        $user = Auth::user();
        $artwork = $user->artworks()->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_portfolio_piece' => 'boolean',
        ], [
            'title.required' => 'El título de la obra es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'image.max' => 'La imagen no puede ser mayor a 5MB.',
        ]);

        try {
            // Actualizar campos básicos
            $artwork->title = $request->title;
            $artwork->description = $request->description;
            $artwork->is_portfolio_piece = $request->boolean('is_portfolio_piece', true);
            
            // Si se sube nueva imagen, reemplazar la anterior
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior
                Storage::disk('public')->delete($artwork->image_path);
                
                $imageFile = $request->file('image');
                $fileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
                $imagePath = $imageFile->storeAs('artworks', $fileName, 'public');
                
                $artwork->image_path = $imagePath;
                $artwork->original_filename = $imageFile->getClientOriginalName();
                $artwork->mime_type = $imageFile->getMimeType();
                $artwork->file_size = $imageFile->getSize();
                
                // Si era aprobada, volver a pending por nueva imagen
                if ($artwork->status === 'approved') {
                    $artwork->status = 'pending';
                }
            }
            
            $artwork->save();

            return redirect()->route('artist.ver-obra', $artwork->id)
                ->with('success', 'Obra actualizada exitosamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la obra. Por favor intenta nuevamente.');
        }
    }

    /**
     * Elimina una obra
     */
    public function eliminarObra($id)
    {
        $user = Auth::user();
        $artwork = $user->artworks()->findOrFail($id);
        
        try {
            // Eliminar archivo de imagen
            Storage::disk('public')->delete($artwork->image_path);
            
            // Eliminar registro de base de datos
            $artwork->delete();
            
            return redirect()->route('artist.mis-obras')
                ->with('success', 'Obra eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la obra. Por favor intenta nuevamente.');
        }
    }

    /**
     * Mostrar estadísticas del artista
     */
    public function estadisticas()
    {
        $user = Auth::user();
        
        $stats = [
            'total_obras' => $user->artworks()->count(),
            'obras_aprobadas' => $user->artworks()->approved()->count(),
            'obras_pendientes' => $user->artworks()->where('status', 'pending')->count(),
            'obras_rechazadas' => $user->artworks()->where('status', 'rejected')->count(),
            'obras_destacadas' => $user->artworks()->featured()->count(),
            'obras_portafolio' => $user->artworks()->portfolio()->count(),
        ];
        
        // Obras por mes (últimos 6 meses)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = $user->artworks()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyStats[$date->format('M Y')] = $count;
        }
        
        return view('artist.estadisticas', compact('stats', 'monthlyStats'));
    }
}