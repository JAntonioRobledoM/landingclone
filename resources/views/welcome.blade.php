@extends('layouts.app')

@section('title', 'Everlasting Art - Lista de espera')

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    
    /* Estilos para la sección hero */
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('images/fondo3cuadros.png') }}');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 120px 0 120px; 
        margin-top: -20px;
    }
    
    .logo-section {
        margin-bottom: 40px;
    }
    
    .logo-img {
        max-width: 180px;
        margin-bottom: 20px;
    }
    
    .coming-soon {
        display: inline-block;
        background-color: #6c5dd3;
        color: white;
        font-size: 14px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto 30px;
    }
    
    /* Estilos para la tarjeta principal */
    .content-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .main-card {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 40px;
        margin-top: 60px; 
        position: relative;
        z-index: 10;
        margin-bottom: 60px; 
    }
    
    .section-title {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 40px; 
        color: #333;
    }
    
    .options-container {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        margin-bottom: 15px;
    }
    
    .option-box {
        width: 48%;
        padding: 30px;
        background-color: #f9f9fa;
        border-radius: 10px;
    }
    
    .option-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
        font-weight: 500;
    }
    
    .option-description {
        color: #666;
        margin-bottom: 25px;
        line-height: 1.5;
    }
    
    .option-button {
        display: inline-block;
        background-color: #6c5dd3;
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .option-button:hover {
        background-color: #5a4bbf;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 93, 211, 0.2);
        color: white;
    }
    
    .login-section {
        text-align: center;
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid #eee;
    }
    
    .login-question {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: #333;
    }
    
    .login-button {
        display: inline-block;
        border: 2px solid #6c5dd3;
        color: #6c5dd3;
        background-color: transparent;
        padding: 15px 25px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .login-button:hover {
        background-color: #6c5dd3;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Estilos para las características */
    .features-section {
        display: flex;
        justify-content: space-around;
        margin-top: 70px; 
        margin-bottom: 60px; 
    }
    
    .feature-item {
        text-align: center;
        width: 30%;
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        background-color: #f0f0ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #6c5dd3;
        font-size: 2rem;
    }
    
    .feature-title {
        font-size: 1.3rem;
        margin-bottom: 15px;
        color: #333;
    }
    
    .feature-desc {
        color: #666;
        line-height: 1.5;
    }
    
    .footer {
        text-align: center;
        margin-top: 60px; 
        padding-bottom: 60px; 
        color: #888;
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="logo-section">
                <img src="{{ asset('images/logo-light.png') }}" alt="Everlasting Art Logo" class="logo-img">
            </div>
            <div class="coming-soon">Próximamente</div>
            <h1 class="hero-title">Everlasting Art</h1>
            <p class="hero-subtitle">La nueva plataforma que conectará artistas con amantes del arte está en desarrollo. Únete a nuestra lista de espera para ser de los primeros en acceder.</p>
        </div>
    </section>

    <div class="content-container">
        <!-- Main Card -->
        <div class="main-card">            
            <div class="options-container">
                <!-- Opción para usuarios -->
                <div class="option-box text-center">
                    <h2 class="option-title">Descubre y colecciona creaciones únicas de artistas de todo el mundo.</h2>
                    <p class="option-description">Explora obras excepcionales y conecta con los artistas detrás de ellas.</p>
                    <a href="{{ route('register') }}" class="option-button">Unirme como Usuario</a>
                </div>
                
                <!-- Opción para artistas -->
                <div class="option-box text-center">
                    <h2 class="option-title">Comparte tus creaciones y conecta con personas que aprecien tu arte.</h2>
                    <p class="option-description">Muestra tu talento al mundo y forma parte de una comunidad creativa vibrante.</p>
                    <a href="{{ route('register') }}?role=artist" class="option-button">Unirme como Artista</a>
                </div>
            </div>
            
            <!-- Login Section -->
            <div class="login-section">
                <h3 class="login-question">¿Ya tienes una cuenta?</h3>
                <a href="{{ route('login') }}" class="login-button">INICIAR SESIÓN</a>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="features-section">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-lightning-fill"></i>
                </div>
                <h3 class="feature-title">Acceso anticipado</h3>
                <p class="feature-desc">Sé de los primeros en experimentar nuestra plataforma innovadora.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-star-fill"></i>
                </div>
                <h3 class="feature-title">Contenido exclusivo</h3>
                <p class="feature-desc">Accede a colecciones y eventos especiales solo para miembros.</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h3 class="feature-title">Notificaciones</h3>
                <p class="feature-desc">Mantente informado sobre nuevas características y lanzamientos.</p>
            </div>
        </div>
    </div>
@endsection