<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\ArtistRequest;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Reglas de validación base
        $validationRules = [
            'username' => 'required|string|max:45|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birthday' => 'nullable|date',
            'role' => 'required|in:user,artist',
            'terms' => 'required',
        ];
        
        // Añadir validación de motivación solo si el rol es 'artist'
        if ($request->role === 'artist') {
            $validationRules['motivation'] = 'required|string|min:30|max:500';
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Crear el usuario con el rol seleccionado
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'role' => $request->role, // Usar el rol seleccionado directamente
        ]);

        // Si quiere ser artista, crear una solicitud de artista con la motivación
        if ($request->role === 'artist') {
            ArtistRequest::create([
                'user_id' => $user->id,
                'motivation' => $request->motivation,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('login')
            ->with('registered', true)
            ->with('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}