<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    /**
     * Muestra la página principal del área de artistas.
     */
    public function index()
    {
        return view('artist.index');
    }
    
    /**
     * Muestra el perfil del artista.
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('artist.perfil', compact('user'));
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
            'description' => 'nullable|string',
            'tlf' => 'nullable|string|max:45',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        
        // Procesar banner si se ha subido
        if ($request->hasFile('banner_url')) {
            // Aquí procesarías la subida del banner
            $path = $request->file('banner_url')->store('banners', 'public');
            $user->banner_url = '/storage/' . $path;
        }
        
        $user->save();
        
        return redirect()->route('artist.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}