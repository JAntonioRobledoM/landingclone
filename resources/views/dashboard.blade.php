@extends('layouts.app')

@section('title', 'Dashboard - Everlasting Art')

@section('content')
<div class="container py-5">
    @if(Auth::user()->role == 'artist')
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
        <!-- Dashboard para administradores (versión original) -->
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
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">Usuarios</a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Panel de Administración</h4>
                    </div>
                    <div class="card-body">
                        <h5>Bienvenido al panel de administración</h5>
                        <p>Desde aquí puedes gestionar todos los aspectos de la plataforma Everlasting Art.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Solicitudes pendientes</h5>
                                        <p class="display-4">{{ \App\Models\ArtistRequest::where('status', 'pending')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Usuarios registrados</h5>
                                        <p class="display-4">{{ \App\Models\User::count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Artistas aprobados</h5>
                                        <p class="display-4">{{ \App\Models\User::where('role', 'artist_approved')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gestión de Solicitudes de Artistas -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Solicitudes de Artistas Pendientes</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @php
                            // Obtener solicitudes de artistas pendientes
                            $pendingRequests = \App\Models\ArtistRequest::with('user')
                                ->where('status', 'pending')
                                ->get();
                        @endphp
                        
                        @if($pendingRequests->isEmpty())
                            <div class="alert alert-info">
                                No hay solicitudes pendientes en este momento.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Fecha de solicitud</th>
                                            <th>Motivación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingRequests as $request)
                                            <tr>
                                                <td>{{ $request->id }}</td>
                                                <td>{{ $request->user->username }}</td>
                                                <td>{{ $request->user->email }}</td>
                                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#motivationModal-{{ $request->id }}">
                                                        Ver motivación
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $request->id }}">
                                                        Aprobar
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $request->id }}">
                                                        Rechazar
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal de Motivación -->
                                            <div class="modal fade" id="motivationModal-{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Motivación de {{ $request->user->username }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ $request->motivation }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Aprobar solicitud de artista</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro de que deseas aprobar a <strong>{{ $request->user->username }}</strong> como artista?</p>
                                                                <div class="mb-3">
                                                                    <label for="notes-{{ $request->id }}" class="form-label">Notas (opcional):</label>
                                                                    <textarea name="notes" id="notes-{{ $request->id }}" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-success">Aprobar</button>
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
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Rechazar solicitud de artista</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro de que deseas rechazar a <strong>{{ $request->user->username }}</strong> como artista?</p>
                                                                <div class="mb-3">
                                                                    <label for="reject-notes-{{ $request->id }}" class="form-label">Motivo del rechazo:</label>
                                                                    <textarea name="notes" id="reject-notes-{{ $request->id }}" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger">Rechazar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->role == 'artist_approved')
        <!-- Mensaje para artistas aprobados -->
        <div class="card text-center my-5">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h2 class="mb-4">¡Felicidades! Tu solicitud de artista ha sido aprobada</h2>
                <p class="lead mb-4">Ahora formas parte de la comunidad de artistas de Everlasting Art.</p>
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
            </div>
        </div>
    @endif
</div>
@endsection