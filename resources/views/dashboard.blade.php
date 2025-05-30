@extends('layouts.app')

@section('title', 'Dashboard - Everlasting Art')

@section('styles')
    <style>
        .artwork-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.375rem;
            border: 2px solid #dee2e6;
        }

        .request-card {
            transition: transform 0.2s ease-in-out;
            border: 1px solid #dee2e6;
        }

        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            align-self: flex-end;
            margin-top: 0.25rem;
        }

        .artwork-info {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-top: 0.5rem;
        }

        .modal-artwork-preview {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .no-image-placeholder {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .user-info-table {
            font-size: 0.875rem;
        }

        .user-info-table td {
            padding: 0.375rem 0.5rem;
            border: none;
            border-bottom: 1px solid #dee2e6;
        }

        .user-info-table td:first-child {
            font-weight: 600;
            color: #495057;
            width: 40%;
        }

        .phone-number {
            color: #0d6efd;
            text-decoration: none;
        }

        .phone-number:hover {
            text-decoration: underline;
        }

        .no-artwork-section {
            background-color: #fff8e1;
            border: 1px solid #ffcc02;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin: 0.5rem 0;
        }

        .request-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        @php
            $userRole = Auth::user()->role;
        @endphp

        @if($userRole === 'pending_artist')
            <!-- Mensaje para artistas en lista de espera -->
            <div class="card text-center mt-2 mb-5">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-hourglass-split fs-1 text-primary"></i>
                    </div>
                    <h2 class="mb-4">Te encuentras en la lista de espera de Artistas de Everlasting Art</h2>
                    <p class="lead mb-4">Estamos revisando tu solicitud. Te notificaremos por email cuando tu cuenta de artista
                        sea aprobada.</p>
                </div>
            </div>

        @elseif($userRole === 'admin')
            <!-- Dashboard para administradores -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ Auth::user()->profile_picture }}" alt="Foto de perfil"
                                        class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 120px; height: 120px;">
                                        <span class="fs-1">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <h5>{{ Auth::user()->username }}</h5>
                            <p class="text-muted">Administrador</p>
                        </div>
                    </div>

                    <div class="list-group mb-4">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-people me-2"></i>Usuarios
                        </a>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Tarjetas de estadísticas básicas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock-history fs-2 mb-2"></i>
                                    <h6 class="card-title">Solicitudes pendientes</h6>
                                    <p class="display-6 mb-0">{{ $pendingRequests->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-people fs-2 mb-2"></i>
                                    <h6 class="card-title">Usuarios registrados</h6>
                                    <p class="display-6 mb-0">{{ \App\Models\User::count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-palette2 fs-2 mb-2"></i>
                                    <h6 class="card-title">Artistas aprobados</h6>
                                    <p class="display-6 mb-0">{{ \App\Models\User::where('role', 'artist')->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-image fs-2 mb-2"></i>
                                    <h6 class="card-title">Obras totales</h6>
                                    <p class="display-6 mb-0">{{ \App\Models\Artwork::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gestión de Solicitudes de Artistas -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="bi bi-person-plus me-2"></i>Solicitudes de Artistas Pendientes
                            </h4>
                            @if($pendingRequests->count() > 0)
                                <span class="badge bg-warning fs-6">{{ $pendingRequests->count() }} pendientes</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($pendingRequests->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                                    <h5>No hay solicitudes pendientes</h5>
                                    <p class="text-muted">Todas las solicitudes han sido procesadas.</p>
                                </div>
                            @else
                                <div class="row">
                                    @foreach($pendingRequests as $request)
                                        <div class="col-lg-6 col-xl-4 mb-4">
                                            <div class="card request-card h-100 position-relative">
                                                <!-- Badge indicador del tipo de solicitud -->
                                                <span class="badge request-type-badge {{ $request->hasArtwork() ? 'bg-success' : 'bg-info' }}">
                                                    @if($request->hasArtwork())
                                                        <i class="bi bi-image me-1"></i>Con obra
                                                    @else
                                                        <i class="bi bi-person me-1"></i>Solo perfil
                                                    @endif
                                                </span>

                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $request->user->first_name ?? 'N/A' }}
                                                            {{ $request->user->last_name ?? '' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $request->user->username ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                    <span class="badge badge-status bg-warning">Pendiente</span>
                                                </div>

                                                <!-- Información básica del usuario -->
                                                <div class="px-3 pt-3">
                                                    <table class="table table-sm user-info-table mb-2">
                                                        <tr>
                                                            <td><i class="bi bi-envelope me-1"></i>Email:</td>
                                                            <td class="text-truncate">{{ $request->user->email ?? 'N/A' }}</td>
                                                        </tr>
                                                        @if($request->user && $request->user->tlf)
                                                            <tr>
                                                                <td><i class="bi bi-telephone me-1"></i>Teléfono:</td>
                                                                <td>
                                                                    <a href="tel:{{ $request->user->tlf }}" class="phone-number">
                                                                        {{ $request->user->tlf }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td><i class="bi bi-calendar3 me-1"></i>Solicitud:</td>
                                                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <!-- Mostrar la obra si existe -->
                                                @if($request->hasArtwork())
                                                    <div class="text-center p-3">
                                                        @if($request->artwork_image_url)
                                                            <img src="{{ $request->artwork_image_url }}" alt="{{ $request->artwork_title }}"
                                                                class="artwork-preview" style="cursor: pointer;"
                                                                onclick="showArtworkModal({{ $request->id }})"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <div class="no-image-placeholder" style="display: none;">
                                                                <i class="bi bi-image fs-2 text-muted"></i>
                                                                <small class="text-muted">Imagen no disponible</small>
                                                            </div>
                                                        @else
                                                            <div class="no-image-placeholder">
                                                                <i class="bi bi-image fs-2 text-muted"></i>
                                                                <small class="text-muted">Sin imagen</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="px-3">
                                                        <div class="artwork-info">
                                                            <h6 class="mb-1">
                                                                <i class="bi bi-image me-1"></i>{{ $request->artwork_title ?? 'Sin título' }}
                                                            </h6>
                                                            @if($request->artwork_description)
                                                                <p class="small text-muted mb-2">
                                                                    {{ Str::limit($request->artwork_description, 80) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="px-3">
                                                        <div class="no-artwork-section text-center">
                                                            <i class="bi bi-info-circle text-warning fs-4 mb-2"></i>
                                                            <p class="mb-0 small text-muted">
                                                                <strong>Solicitud sin obra</strong><br>
                                                                El artista podrá subir obras más tarde
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <h6><i class="bi bi-chat-quote me-1"></i>Motivación:</h6>
                                                        <p class="small">
                                                            {{ $request->motivation ? Str::limit($request->motivation, 120) : 'No especificada' }}
                                                        </p>
                                                    </div>

                                                    <!-- Información adicional del usuario -->
                                                    @if($request->user && ($request->user->birthday || $request->user->instagram_url || $request->user->facebook_url))
                                                        <div class="small text-muted mb-3">
                                                            @if($request->user->birthday)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-cake2 me-1"></i>
                                                                    {{ $request->user->birthday->format('d/m/Y') }}
                                                                    ({{ $request->user->birthday->age }} años)
                                                                </div>
                                                            @endif
                                                            @if($request->user->instagram_url)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-instagram me-1 text-danger"></i>
                                                                    <a href="{{ $request->user->instagram_url }}" target="_blank" class="text-decoration-none">
                                                                        Instagram
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            @if($request->user->facebook_url)
                                                                <div class="mb-1">
                                                                    <i class="bi bi-facebook me-1 text-primary"></i>
                                                                    <a href="{{ $request->user->facebook_url }}" target="_blank" class="text-decoration-none">
                                                                        Facebook
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="card-footer bg-white">
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-outline-info btn-sm"
                                                            onclick="showRequestDetails({{ $request->id }})">
                                                            <i class="bi bi-eye me-1"></i>Ver detalles completos
                                                        </button>
                                                        <div class="row g-2">
                                                            <div class="col">
                                                                <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal"
                                                                    data-bs-target="#approveModal-{{ $request->id }}">
                                                                    <i class="bi bi-check-lg me-1"></i>Aprobar
                                                                </button>
                                                            </div>
                                                            <div class="col">
                                                                <button class="btn btn-danger btn-sm w-100" data-bs-toggle="modal"
                                                                    data-bs-target="#rejectModal-{{ $request->id }}">
                                                                    <i class="bi bi-x-lg me-1"></i>Rechazar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de Detalles Completos -->
                                        <div class="modal fade" id="detailsModal-{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-person-badge me-2"></i>
                                                            Solicitud de {{ $request->user->first_name ?? 'Usuario' }}
                                                            {{ $request->user->last_name ?? 'eliminado' }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <!-- Información del usuario -->
                                                            <div class="col-md-6">
                                                                <h6><i class="bi bi-person me-1"></i>Información del Usuario</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Nombre:</strong></td>
                                                                        <td>{{ $request->user->first_name ?? 'N/A' }}
                                                                            {{ $request->user->last_name ?? '' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Usuario:</strong></td>
                                                                        <td>{{ $request->user->username ?? 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Email:</strong></td>
                                                                        <td>{{ $request->user->email ?? 'N/A' }}</td>
                                                                    </tr>
                                                                    @if($request->user && $request->user->tlf)
                                                                        <tr>
                                                                            <td><strong>Teléfono:</strong></td>
                                                                            <td>
                                                                                <a href="tel:{{ $request->user->tlf }}" class="phone-number">
                                                                                    {{ $request->user->tlf }}
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    @if($request->user && $request->user->birthday)
                                                                        <tr>
                                                                            <td><strong>Fecha nacimiento:</strong></td>
                                                                            <td>{{ $request->user->birthday->format('d/m/Y') }} ({{ $request->user->birthday->age }} años)</td>
                                                                        </tr>
                                                                    @endif
                                                                    <tr>
                                                                        <td><strong>Fecha de registro:</strong></td>
                                                                        <td>{{ $request->user ? $request->user->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Solicitud enviada:</strong></td>
                                                                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                                    </tr>
                                                                    @if($request->user && ($request->user->instagram_url || $request->user->facebook_url))
                                                                        <tr>
                                                                            <td><strong>Redes sociales:</strong></td>
                                                                            <td>
                                                                                @if($request->user->instagram_url)
                                                                                    <a href="{{ $request->user->instagram_url }}" target="_blank" class="btn btn-sm btn-outline-danger me-1">
                                                                                        <i class="bi bi-instagram"></i> Instagram
                                                                                    </a>
                                                                                @endif
                                                                                @if($request->user->facebook_url)
                                                                                    <a href="{{ $request->user->facebook_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                                        <i class="bi bi-facebook"></i> Facebook
                                                                                    </a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </table>
                                                            </div>

                                                            <!-- Obra enviada -->
                                                            <div class="col-md-6">
                                                                @if($request->hasArtwork())
                                                                    <h6><i class="bi bi-image me-1"></i>Obra Enviada</h6>
                                                                    <div class="text-center mb-3">
                                                                        @if($request->artwork_image_url)
                                                                            <img src="{{ $request->artwork_image_url }}"
                                                                                alt="{{ $request->artwork_title }}"
                                                                                class="modal-artwork-preview"
                                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                            <div class="no-image-placeholder"
                                                                                style="display: none; height: 300px;">
                                                                                <i class="bi bi-image fs-2 text-muted"></i>
                                                                                <small class="text-muted">Imagen no disponible</small>
                                                                            </div>
                                                                        @else
                                                                            <div class="no-image-placeholder" style="height: 300px;">
                                                                                <i class="bi bi-image fs-2 text-muted"></i>
                                                                                <small class="text-muted">Sin imagen</small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <table class="table table-sm">
                                                                        <tr>
                                                                            <td><strong>Título:</strong></td>
                                                                            <td>{{ $request->artwork_title ?? 'Sin título' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Archivo:</strong></td>
                                                                            <td>{{ $request->artwork_original_filename ?? 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Tamaño:</strong></td>
                                                                            <td>{{ $request->formatted_file_size }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Tipo:</strong></td>
                                                                            <td>{{ $request->artwork_mime_type ?? 'N/A' }}</td>
                                                                        </tr>
                                                                    </table>
                                                                    @if($request->artwork_description)
                                                                        <div class="mt-2">
                                                                            <strong>Descripción:</strong>
                                                                            <p class="small">{{ $request->artwork_description }}</p>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <h6><i class="bi bi-info-circle me-1"></i>Solicitud sin obra</h6>
                                                                    <div class="alert alert-info">
                                                                        <i class="bi bi-info-circle me-2"></i>
                                                                        Este artista ha enviado su solicitud sin obra de muestra. Podrá subir obras una vez aprobado.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Motivación completa -->
                                                        <div class="mt-3">
                                                            <h6><i class="bi bi-chat-quote me-1"></i>Motivación Completa</h6>
                                                            <div class="bg-light p-3 rounded">
                                                                <p class="mb-0">{{ $request->motivation ?? 'No especificada' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cerrar</button>
                                                        <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                                                            data-bs-toggle="modal" data-bs-target="#approveModal-{{ $request->id }}">
                                                            <i class="bi bi-check-lg me-1"></i>Aprobar Artista
                                                        </button>
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                                            data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $request->id }}">
                                                            <i class="bi bi-x-lg me-1"></i>Rechazar Solicitud
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de Aprobación -->
                                        <div class="modal fade" id="approveModal-{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.approve.artist', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-check-circle me-2"></i>Aprobar solicitud de artista
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-success">
                                                                <i class="bi bi-info-circle me-2"></i>
                                                                Al aprobar esta solicitud, el usuario será promovido a artista
                                                                @if($request->hasArtwork())
                                                                    y su obra será añadida automáticamente a su galería.
                                                                @else
                                                                    y podrá empezar a subir obras inmediatamente.
                                                                @endif
                                                            </div>
                                                            <p>¿Estás seguro de que deseas aprobar a
                                                                <strong>{{ $request->user->username ?? 'este usuario' }}</strong> como
                                                                artista?</p>
                                                            <div class="mb-3">
                                                                <label for="notes-{{ $request->id }}" class="form-label">Notas del
                                                                    administrador (opcional):</label>
                                                                <textarea name="notes" id="notes-{{ $request->id }}"
                                                                    class="form-control" rows="3"
                                                                    placeholder="Mensaje de bienvenida o comentarios..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check-lg me-1"></i>Aprobar Artista
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de Rechazo -->
                                        <div class="modal fade" id="rejectModal-{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.reject.artist', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-x-circle me-2"></i>Rechazar solicitud de artista
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                Al rechazar esta solicitud, el usuario será notificado y sus archivos
                                                                serán eliminados.
                                                            </div>
                                                            <p>¿Estás seguro de que deseas rechazar a
                                                                <strong>{{ $request->user->username ?? 'este usuario' }}</strong> como
                                                                artista?</p>
                                                            <div class="mb-3">
                                                                <label for="reject-notes-{{ $request->id }}" class="form-label">
                                                                    Motivo del rechazo <span class="text-danger">*</span>:
                                                                </label>
                                                                <textarea name="notes" id="reject-notes-{{ $request->id }}"
                                                                    class="form-control" rows="3" required
                                                                    placeholder="Explica brevemente por qué se rechaza la solicitud..."></textarea>
                                                                <small class="text-muted">Este mensaje será enviado al usuario.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="bi bi-x-lg me-1"></i>Rechazar Solicitud
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @elseif($userRole === 'artist')
            <!-- Mensaje para artistas aprobados -->
            <div class="card text-center my-5">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle fs-1 text-success"></i>
                    </div>
                    <h2 class="mb-4">¡Felicidades! Tu solicitud de artista ha sido aprobada</h2>
                    <p class="lead mb-4">Ahora formas parte de la comunidad de artistas de Everlasting Art.</p>
                    <a href="{{ route('artist.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-palette me-2"></i>Ir a mi área de artista
                    </a>
                </div>
            </div>

        @elseif($userRole === 'user')
            <!-- Mensaje para usuarios normales -->
            <div class="card text-center my-5">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-palette fs-1 text-info"></i>
                    </div>
                    <h2 class="mb-4">Bienvenido a Everlasting Art</h2>
                    <p class="lead mb-4">Próximamente tendrás acceso a disfrutar del arte en nuestra plataforma. Estamos
                        trabajando para ofrecerte la mejor experiencia.</p>
                </div>
            </div>

        @else
            <!-- Fallback para roles no reconocidos -->
            <div class="card text-center my-5">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-question-circle fs-1 text-warning"></i>
                    </div>
                    <h2 class="mb-4">Rol no reconocido</h2>
                    <p class="lead mb-4">Por favor, contacta con el administrador para resolver este problema.</p>
                    <p class="text-muted">Rol actual: <strong>{{ $userRole }}</strong></p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para vista ampliada de obras -->
    <div class="modal fade" id="artworkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="artworkModalTitle">Vista de obra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="artworkModalImage" src="" alt="" class="img-fluid rounded"
                        onerror="this.style.display='none'; document.getElementById('artworkModalPlaceholder').style.display='flex';">
                    <div id="artworkModalPlaceholder" class="no-image-placeholder" style="display: none; height: 400px;">
                        <i class="bi bi-image fs-1 text-muted"></i>
                        <p class="text-muted">Imagen no disponible</p>
                    </div>
                    <div id="artworkModalInfo" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showRequestDetails(requestId) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal-' + requestId));
            modal.show();
        }

        function showArtworkModal(requestId) {
            // Encontrar los datos de la obra en la página
            const requestCard = document.querySelector(`[onclick="showArtworkModal(${requestId})"]`);
            if (requestCard) {
                const artworkSrc = requestCard.src;
                const artworkAlt = requestCard.alt;

                // Actualizar el modal
                const modalImage = document.getElementById('artworkModalImage');
                const modalTitle = document.getElementById('artworkModalTitle');
                const modalPlaceholder = document.getElementById('artworkModalPlaceholder');

                modalImage.src = artworkSrc;
                modalImage.alt = artworkAlt;
                modalTitle.textContent = artworkAlt || 'Vista de obra';
                modalImage.style.display = 'block';
                modalPlaceholder.style.display = 'none';

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('artworkModal'));
                modal.show();
            }
        }

        // Auto-refresh cada 30 segundos para nuevas solicitudes (solo si hay solicitudes pendientes)
        @if($pendingRequests->count() > 0)
            setInterval(function () {
                // Solo refresh si hay solicitudes pendientes para evitar refresh innecesario
                location.reload();
            }, 30000);
        @endif

        // Mejorar la experiencia de usuario con tooltips
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar tooltips de Bootstrap si están disponibles
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Agregar animaciones suaves a las tarjetas
            const cards = document.querySelectorAll('.request-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Función para manejar errores de imagen de forma más elegante
        function handleImageError(img) {
            const placeholder = img.nextElementSibling;
            if (placeholder && placeholder.classList.contains('no-image-placeholder')) {
                img.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        }

        // Función para precargar imágenes (opcional, para mejor rendimiento)
        function preloadImages() {
            const images = document.querySelectorAll('.artwork-preview[src]');
            images.forEach(img => {
                const preloadImg = new Image();
                preloadImg.src = img.src;
            });
        }

        // Llamar a precargar imágenes cuando la página esté lista
        document.addEventListener('DOMContentLoaded', preloadImages);

        // Función para mostrar notificación de éxito
        function showSuccessMessage(message) {
            // Crear elemento de notificación
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

            document.body.appendChild(alert);

            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        // Función para confirmar acciones importantes
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }

        // Función para copiar teléfono al portapapeles
        function copyPhone(phone) {
            navigator.clipboard.writeText(phone).then(function() {
                showSuccessMessage('Número de teléfono copiado al portapapeles');
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
            });
        }

        // Agregar eventos de clic a los números de teléfono para copiar
        document.addEventListener('DOMContentLoaded', function() {
            const phoneLinks = document.querySelectorAll('.phone-number');
            phoneLinks.forEach(link => {
                link.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    const phone = this.textContent.trim();
                    copyPhone(phone);
                });
            });
        });
    </script>
@endsection