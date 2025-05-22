<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            
            $user = Auth::user();
            
            // Redirigir según el rol del usuario
            if ($user->role === 'artist') {
                return redirect()->intended('/artist/dashboard');
            } elseif ($user->role === 'pending_artist') {
                return redirect()->intended('/dashboard')->with('info', 
                    'Tu solicitud de artista está pendiente de aprobación. Te notificaremos cuando sea procesada.');
            } else {
                return redirect()->intended('/dashboard');
            }
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
            'birthday' => 'nullable|date|before:today',
            'role' => 'required|in:user,artist',
            'terms' => 'required',
        ];
        
        // Añadir validaciones específicas para artistas
        if ($request->role === 'artist') {
            $validationRules['motivation'] = 'required|string|min:50|max:1000';
            $validationRules['artwork_image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'; // 5MB max
            $validationRules['artwork_title'] = 'required|string|max:255';
            $validationRules['artwork_description'] = 'nullable|string|max:1000';
        }
        
        $validator = Validator::make($request->all(), $validationRules, [
            'motivation.min' => 'La motivación debe tener al menos 50 caracteres.',
            'motivation.max' => 'La motivación no puede exceder los 1000 caracteres.',
            'artwork_image.required' => 'Debes subir al menos una obra para registrarte como artista.',
            'artwork_image.image' => 'El archivo debe ser una imagen.',
            'artwork_image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'artwork_image.max' => 'La imagen no puede ser mayor a 5MB.',
            'artwork_title.required' => 'El título de la obra es obligatorio.',
            'artwork_title.max' => 'El título no puede exceder los 255 caracteres.',
            'artwork_description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'birthday.before' => 'Debes proporcionar una fecha de nacimiento válida.',
            'terms.required' => 'Debes aceptar los términos y condiciones.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Crear el usuario - si selecciona artista, asignarle rol 'pending_artist'
        $role = $request->role === 'artist' ? 'pending_artist' : 'user';
        
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'role' => $role, 
        ]);

        // Si quiere ser artista, crear una solicitud de artista con la motivación y obra
        if ($request->role === 'artist') {
            // Crear la solicitud de artista
            ArtistRequest::create([
                'user_id' => $user->id,
                'motivation' => $request->motivation,
                'status' => 'pending',
            ]);

            // Guardar la obra de arte si se subió
            if ($request->hasFile('artwork_image')) {
                $this->saveArtwork($user, $request->file('artwork_image'), $request->all());
            }

            return redirect()->route('login')
                ->with('registered', true)
                ->with('success', '¡Registro exitoso! Tu solicitud de artista está pendiente de aprobación. Te notificaremos por email cuando sea procesada.');
        }

        return redirect()->route('login')
            ->with('registered', true)
            ->with('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
    }

    /**
     * Guardar la obra de arte del registro
     */
    protected function saveArtwork(User $user, $imageFile, array $data)
    {
        try {
            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
            
            // Guardar en storage/app/public/artworks
            $imagePath = $imageFile->storeAs('artworks', $fileName, 'public');

            // Crear registro de la obra
            Artwork::create([
                'user_id' => $user->id,
                'title' => $data['artwork_title'],
                'description' => $data['artwork_description'] ?? null,
                'image_path' => $imagePath,
                'original_filename' => $imageFile->getClientOriginalName(),
                'mime_type' => $imageFile->getMimeType(),
                'file_size' => $imageFile->getSize(),
                'is_portfolio_piece' => true,
                'status' => 'pending', // Requiere aprobación del administrador
            ]);

        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error al guardar obra en registro: ' . $e->getMessage());
            
            // No fallar el registro por esto, solo log del error
            // El usuario se puede registrar sin la obra si hay problemas
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Procesar aprobación de artista (método para admin)
     */
    public function approveArtist(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->role !== 'pending_artist') {
            return back()->with('error', 'El usuario no tiene una solicitud de artista pendiente.');
        }

        // Cambiar rol a artista
        $user->role = 'artist';
        $user->save();

        // Actualizar estado de la solicitud
        $artistRequest = ArtistRequest::where('user_id', $userId)->first();
        if ($artistRequest) {
            $artistRequest->status = 'approved';
            $artistRequest->save();
        }

        // Aprobar todas las obras pendientes del usuario
        Artwork::where('user_id', $userId)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        return back()->with('success', 'Artista aprobado exitosamente.');
    }

    /**
     * Rechazar solicitud de artista (método para admin)
     */
    public function rejectArtist(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->role !== 'pending_artist') {
            return back()->with('error', 'El usuario no tiene una solicitud de artista pendiente.');
        }

        // Cambiar rol a usuario normal
        $user->role = 'user';
        $user->save();

        // Actualizar estado de la solicitud
        $artistRequest = ArtistRequest::where('user_id', $userId)->first();
        if ($artistRequest) {
            $artistRequest->status = 'rejected';
            $artistRequest->save();
        }

        // Rechazar todas las obras pendientes del usuario
        Artwork::where('user_id', $userId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud de artista rechazada.');
    }
}