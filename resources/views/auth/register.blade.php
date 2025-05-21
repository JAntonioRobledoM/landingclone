@extends('layouts.app')

@section('title', 'Registro - Everlasting Art')

@section('styles')
    <style>
        /* Estilos específicos para la página de registro */
        .bg-login {
            background-image: url('{{ asset('images/fondo3cuadros.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            z-index: 0;
        }

        .min-vh-100 {
            min-height: 100vh;
        }

        .form-signin {
            max-width: 500px;
            margin: 0 auto;
            z-index: 1;
            position: relative;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        /* Override navbar styles for register page */
        .navbar {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Hide footer for register page */
        footer {
            display: none;
        }

        /* Estilos para el campo de motivación */
        #motivationField {
            display: none;
            /* Inicialmente oculto */
            transition: all 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <section class="position-relative">
        <!-- Fondo -->
        <div class="bg-login"></div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="d-flex flex-column min-vh-100 p-4">
                        <!-- Logotipo -->
                        <div class="text-center">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('images/logo-light.png') }}" width="125" height="125" alt="logo">
                            </a>
                        </div>

                        <div class="title-heading text-center my-auto">
                            <div class="form-signin px-4 py-5 bg-white rounded-md shadow-sm">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <h5 class="mb-4">Registra tu cuenta</h5>

                                    <!-- Mensajes de error y éxito -->
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            @foreach($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <!-- Selector de rol -->
                                    <div class="d-flex justify-content-center gap-2 mb-4">
                                        <button type="button"
                                            class="btn btn-lg {{ old('role', 'user') === 'user' ? 'btn-primary' : 'btn-outline-primary' }}"
                                            id="roleUserBtn" onclick="selectRole('user')">
                                            Registrarme como Usuario
                                        </button>
                                        <button type="button"
                                            class="btn btn-lg {{ old('role', 'user') === 'artist' ? 'btn-primary' : 'btn-outline-primary' }}"
                                            id="roleArtistBtn" onclick="selectRole('artist')">
                                            Registrarme como Artista
                                        </button>
                                        <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'user') }}">
                                    </div>



                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            id="firstName" name="first_name" placeholder="Nombre" required
                                            value="{{ old('first_name') }}">
                                        <label for="firstName">Nombre</label>
                                    </div>

                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            id="lastName" name="last_name" placeholder="Apellido" required
                                            value="{{ old('last_name') }}">
                                        <label for="lastName">Apellido</label>
                                    </div>

                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username" placeholder="Nombre de usuario" required
                                            value="{{ old('username') }}">
                                        <label for="username">Nombre de usuario</label>
                                    </div>

                                    <div class="form-floating mb-2">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                                        <label for="email">Email</label>
                                    </div>

                                    <div class="form-floating mb-2">
                                        <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                            id="birthday" name="birthday" required value="{{ old('birthday') }}">
                                        <label for="birthday">Fecha de nacimiento</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Contraseña" required>
                                        <label for="password">Contraseña</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Confirmar contraseña" required>
                                        <label for="password_confirmation">Confirmar contraseña</label>
                                    </div>

                                    <!-- Campo de motivación (aparece solo si es artista) -->
                                    <div id="motivationField" class="mb-3"
                                        style="{{ old('role') === 'artist' ? 'display: block;' : 'display: none;' }}">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('motivation') is-invalid @enderror"
                                                id="motivation" name="motivation"
                                                placeholder="¿Por qué quieres ser artista?"
                                                style="height: 120px">{{ old('motivation') }}</textarea>
                                            <label for="motivation">¿Por qué quieres ser artista en Everlasting Art?</label>
                                        </div>
                                        <small class="text-muted">Cuéntanos brevemente por qué te gustaría unirte como
                                            artista. Esta información nos ayudará a conocerte mejor.</small>
                                    </div>

                                    <div class="form-check align-items-center d-flex mb-3">
                                        <input class="form-check-input mt-0" type="checkbox" id="acceptTerms" name="terms"
                                            required>
                                        <label class="form-check-label text-muted ms-2" for="acceptTerms">
                                            Acepto <a href="#" class="text-primary">Términos y Condiciones</a>
                                        </label>
                                    </div>

                                    <button class="btn btn-primary rounded-md w-100" type="submit">
                                        Registrarse
                                    </button>

                                    <div class="col-12 text-center mt-4">
                                        <small>
                                            <span class="text-muted me-2">¿Tienes ya una cuenta?</span>
                                            <a href="{{ route('login') }}" class="text-dark fw-bold">Iniciar sesión</a>
                                        </small>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- © DEBAJO DEL CUADRO BLANCO -->
                        <div class="text-center text-black-50">
                            <small class="mb-0">
                                © {{ date('Y') }} Everlasting Art
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Ocultar navbar en la página de registro
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.querySelector('header');
            if (navbar) {
                navbar.style.display = 'none';
            }

            // Si hay un error de validación y el rol es artista, mostrar el campo de motivación
            if (document.getElementById('roleInput').value === 'artist') {
                document.getElementById('motivationField').style.display = 'block';
            }
        });

        // Función para seleccionar el rol
        function selectRole(role) {
            document.getElementById('roleInput').value = role;

            // Mostrar u ocultar campo de motivación
            if (role === 'artist') {
                document.getElementById('motivationField').style.display = 'block';
            } else {
                document.getElementById('motivationField').style.display = 'none';
            }

            // Actualizar estilos de los botones
            if (role === 'user') {
                document.getElementById('roleUserBtn').classList.add('btn-primary');
                document.getElementById('roleUserBtn').classList.remove('btn-outline-primary');

                document.getElementById('roleArtistBtn').classList.remove('btn-primary');
                document.getElementById('roleArtistBtn').classList.add('btn-outline-primary');
            } else {
                document.getElementById('roleArtistBtn').classList.add('btn-primary');
                document.getElementById('roleArtistBtn').classList.remove('btn-outline-primary');

                document.getElementById('roleUserBtn').classList.remove('btn-primary');
                document.getElementById('roleUserBtn').classList.add('btn-outline-primary');
            }
        }
    </script>
@endsection