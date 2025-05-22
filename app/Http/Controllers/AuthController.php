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

        // Validaciones adicionales para artistas
        if ($request->role === 'artist') {
            $rules = array_merge($rules, [
                'motivation' => 'required|string|max:1000',
                'artwork_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'artwork_title' => 'required|string|max:255',
                'artwork_description' => 'nullable|string|max:1000',
            ]);
        }

        $validatedData = $request->validate($rules);

        try {
            // Crear usuario base
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'birthday' => $validatedData['birthday'],
                'password' => Hash::make($validatedData['password']),
                'role' => $request->role === 'artist' ? 'pending_artist' : 'user',
            ]);

            // Si es artista, crear solicitud con obra
            if ($request->role === 'artist') {
                $artworkData = [
                    'user_id' => $user->id,
                    'motivation' => $validatedData['motivation'],
                    'artwork_title' => $validatedData['artwork_title'],
                    'artwork_description' => $validatedData['artwork_description'] ?? null,
                ];

                // Procesar la imagen de la obra
                if ($request->hasFile('artwork_image')) {
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
                }

                // Crear la solicitud de artista
                ArtistRequest::create($artworkData);
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
            return back()->withInput()->with('error', 'Error al procesar el registro. Por favor intenta nuevamente.');
        }
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