<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Everlasting Art')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Estilos personalizados -->
    <style>
        /* ===== Estilos generales ===== */
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* ===== Estilos para el navbar ===== */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand img {
            max-height: 75px;
        }
        
        .navbar .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar .nav-link:hover {
            color: #0d6efd;
        }
        
        .navbar-dark .nav-link:hover {
            color: #fff !important;
            opacity: 0.8;
        }
        
        /* Dropdown menus */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            border-radius: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        
        /* Estilos para la imagen de perfil */
        .rounded-circle {
            object-fit: cover;
        }
        
        /* Estilo para los textos del navbar */
        .text-navbar {
            font-weight: 500;
            text-decoration: none;
        }
        
        /* User dropdown */
        .dropdown-menu .bg-primary {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        
        /* Botones */
        .btn {
            padding: 0.375rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        /* Header sticky */
        .defaultscroll.sticky {
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-sticky {
            background-color: #fff;
            box-shadow: 0 10px 33px -14px rgba(0, 0, 0, 0.1);
        }

        /* ===== Estilos para el footer ===== */
        .bg-footer {
            background-color: #202942;
            color: #adb5bd;
        }

        .footer-py-60 {
            padding-top: 60px;
            padding-bottom: 60px;
        }

        .footer-py-30 {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .footer-border {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-head {
            color: white;
            font-weight: 500;
            font-size: 1.25rem;
            margin-bottom: 0;
        }

        .text-foot {
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .text-foot:hover {
            color: white;
        }

        .footer-list li {
            margin-bottom: 10px;
        }

        .btn-suscripcion {
            background-color: #6c5dd3;
            color: white;
            border-radius: 5px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .btn-suscripcion:hover {
            background-color: #5a4bbf;
            color: white;
        }

        .footer-bar {
            background-color: #1a2237;
        }

        .social-icon li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .social-icon li a:hover {
            background-color: #6c5dd3;
        }

        /* Botón Volver Arriba */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: #6c5dd3;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99;
            transition: all 0.5s ease;
            text-decoration: none;
        }

        .back-to-top:hover {
            background-color: #202942;
            color: white;
        }
        
        /* Media queries para responsividad */
        @media (max-width: 992px) {
            .navbar-collapse {
                padding: 1rem 0;
            }
            
            .dropdown-menu {
                border: none;
                background-color: transparent;
                box-shadow: none;
            }
            
            .dropdown-item {
                color: rgba(255, 255, 255, 0.55);
                padding: 0.5rem 1.5rem;
            }
            
            .dropdown-item:hover {
                background-color: transparent;
                color: rgba(255, 255, 255, 0.75);
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Incluir Navbar -->
    @include('layouts.navbar')

    <!-- Contenido principal -->
    <main>
        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Incluir Footer -->
    @include('layouts.footer')

    <!-- Bootstrap JS Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script>
        // Script para detectar scroll y aplicar clase sticky al navbar
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            
            if (navbar) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('nav-sticky');
                    } else {
                        navbar.classList.remove('nav-sticky');
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>