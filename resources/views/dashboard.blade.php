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
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
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
</style>
@endsection

@section('content')
<div class="container py-5">
    @if(Auth::user()->role == 'pending_artist')
        <!-- Mensaje para artistas en lista de espera -->
        <div class="card text-center my-5">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="bi bi-hourglass-split fs-1 text-primary"></i>
                </div>
                <h2 class="mb-4">Te encuentras en la lista de espera de Artistas de Everlasting Art</h2>
                <p class="lead mb-4">Estamos revisando tu solicitud. Te notificaremos por email cuando tu cuenta de artista sea aprobada.</p>
            </div>
        </div>
    @elseif(Auth::user()->role == 'admin')
        <!-- Dashboard para administradores -->
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ Auth::user()->profile_picture }}" alt="Foto de perfil" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
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
                                        <div class="card request-card h-100">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $request->user->first_name }} {{ $request->user->last_name }}</h6>
                                                    <small class="text-muted">@{{ $request->user->username }}</small>
                                                </div>
                                                <span class="badge badge-status bg-warning">Pendiente</span>
                                            </div>
                                            
                                            <!-- Mostrar la obra si existe -->
                                            @if($request->hasArtwork())
                                                <div class="text-center p-3">
                                                    <img src="{{ $request->artwork_image_url }}" 
                                                         alt="{{ $request->artwork_title }}" 
                                                         class="artwork-preview"
                                                         style="cursor: pointer;"
                                                         onclick="showArtworkModal({{ $request->id }})">
                                                </div>
                                                <div class="px-3">
                                                    <div class="artwork-info">
                                                        <h6 class="mb-1">
                                                            <i class="bi bi-image me-1"></i>{{ $request->artwork_title }}
                                                        </h6>
                                                        @if($request->artwork_description)
                                                            <p class="small text-muted mb-2">
                                                                {{ Str::limit($request->artwork_description, 80) }}
                                                            </p>
                                                        @endif
                                                        <div class="row">
                                                            <div class="col">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-file-image me-1"></i>
                                                                    {{ $request->formatted_file_size }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <h6><i class="bi bi-chat-quote me-1"></i>Motivación:</h6>
                                                    <p class="small">{{ Str::limit($request->motivation, 120) }}</p>
                                                </div>
                                                
                                                <div class="small text-muted mb-3">
                                                    <div class="mb-1">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                                    </div>
                                                    <div>
                                                        <i class="bi bi-envelope me-1"></i>
                                                        {{ $request->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-footer bg-white">
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-outline-info btn-sm" 
                                                            onclick="showRequestDetails({{ $request->id }})">
                                                        <i class="bi bi-eye me-1"></i>Ver detalles completos
                                                    </button>
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <button class="btn btn-success btn-sm w-100" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#approveModal-{{ $request->id }}">
                                                                <i class="bi bi-check-lg me-1"></i>Aprobar
                                                            </button>
                                                        </div>
                                                        <div class="col">
                                                            <button class="btn btn-danger btn-sm w-100" 
                                                                    data-bs-toggle="modal" 
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
                                                        Solicitud de {{ $request->user->first_name }} {{ $request->user->last_name }}
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
                                                                    <td>{{ $request->user->first_name }} {{ $request->user->last_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Usuario:</strong></td>
                                                                    <td>@{{ $request->user->username }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Email:</strong></td>
                                                                    <td>{{ $request->user->email }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Fecha de registro:</strong></td>
                                                                    <td>{{ $request->user->created_at->format('d/m/Y H:i') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Solicitud enviada:</strong></td>
                                                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        
                                                        <!-- Obra enviada -->
                                                        @if($request->hasArtwork())
                                                            <div class="col-md-6">
                                                                <h6><i class="bi bi-image me-1"></i>Obra Enviada</h6>
                                                                <div class="text-center mb-3">
                                                                    <img src="{{ $request->artwork_image_url }}" 
                                                                         alt="{{ $request->artwork_title }}" 
                                                                         class="modal-artwork-preview">
                                                                </div>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Título:</strong></td>
                                                                        <td>{{ $request->artwork_title }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Archivo:</strong></td>
                                                                        <td>{{ $request->artwork_original_filename }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Tamaño:</strong></td>
                                                                        <td>{{ $request->formatted_file_size }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Tipo:</strong></td>
                                                                        <td>{{ $request->artwork_mime_type }}</td>
                                                                    </tr>
                                                                </table>
                                                                @if($request->artwork_description)
                                                                    <div class="mt-2">
                                                                        <strong>Descripción:</strong>
                                                                        <p class="small">{{ $request->artwork_description }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Motivación completa -->
                                                    <div class="mt-3">
                                                        <h6><i class="bi bi-chat-quote me-1"></i>Motivación Completa</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            <p class="mb-0">{{ $request->motivation }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn btn-success" 
                                                            data-bs-dismiss="modal"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#approveModal-{{ $request->id }}">
                                                        <i class="bi bi-check-lg me-1"></i>Aprobar Artista
                                                    </button>
                                                    <button type="button" class="btn btn-danger" 
                                                            data-bs-dismiss="modal"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal-{{ $request->id }}">
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
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-success">
                                                            <i class="bi bi-info-circle me-2"></i>
                                                            Al aprobar esta solicitud, el usuario será promovido a artista y su obra será añadida automáticamente a su galería.
                                                        </div>
                                                        <p>¿Estás seguro de que deseas aprobar a <strong>{{ $request->user->username }}</strong> como artista?</p>
                                                        <div class="mb-3">
                                                            <label for="notes-{{ $request->id }}" class="form-label">Notas del administrador (opcional):</label>
                                                            <textarea name="notes" id="notes-{{ $request->id }}" class="form-control" rows="3" placeholder="Mensaje de bienvenida o comentarios..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                            Al rechazar esta solicitud, el usuario será notificado y sus archivos serán eliminados.
                                                        </div>
                                                        <p>¿Estás seguro de que deseas rechazar a <strong>{{ $request->user->username }}</strong> como artista?</p>
                                                        <div class="mb-3">
                                                            <label for="reject-notes-{{ $request->id }}" class="form-label">
                                                                Motivo del rechazo <span class="text-danger">*</span>:
                                                            </label>
                                                            <textarea name="notes" id="reject-notes-{{ $request->id }}" class="form-control" rows="3" required placeholder="Explica brevemente por qué se rechaza la solicitud..."></textarea>
                                                            <small class="text-muted">Este mensaje será enviado al usuario.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    @elseif(Auth::user()->role == 'artist')
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
    @else
        <!-- Mensaje para usuarios normales -->
        <div class="card text-center my-5">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="bi bi-palette fs-1 text-info"></i>
                </div>
                <h2 class="mb-4">Bienvenido a Everlasting Art</h2>
                <p class="lead mb-4">Próximamente tendrás acceso a disfrutar del arte en nuestra plataforma. Estamos trabajando para ofrecerte la mejor experiencia.</p>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-person-plus me-2"></i>¿Quieres ser artista?
                </a>
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
                <img id="artworkModalImage" src="" alt="" class="img-fluid rounded">
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
    const artworkSrc = requestCard.src;
    const artworkAlt = requestCard.alt;
    
    // Actualizar el modal
    document.getElementById('artworkModalImage').src = artworkSrc;
    document.getElementById('artworkModalImage').alt = artworkAlt;
    document.getElementById('artworkModalTitle').textContent = artworkAlt;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('artworkModal'));
    modal.show();
}

// Auto-refresh cada 30 segundos para nuevas solicitudes
setInterval(function() {
    if ({{ $pendingRequests->count() > 0 ? 'true' : 'false' }}) {
        // Solo refresh si hay solicitudes pendientes
        location.reload();
    }
}, 30000);
</script>
@endsection