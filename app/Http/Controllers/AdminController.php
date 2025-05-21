<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ArtistRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Mostrar dashboard de administrador
     */
    public function dashboard()
    {
        // Obtener solicitudes pendientes usando el modelo ArtistRequest
        $pendingRequests = ArtistRequest::with('user')
            ->where('status', 'pending')
            ->get();

        return view('dashboard', [
            'pendingRequests' => $pendingRequests
        ]);
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

        // Buscar el usuario que solicitó ser artista
        $user = $artistRequest->user;

        // Actualizar el rol a 'artist' (antes era 'artist_approved')
        $user->role = 'artist';
        $user->save();

        // Actualizar el estado de la solicitud
        $artistRequest->status = 'approved';
        $artistRequest->approved_at = now();
        $artistRequest->approved_by = Auth::id();
        $artistRequest->admin_notes = $request->input('notes');
        $artistRequest->save();

        // Aquí podrías enviar un email notificando al usuario

        return redirect()->route('dashboard')->with('success', 'Solicitud de artista aprobada correctamente.');
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

        // Buscar el usuario que solicitó ser artista
        $user = $artistRequest->user;

        // Cambiar el rol a 'user'
        $user->role = 'user';
        $user->save();

        // Actualizar el estado de la solicitud
        $artistRequest->status = 'rejected';
        $artistRequest->admin_notes = $request->input('notes');
        $artistRequest->save();

        return redirect()->route('dashboard')->with('success', 'Solicitud de artista rechazada correctamente.');
    }

    /**
     * Listar usuarios
     */
    public function listUsers()
    {
        // Obtener todos los usuarios con sus roles
        $users = User::all();

        return view('admin.users', [
            'users' => $users
        ]);
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function showUser($id)
    {
        $user = User::findOrFail($id);
        
        // Puedes personalizar la información que deseas mostrar
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at->format('d/m/Y H:i'),
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
            'password' => 'nullable|min:6|confirmed'
        ]);

        // Actualizar campos
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        // Actualizar contraseña si se proporciona
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Usuario actualizado correctamente.');
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
        
        // Eliminar también cualquier solicitud de artista asociada
        ArtistRequest::where('user_id', $user->id)->delete();
        
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}