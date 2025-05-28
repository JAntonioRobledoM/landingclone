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

        .logo-saturada {
            filter: saturate(1);
        }

        /* Estilos para los campos específicos de artista */
        .artist-fields {
            display: none;
            transition: all 0.3s ease;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.375rem;
            border: 2px dashed #dee2e6;
            padding: 10px;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #0d6efd;
        }

        .file-upload-area.dragover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        /* Estilos para la selección de red social */
        .social-media-selection {
            border: 2px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 15px;
            margin-bottom: 15px;
        }

        .social-option {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .social-option:hover {
            background-color: #f8f9fa;
        }

        .social-option.selected {
            border-color: #0d6efd;
            background-color: #e7f3ff;
        }

        .social-option input[type="radio"] {
            margin-right: 10px;
        }

        .social-url-input {
            display: none;
            margin-top: 10px;
        }

        .social-url-input.show {
            display: block;
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
                                <img src="{{ asset('images/logo-light.png') }}" width="125" height="125" class="logo-saturada" alt="logo">
                            </a>
                        </div>

                        <div class="title-heading text-center my-auto">
                            <div class="form-signin px-4 py-5 bg-white rounded-md shadow-sm">
                                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
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

                                    <!-- Campos específicos para artistas -->
                                    <div id="artistFields" class="artist-fields"
                                        style="{{ old('role') === 'artist' ? 'display: block;' : 'display: none;' }}">
                                        
                                        <!-- Campo de motivación -->
                                        <div class="mb-3">
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

                                        <!-- Selección de Red Social -->
                                        <div class="social-media-selection">
                                            <label class="form-label mb-3">
                                                <i class="fas fa-share-alt me-2"></i>Red Social (Opcional)
                                            </label>
                                            <small class="text-muted d-block mb-3">Puedes agregar tu Instagram o Facebook para que los usuarios puedan seguirte</small>
                                            
                                            <div class="social-option" onclick="selectSocialMedia('none')">
                                                <input type="radio" name="social_media_type" value="none" id="socialNone" 
                                                    {{ old('social_media_type', 'none') === 'none' ? 'checked' : '' }}>
                                                <label for="socialNone" class="mb-0">
                                                    <i class="fas fa-times-circle me-2 text-muted"></i>
                                                    No agregar red social
                                                </label>
                                            </div>

                                            <div class="social-option" onclick="selectSocialMedia('instagram')">
                                                <input type="radio" name="social_media_type" value="instagram" id="socialInstagram"
                                                    {{ old('social_media_type') === 'instagram' ? 'checked' : '' }}>
                                                <label for="socialInstagram" class="mb-0">
                                                    <i class="fab fa-instagram me-2 text-danger"></i>
                                                    Instagram
                                                </label>
                                            </div>

                                            <div class="social-url-input" id="instagramUrlInput">
                                                <div class="form-floating">
                                                    <input type="url" class="form-control @error('instagram_url') is-invalid @enderror"
                                                        id="instagramUrl" name="instagram_url" 
                                                        placeholder="https://instagram.com/tu_usuario"
                                                        value="{{ old('instagram_url') }}">
                                                    <label for="instagramUrl">URL de Instagram</label>
                                                </div>
                                                <small class="text-muted">Ejemplo: https://instagram.com/tu_usuario</small>
                                                @error('instagram_url')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="social-option" onclick="selectSocialMedia('facebook')">
                                                <input type="radio" name="social_media_type" value="facebook" id="socialFacebook"
                                                    {{ old('social_media_type') === 'facebook' ? 'checked' : '' }}>
                                                <label for="socialFacebook" class="mb-0">
                                                    <i class="fab fa-facebook me-2 text-primary"></i>
                                                    Facebook
                                                </label>
                                            </div>

                                            <div class="social-url-input" id="facebookUrlInput">
                                                <div class="form-floating">
                                                    <input type="url" class="form-control @error('facebook_url') is-invalid @enderror"
                                                        id="facebookUrl" name="facebook_url" 
                                                        placeholder="https://facebook.com/tu_usuario"
                                                        value="{{ old('facebook_url') }}">
                                                    <label for="facebookUrl">URL de Facebook</label>
                                                </div>
                                                <small class="text-muted">Ejemplo: https://facebook.com/tu_usuario</small>
                                                @error('facebook_url')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Subida de obra -->
                                        <div class="mb-3">
                                            <label class="form-label">Sube una obra para tu portafolio</label>
                                            <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('artworkImage').click()">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-muted"></i>
                                                <p class="mb-1">Haz clic para seleccionar una imagen</p>
                                                <small class="text-muted">o arrastra y suelta aquí</small>
                                                <input type="file" class="d-none @error('artwork_image') is-invalid @enderror" 
                                                    id="artworkImage" name="artwork_image" accept="image/*" onchange="previewImage(this)">
                                            </div>
                                            <div id="imagePreview" class="mt-2"></div>
                                            @error('artwork_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Título de la obra -->
                                        <div class="form-floating mb-2">
                                            <input type="text" class="form-control @error('artwork_title') is-invalid @enderror"
                                                id="artworkTitle" name="artwork_title" placeholder="Título de la obra"
                                                value="{{ old('artwork_title') }}">
                                            <label for="artworkTitle">Título de la obra</label>
                                            @error('artwork_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Descripción de la obra -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <textarea class="form-control @error('artwork_description') is-invalid @enderror"
                                                    id="artworkDescription" name="artwork_description"
                                                    placeholder="Descripción de la obra"
                                                    style="height: 100px">{{ old('artwork_description') }}</textarea>
                                                <label for="artworkDescription">Descripción de la obra (opcional)</label>
                                            </div>
                                            <small class="text-muted">Describe tu obra, técnica utilizada, inspiración, etc.</small>
                                            @error('artwork_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
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

            // Si hay un error de validación y el rol es artista, mostrar los campos de artista
            if (document.getElementById('roleInput').value === 'artist') {
                document.getElementById('artistFields').style.display = 'block';
            }

            // Configurar drag and drop
            setupDragAndDrop();

            // Inicializar selección de red social
            initializeSocialMediaSelection();
        });

        // Función para seleccionar el rol
        function selectRole(role) {
            document.getElementById('roleInput').value = role;

            // Mostrar u ocultar campos de artista
            const artistFields = document.getElementById('artistFields');
            if (role === 'artist') {
                artistFields.style.display = 'block';
                // Hacer requeridos los campos de artista
                document.getElementById('motivation').required = true;
                document.getElementById('artworkImage').required = true;
                document.getElementById('artworkTitle').required = true;
            } else {
                artistFields.style.display = 'none';
                // Remover requerimientos de campos de artista
                document.getElementById('motivation').required = false;
                document.getElementById('artworkImage').required = false;
                document.getElementById('artworkTitle').required = false;
                // Limpiar preview de imagen
                document.getElementById('imagePreview').innerHTML = '';
                // Resetear selección de red social
                selectSocialMedia('none');
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

        // Función para seleccionar red social
        function selectSocialMedia(type) {
            // Remover clases selected de todas las opciones
            document.querySelectorAll('.social-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Ocultar todos los inputs de URL
            document.getElementById('instagramUrlInput').classList.remove('show');
            document.getElementById('facebookUrlInput').classList.remove('show');

            // Limpiar valores de los campos no seleccionados
            if (type !== 'instagram') {
                document.getElementById('instagramUrl').value = '';
            }
            if (type !== 'facebook') {
                document.getElementById('facebookUrl').value = '';
            }

            // Marcar la opción seleccionada
            const selectedRadio = document.querySelector(`input[name="social_media_type"][value="${type}"]`);
            if (selectedRadio) {
                selectedRadio.checked = true;
                selectedRadio.closest('.social-option').classList.add('selected');
            }

            // Mostrar el input correspondiente
            if (type === 'instagram') {
                document.getElementById('instagramUrlInput').classList.add('show');
                document.getElementById('instagramUrl').focus();
            } else if (type === 'facebook') {
                document.getElementById('facebookUrlInput').classList.add('show');
                document.getElementById('facebookUrl').focus();
            }
        }

        // Inicializar la selección de red social basada en old values
        function initializeSocialMediaSelection() {
            const oldSocialType = '{{ old('social_media_type', 'none') }}';
            const oldInstagram = '{{ old('instagram_url') }}';
            const oldFacebook = '{{ old('facebook_url') }}';

            if (oldInstagram && oldSocialType === 'instagram') {
                selectSocialMedia('instagram');
            } else if (oldFacebook && oldSocialType === 'facebook') {
                selectSocialMedia('facebook');
            } else {
                selectSocialMedia('none');
            }
        }

        // Función para previsualizar imagen
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    img.alt = 'Vista previa de la obra';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Configurar drag and drop
        function setupDragAndDrop() {
            const uploadArea = document.getElementById('fileUploadArea');
            const fileInput = document.getElementById('artworkImage');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                uploadArea.classList.add('dragover');
            }

            function unhighlight(e) {
                uploadArea.classList.remove('dragover');
            }

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    previewImage(fileInput);
                }
            }
        }

        // Validación en tiempo real de URLs de redes sociales
        document.getElementById('instagramUrl').addEventListener('input', function() {
            validateSocialUrl(this, 'instagram');
        });

        document.getElementById('facebookUrl').addEventListener('input', function() {
            validateSocialUrl(this, 'facebook');
        });

        function validateSocialUrl(input, platform) {
            const value = input.value.trim();
            if (!value) return;

            let isValid = false;
            if (platform === 'instagram') {
                isValid = /^https?:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9_.]+\/?$/.test(value);
            } else if (platform === 'facebook') {
                isValid = /^https?:\/\/(www\.)?facebook\.com\/[a-zA-Z0-9_.]+\/?$/.test(value);
            }

            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        }
    </script>
@endsection