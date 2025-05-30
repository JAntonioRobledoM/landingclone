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
use Illuminate\Support\Facades\Log;

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
                return redirect()->intended('/dashboard')->with(
                    'info',
                    'Tu solicitud de artista está pendiente de aprobación. Te notificaremos cuando sea procesada.'
                );
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
        // Validación base
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'birthday' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,artist',
            'terms' => 'required|accepted',
        ];

        $messages = [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'birthday.required' => 'La fecha de nacimiento es obligatoria.',
            'terms.required' => 'Debes aceptar los términos y condiciones.',
        ];

        // Validaciones adicionales para artistas
        if ($request->role === 'artist') {
            $rules = array_merge($rules, [
                'motivation' => 'required|string|max:1000',
                'tlf' => 'nullable|string|max:20', // Campo teléfono opcional
                'artwork_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Obra opcional
                'artwork_title' => 'required_with:artwork_image|string|max:255', // Título requerido solo si hay imagen
                'artwork_description' => 'nullable|string|max:1000',
                'social_media_type' => 'nullable|in:none,instagram,facebook',
                'instagram_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9_.]+\/?$/',
                'facebook_url' => 'nullable|url|regex:/^https?:\/\/(www\.)?facebook\.com\/[a-zA-Z0-9_.]+\/?$/',
            ]);

            $messages = array_merge($messages, [
                'motivation.required' => 'Debes explicar por qué quieres ser artista.',
                'tlf.max' => 'El número de teléfono no puede exceder los 20 caracteres.',
                'artwork_image.image' => 'El archivo debe ser una imagen.',
                'artwork_image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
                'artwork_image.max' => 'La imagen no puede ser mayor a 5MB.',
                'artwork_title.required_with' => 'El título de la obra es obligatorio cuando subes una imagen.',
                'instagram_url.regex' => 'La URL de Instagram debe ser válida.',
                'facebook_url.regex' => 'La URL de Facebook debe ser válida.',
            ]);
        }

        $validatedData = $request->validate($rules, $messages);

        // Validación personalizada para redes sociales
        if ($request->role === 'artist' && $request->social_media_type !== 'none') {
            if ($request->social_media_type === 'instagram' && !$request->filled('instagram_url')) {
                return back()->withErrors(['instagram_url' => 'Debes proporcionar tu URL de Instagram.'])->withInput();
            }
            
            if ($request->social_media_type === 'facebook' && !$request->filled('facebook_url')) {
                return back()->withErrors(['facebook_url' => 'Debes proporcionar tu URL de Facebook.'])->withInput();
            }

            // Asegurar que solo se proporcione una red social
            if ($request->filled('instagram_url') && $request->filled('facebook_url')) {
                return back()->withErrors(['social_media' => 'Solo puedes agregar una red social a la vez.'])->withInput();
            }
        }

        try {
            // Preparar datos del usuario
            $userData = [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'birthday' => $validatedData['birthday'],
                'password' => Hash::make($validatedData['password']),
                'role' => $request->role === 'artist' ? 'pending_artist' : 'user',
            ];

            // Agregar teléfono si es artista y se proporcionó
            if ($request->role === 'artist' && $request->filled('tlf')) {
                $userData['tlf'] = $request->tlf;
            }

            // Agregar redes sociales si es artista
            if ($request->role === 'artist') {
                if ($request->social_media_type === 'instagram' && $request->filled('instagram_url')) {
                    $userData['instagram_url'] = $request->instagram_url;
                } elseif ($request->social_media_type === 'facebook' && $request->filled('facebook_url')) {
                    $userData['facebook_url'] = $request->facebook_url;
                }
            }

            // Crear usuario
            $user = User::create($userData);

            // Si es artista, crear solicitud (con o sin obra)
            if ($request->role === 'artist') {
                $this->createArtistRequest($user, $request, $validatedData);
            }

            // Autenticar usuario
            Auth::login($user);

            return redirect()->route('dashboard')->with(
                'success',
                $request->role === 'artist'
                ? 'Registro exitoso. Tu solicitud de artista está pendiente de aprobación.'
                : 'Registro exitoso. ¡Bienvenido a Everlasting Art!'
            );

        } catch (\Exception $e) {
            Log::error('Error en registro de usuario: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al procesar el registro. Por favor intenta nuevamente.');
        }
    }

    /**
     * Crear solicitud de artista (con o sin obra)
     */
    protected function createArtistRequest(User $user, Request $request, array $validatedData)
    {
        $artworkData = [
            'user_id' => $user->id,
            'motivation' => $validatedData['motivation'],
            'status' => 'pending',
        ];

        // Solo procesar obra si se subió una imagen
        if ($request->hasFile('artwork_image')) {
            $artworkData['artwork_title'] = $validatedData['artwork_title'];
            $artworkData['artwork_description'] = $validatedData['artwork_description'] ?? null;

            $imageFile = $request->file('artwork_image');

            // Generar nombre único para el archivo
            $fileName = 'artist_request_' . $user->id . '_' . time() . '.' . $imageFile->getClientOriginalExtension();

            // Guardar en storage/app/public/artist_requests
            $imagePath = $imageFile->storeAs('artist_requests', $fileName, 'public');

            // Añadir información de la imagen
            $artworkData = array_merge($artworkData, [
                'artwork_image_path' => $imagePath,
                'artwork_original_filename' => $imageFile->getClientOriginalName(),
                'artwork_mime_type' => $imageFile->getMimeType(),
                'artwork_file_size' => $imageFile->getSize(),
            ]);

            // También crear la obra en la tabla artworks para cuando sea aprobada
            $this->saveArtwork($user, $imageFile, $validatedData);
        }

        // Crear la solicitud de artista
        ArtistRequest::create($artworkData);
    }

    /**
     * Guardar la obra de arte del registro (solo si se proporcionó)
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
            Log::error('Error al guardar obra en registro: ' . $e->getMessage());
            // No fallar el registro por esto, solo log del error
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
        try {
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
                $artistRequest->approved_at = now();
                $artistRequest->save();
            }

            // Aprobar todas las obras pendientes del usuario (si las tiene)
            Artwork::where('user_id', $userId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'approved_at' => now()
                ]);

            return back()->with('success', 'Artista aprobado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al aprobar artista: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar la aprobación.');
        }
    }

    /**
     * Rechazar solicitud de artista (método para admin)
     */
    public function rejectArtist(Request $request, $userId)
    {
        try {
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
                $artistRequest->rejected_at = now();
                $artistRequest->save();
            }

            // Rechazar todas las obras pendientes del usuario (si las tiene)
            Artwork::where('user_id', $userId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejected_at' => now()
                ]);

            return back()->with('success', 'Solicitud de artista rechazada.');

        } catch (\Exception $e) {
            Log::error('Error al rechazar artista: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el rechazo.');
        }
    }

    /**
     * Mostrar solicitudes de artistas pendientes (para admin)
     */
    public function showPendingArtists()
    {
        $pendingRequests = ArtistRequest::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pending-artists', compact('pendingRequests'));
    }

    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        $user = Auth::user();
        $pendingRequests = collect(); // Inicializar como colección vacía

        // Si es admin, obtener solicitudes pendientes
        if ($user->role === 'admin') {
            $pendingRequests = ArtistRequest::where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard', compact('pendingRequests'));
    }
}