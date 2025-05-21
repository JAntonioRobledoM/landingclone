@extends('layouts.app')

@section('title', 'Iniciar Sesión - ArtConnect')

@section('styles')
<style>
    /* Estilos específicos para la página de login */
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
        max-width: 400px;
        margin: 0 auto;
        z-index: 1;
        position: relative;
    }
    
    .rounded-md {
        border-radius: 0.375rem;
    }
    
    /* Override navbar styles for login page */
    .navbar {
        background-color: transparent !important;
        box-shadow: none !important;
    }
    
    main {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Hide footer for login page */
    footer {
        display: none;
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
                <div class="d-flex flex-column min-vh-100 p-5">
                    <!-- Logotipo -->
                    <div class="text-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/logo-light.png') }}" width="125" height="125" alt="logo">
                        </a>
                    </div>
                    
                    <div class="title-heading text-center my-auto">
                        <div class="form-signin px-4 py-5 bg-white rounded-md shadow-sm">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <h5 class="mb-4">Iniciar Sesión</h5>
                                
                                @if(session('registered'))
                                    <div class="alert alert-success">
                                        ¡Registro exitoso! Ahora puedes iniciar sesión.
                                    </div>
                                @endif
                                
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
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-2">
                                            <input 
                                                type="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                id="LoginEmail" 
                                                name="email" 
                                                required 
                                                placeholder="Email"
                                                value="{{ old('email') }}"
                                            >
                                            <label for="LoginEmail">Email:</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input 
                                                type="password" 
                                                class="form-control @error('password') is-invalid @enderror" 
                                                id="LoginPassword" 
                                                name="password" 
                                                required 
                                                placeholder="Contraseña"
                                            >
                                            <label for="LoginPassword">Contraseña:</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between">
                                            <div class="mb-3">
                                                <div class="form-check align-items-center d-flex mb-0">
                                                    <input 
                                                        class="form-check-input mt-0" 
                                                        type="checkbox" 
                                                        id="RememberMe"
                                                        name="remember"
                                                        {{ old('remember') ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label text-muted ms-2" for="RememberMe">
                                                        Recordarme
                                                    </label>
                                                </div>
                                            </div>
                                            <small class="text-muted mb-0">
                                                <a href="{{ route('home') }}" class="text-muted fw-semibold">
                                                    ¿Olvidaste tu contraseña?
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary rounded-md w-100" type="submit">
                                            Iniciar sesión
                                        </button>
                                    </div>
                                    
                                    <div class="col-12 text-center mt-4">
                                        <small>
                                            <span class="text-muted me-2">¿No tienes una cuenta?</span>
                                            <a href="{{ route('register') }}" class="text-dark fw-bold">
                                                Registrarse
                                            </a>
                                        </small>
                                    </div>
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
    // Ocultar navbar en la página de login
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('header');
        if (navbar) {
            navbar.style.display = 'none';
        }
    });
</script>
@endsection