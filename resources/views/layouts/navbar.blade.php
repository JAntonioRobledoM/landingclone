<header id="topnav" class="defaultscroll sticky navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <!-- Logo -->
        <a class="logo navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" width="75" height="75" class="logo" alt="Logo">
        </a>

        <!-- Espacio adicional para empujar los elementos a la derecha -->
        <div class="flex-grow-1"></div>

        <!-- Usuario o botones de login/registro -->
        <div class="d-flex align-items-center justify-content-end">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary me-2">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Registrarse</a>
            @else
                <div class="dropdown">
                    <!-- Quitado el dropdown-toggle para eliminar la flecha -->
                    <button class="btn btn-pills p-0" type="button" data-bs-toggle="dropdown">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ Auth::user()->profile_picture }}" alt="Avatar" width="36" height="36"
                                class="rounded-pill avatar avatar-sm-sm">
                        @else
                            <div class="bg-secondary text-white rounded-pill d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                            </div>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end bg-white shadow border-0 mt-3 pb-3 pt-0 overflow-hidden rounded"
                        style="width: 200px">
                        <div class="position-relative">
                            <div class="pt-5 pb-3 bg-gradient-primary"></div>
                            <!-- Modificada esta sección para centrar la foto y el nombre -->
                            <div class="px-3 text-center">
                                <div class="d-flex justify-content-center mt-n4">
                                    @if(Auth::user()->profile_picture)
                                        <img src="{{ Auth::user()->profile_picture }}" alt="Avatar" width="56" height="56"
                                            class="rounded-pill avatar avatar-md-sm img-thumbnail shadow-md">
                                    @else
                                        <div class="bg-secondary text-white rounded-pill d-flex align-items-center justify-content-center mx-auto"
                                            style="width: 56px; height: 56px;">
                                            {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small class="text-center text-dark d-block fw-bold">
                                        {{ Auth::user()->first_name ?? Auth::user()->username }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 px-3">
                                <a href="{{ route('dashboard') }}"
                                    class="dropdown-item py-2 small fw-semibold text-dark d-flex align-items-center">
                                    <i class="bi bi-person me-2"></i> Perfil
                                </a>
                            <div class="dropdown-divider border-top"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item py-2 small fw-semibold text-dark d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</header>