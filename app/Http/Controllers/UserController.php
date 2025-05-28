<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Muestra la página principal del área de usuarios.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener artistas destacados con sus obras aprobadas
        $featuredArtists = User::where('role', 'artist')
            ->whereHas('artworks', function($query) {
                $query->approved();
            })
            ->with(['artworks' => function($query) {
                $query->approved()->latest()->take(3);
            }])
            ->take(6)
            ->get();
        
        // Obtener obras destacadas recientes
        $featuredArtworks = Artwork::approved()
            ->featured()
            ->with('user')
            ->latest()
            ->take(8)
            ->get();
        
        return view('user.index', compact('featuredArtists', 'featuredArtworks'));
    }

    /**
     * Muestra el perfil del usuario.
     */
    public function perfil()
    {
        $user = Auth::user();
        
        // Si el usuario es artista, cargar sus obras
        $artworks = null;
        if ($user->isArtist()) {
            $artworks = $user->artworks()->latest()->paginate(12);
        }
        
        return view('user.perfil', compact('user', 'artworks'));
    }
    
    /**
     * Muestra el formulario para editar el perfil del usuario.
     */
    public function editarPerfil()
    {
        $user = Auth::user();
        return view('user.editar-perfil', compact('user'));
    }
    
    /**
     * Actualiza el perfil del usuario.
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string|max:45',
            'description' => 'nullable|string',
            'tlf' => 'nullable|string|max:45',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'social_media_type' => 'nullable|in:none,instagram,facebook',
            'instagram_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9_.]+\/?$/',
            'facebook_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?facebook\.com\/[a-zA-Z0-9_.]+\/?$/',
        ], [
            'instagram_url.regex' => 'La URL de Instagram debe ser válida (ejemplo: https://instagram.com/usuario)',
            'facebook_url.regex' => 'La URL de Facebook debe ser válida (ejemplo: https://facebook.com/usuario)',
        ]);
        
        // Validación personalizada: solo una red social a la vez (si es artista)
        if ($user->isArtist()) {
            if ($request->filled('instagram_url') && $request->filled('facebook_url')) {
                return back()->withErrors(['social_media' => 'Solo puedes agregar una red social a la vez (Instagram o Facebook).'])->withInput();
            }
        }
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->description = $request->description;
        $user->tlf = $request->tlf;
        
        // Limpiar redes sociales si es artista
        if ($user->isArtist()) {
            $user->instagram_url = null;
            $user->facebook_url = null;
            
            // Asignar la red social seleccionada
            if ($request->social_media_type === 'instagram' && $request->filled('instagram_url')) {
                $user->instagram_url = $request->instagram_url;
            } elseif ($request->social_media_type === 'facebook' && $request->filled('facebook_url')) {
                $user->facebook_url = $request->facebook_url;
            }
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
        
        $user->save();
        
        return redirect()->route('user.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Muestra las obras de un usuario específico
     */
    public function verObras($userId)
    {
        $artist = User::where('id', $userId)
            ->where('role', 'artist')
            ->firstOrFail();
        
        $artworks = $artist->artworks()
            ->approved()
            ->latest()
            ->paginate(12);
        
        return view('user.artworks', compact('artist', 'artworks'));
    }

    /**
     * Muestra una obra específica
     */
    public function verObra($artworkId)
    {
        $artwork = Artwork::approved()
            ->with('user')
            ->findOrFail($artworkId);
        
        // Obtener obras relacionadas del mismo artista
        $relatedArtworks = Artwork::approved()
            ->where('user_id', $artwork->user_id)
            ->where('id', '!=', $artwork->id)
            ->latest()
            ->take(4)
            ->get();
        
        return view('user.artwork-detail', compact('artwork', 'relatedArtworks'));
    }

    /**
     * Buscar artistas y obras
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->back()->with('error', 'Por favor ingresa un término de búsqueda.');
        }
        
        // Buscar artistas
        $artists = User::where('role', 'artist')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->whereHas('artworks', function($q) {
                $q->approved();
            })
            ->with(['artworks' => function($q) {
                $q->approved()->latest()->take(3);
            }])
            ->paginate(8, ['*'], 'artists_page');
        
        // Buscar obras
        $artworks = Artwork::approved()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orWhereHas('user', function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->with('user')
            ->latest()
            ->paginate(12, ['*'], 'artworks_page');
        
        return view('user.search-results', compact('artists', 'artworks', 'query'));
    }

    /**
     * Explorar todas las obras
     */
    public function explorar()
    {
        $artworks = Artwork::approved()
            ->with('user')
            ->latest()
            ->paginate(16);
        
        return view('user.explore', compact('artworks'));
    }

    /**
     * Explorar todos los artistas
     */
    public function artistas()
    {
        $artists = User::where('role', 'artist')
            ->whereHas('artworks', function($query) {
                $query->approved();
            })
            ->with(['artworks' => function($query) {
                $query->approved()->latest()->take(3);
            }])
            ->paginate(12);
        
        return view('user.artists', compact('artists'));
    }
}