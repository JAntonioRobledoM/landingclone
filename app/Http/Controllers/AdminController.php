<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ArtistRequest;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Mostrar dashboard de administrador
     */
    public function dashboard()
    {
        // Obtener solicitudes pendientes con información de la obra y usuario
        $pendingRequests = ArtistRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener estadísticas adicionales
        $stats = [
            'total_users' => User::count(),
            'total_artists' => User::where('role', 'artist')->count(),
            'pending_artists' => User::where('role', 'pending_artist')->count(),
            'total_artworks' => Artwork::count(),
            'approved_artworks' => Artwork::where('status', 'approved')->count(),
            'pending_artworks' => Artwork::where('status', 'pending')->count(),
            'rejected_artworks' => Artwork::where('status', 'rejected')->count(),
            'pending_requests' => $pendingRequests->count(),
            'approved_requests' => ArtistRequest::where('status', 'approved')->count(),
            'rejected_requests' => ArtistRequest::where('status', 'rejected')->count(),
        ];

        return view('dashboard', compact('pendingRequests', 'stats'));
    }

    /**
     * Mostrar detalles de una solicitud de artista
     */
    public function showArtistRequest($id)
    {
        $artistRequest = ArtistRequest::with('user')->findOrFail($id);
        
        return view('admin.artist-request-detail', compact('artistRequest'));
    }

    /**
     * Aprobar una solicitud de artista
     */
    public function approveArtist(Request $request, $id)
    {
        // Verificar que el usuario actual es admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Buscar la solicitud de artista
        $artistRequest = ArtistRequest::findOrFail($id);
        $user = $artistRequest->user;

        try {
            // Actualizar el rol a 'artist'
            $user->role = 'artist';
            $user->save();

            // Si hay una obra en la solicitud, crear el registro en artworks
            if ($artistRequest->hasArtwork()) {
                // Mover la imagen de artist_requests a artworks
                $oldPath = $artistRequest->artwork_image_path;
                $fileName = 'artwork_' . $user->id . '_' . time() . '.' . pathinfo($artistRequest->artwork_original_filename, PATHINFO_EXTENSION);
                $newPath = 'artworks/' . $fileName;
                
                // Copiar archivo a la nueva ubicación
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->copy($oldPath, $newPath);
                }

                Artwork::create([
                    'user_id' => $user->id,
                    'title' => $artistRequest->artwork_title,
                    'description' => $artistRequest->artwork_description,
                    'image_path' => $newPath,
                    'original_filename' => $artistRequest->artwork_original_filename,
                    'mime_type' => $artistRequest->artwork_mime_type,
                    'file_size' => $artistRequest->artwork_file_size,
                    'is_portfolio_piece' => true,
                    'status' => 'approved', // Aprobar automáticamente la obra de registro
                ]);
            }

            // Actualizar el estado de la solicitud
            $artistRequest->status = 'approved';
            $artistRequest->approved_at = now();
            $artistRequest->approved_by = Auth::id();
            $artistRequest->admin_notes = $request->input('notes');
            $artistRequest->save();

            // Aquí podrías enviar un email notificando al usuario

            return redirect()->route('dashboard')->with('success', 'Solicitud de artista aprobada correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al aprobar la solicitud. Por favor intenta nuevamente.');
        }
    }

    /**
     * Rechazar una solicitud de artista
     */
    public function rejectArtist(Request $request, $id)
    {
        // Verificar que el usuario actual es admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Validar el formulario
        $request->validate([
            'notes' => 'required|string',
        ]);

        // Buscar la solicitud de artista
        $artistRequest = ArtistRequest::findOrFail($id);
        $user = $artistRequest->user;

        try {
            // Cambiar el rol a 'user'
            $user->role = 'user';
            $user->save();

            // Actualizar el estado de la solicitud
            $artistRequest->status = 'rejected';
            $artistRequest->admin_notes = $request->input('notes');
            $artistRequest->save();

            // Opcional: eliminar la imagen de la obra rechazada para ahorrar espacio
            if ($artistRequest->artwork_image_path) {
                Storage::disk('public')->delete($artistRequest->artwork_image_path);
            }

            return redirect()->route('dashboard')->with('success', 'Solicitud de artista rechazada correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al rechazar la solicitud. Por favor intenta nuevamente.');
        }
    }

    /**
     * Listar usuarios
     */
    public function listUsers()
    {
        // Obtener todos los usuarios con sus roles
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', [
            'users' => $users
        ]);
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function showUser($id)
    {
        $user = User::with('artworks')->findOrFail($id);
        
        // Obtener solicitud de artista si existe
        $artistRequest = ArtistRequest::where('user_id', $user->id)->first();
        
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'birthday' => $user->birthday ? $user->birthday->format('d/m/Y') : null,
            'created_at' => $user->created_at->format('d/m/Y H:i'),
            'artworks_count' => $user->artworks->count(),
            'artist_request' => $artistRequest ? [
                'status' => $artistRequest->status,
                'motivation' => $artistRequest->motivation,
                'created_at' => $artistRequest->created_at->format('d/m/Y H:i'),
                'admin_notes' => $artistRequest->admin_notes,
            ] : null,
        ]);
    }

    /**
     * Editar usuario
     */
    public function editUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validación de datos - actualizado para incluir pending_artist y quitar artist_approved
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required', 
                'email', 
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'required|in:user,pending_artist,artist,admin',
            'password' => 'nullable|min:6|confirmed',
            'birthday' => 'nullable|date',
        ]);

        try {
            // Actualizar campos
            $user->first_name = $validatedData['first_name'];
            $user->last_name = $validatedData['last_name'];
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
            $user->role = $validatedData['role'];
            $user->birthday = $validatedData['birthday'];

            // Actualizar contraseña si se proporciona
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            return redirect()->route('admin.users')
                ->with('success', 'Usuario actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario. Por favor intenta nuevamente.');
        }
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser($id)
    {
        // Prevenir eliminación de usuario actual
        if (Auth::id() == $id) {
            return redirect()->route('admin.users')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user = User::findOrFail($id);
        
        try {
            // Eliminar archivos de obras del usuario
            $artworks = $user->artworks;
            foreach ($artworks as $artwork) {
                if ($artwork->image_path) {
                    Storage::disk('public')->delete($artwork->image_path);
                }
            }
            
            // Eliminar también cualquier solicitud de artista asociada y su imagen
            $artistRequest = ArtistRequest::where('user_id', $user->id)->first();
            if ($artistRequest && $artistRequest->artwork_image_path) {
                Storage::disk('public')->delete($artistRequest->artwork_image_path);
            }
            
            // Eliminar registros relacionados
            ArtistRequest::where('user_id', $user->id)->delete();
            $user->artworks()->delete();
            
            // Eliminar usuario
            $user->delete();

            return redirect()->route('admin.users')
                ->with('success', 'Usuario eliminado correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->with('error', 'Error al eliminar el usuario. Por favor intenta nuevamente.');
        }
    }

    /**
     * Listar todas las solicitudes de artistas (aprobadas, rechazadas y pendientes)
     */
    public function listArtistRequests()
    {
        $requests = ArtistRequest::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.artist-requests', compact('requests'));
    }

    /**
     * Mostrar estadísticas del sitio
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_artists' => User::where('role', 'artist')->count(),
            'pending_artists' => User::where('role', 'pending_artist')->count(),
            'total_artworks' => Artwork::count(),
            'approved_artworks' => Artwork::where('status', 'approved')->count(),
            'pending_artworks' => Artwork::where('status', 'pending')->count(),
            'rejected_artworks' => Artwork::where('status', 'rejected')->count(),
            'pending_requests' => ArtistRequest::where('status', 'pending')->count(),
            'approved_requests' => ArtistRequest::where('status', 'approved')->count(),
            'rejected_requests' => ArtistRequest::where('status', 'rejected')->count(),
        ];

        // Registros por mes (últimos 6 meses)
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyRegistrations[$date->format('M Y')] = $count;
        }

        // Obras por mes (últimos 6 meses)
        $monthlyArtworks = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Artwork::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyArtworks[$date->format('M Y')] = $count;
        }

        return view('admin.statistics', compact('stats', 'monthlyRegistrations', 'monthlyArtworks'));
    }

    /**
     * Gestionar obras (aprobar/rechazar obras de artistas)
     */
    public function manageArtworks()
    {
        $pendingArtworks = Artwork::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.manage-artworks', compact('pendingArtworks'));
    }

    /**
     * Aprobar una obra
     */
    public function approveArtwork($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $artwork = Artwork::findOrFail($id);
        $artwork->status = 'approved';
        $artwork->save();

        return redirect()->back()->with('success', 'Obra aprobada correctamente.');
    }

    /**
     * Rechazar una obra
     */
    public function rejectArtwork(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $artwork = Artwork::findOrFail($id);
        $artwork->status = 'rejected';
        $artwork->rejection_reason = $request->reason;
        $artwork->save();

        return redirect()->back()->with('success', 'Obra rechazada correctamente.');
    }
}