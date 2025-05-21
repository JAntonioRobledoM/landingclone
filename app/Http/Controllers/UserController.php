<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Muestra la página principal del área de usuarios.
     */
    public function index()
    {
        return view('user.index');
    }

    /**
     * Muestra el perfil del usuario.
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('user.perfil', compact('user'));
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
        ]);
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->description = $request->description;
        $user->tlf = $request->tlf;
        
        // Procesar imagen de perfil si se ha subido
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = '/storage/' . $path;
        }
        
        $user->save();
        
        return redirect()->route('user.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}