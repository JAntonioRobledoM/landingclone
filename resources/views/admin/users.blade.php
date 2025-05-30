@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Everlasting Art')

@section('styles')
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .user-avatar-placeholder {
            width: 40px;
            height: 40px;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.875rem;
        }

        .table td {
            vertical-align: middle;
        }

        .phone-number {
            color: #0d6efd;
            text-decoration: none;
        }

        .phone-number:hover {
            text-decoration: underline;
        }

        .user-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .user-info-item {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
        }

        .user-info-item strong {
            color: #495057;
            font-size: 0.875rem;
        }

        .social-links {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .modal-artwork-preview {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .artwork-placeholder {
            width: 100%;
            min-height: 200px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .filter-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            border-radius: 0.375rem;
        }

        .table-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
<div class="container py-5">
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
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-people me-2"></i>Usuarios
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Filtros y búsqueda -->
            <div class="filter-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control search-input" id="searchUsers" placeholder="Buscar por nombre, usuario o email...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filterRole">
                            <option value="">Todos los roles</option>
                            <option value="admin">Administradores</option>
                            <option value="artist">Artistas Aprobados</option>
                            <option value="pending_artist">Artistas Pendientes</option>
                            <option value="user">Usuarios</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-people me-2"></i>Gestión de Usuarios
                    </h4>
                    <span class="badge bg-primary fs-6">{{ $users->count() }} usuarios</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Contacto</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                    <th>Obras</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr data-role="{{ $user->role }}" data-search="{{ strtolower($user->username . ' ' . $user->first_name . ' ' . $user->last_name . ' ' . $user->email) }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($user->profile_picture)
                                                        <img src="{{ $user->profile_picture }}" alt="Avatar" class="rounded-circle user-avatar">
                                                    @else
                                                        <div class="rounded-circle user-avatar-placeholder">
                                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->username }}</div>
                                                    <div class="text-muted small">
                                                        {{ $user->first_name }} {{ $user->last_name }}
                                                    </div>
                                                    @if($user->birthday)
                                                        <div class="text-muted small">
                                                            <i class="bi bi-cake2 me-1"></i>{{ $user->birthday->age }} años
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="small mb-1">
                                                    <i class="bi bi-envelope me-1"></i>
                                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                                                </div>
                                                @if($user->tlf)
                                                    <div class="small">
                                                        <i class="bi bi-telephone me-1"></i>
                                                        <a href="tel:{{ $user->tlf }}" class="phone-number">{{ $user->tlf }}</a>
                                                    </div>
                                                @endif
                                                @if($user->instagram_url || $user->facebook_url)
                                                    <div class="social-links">
                                                        @if($user->instagram_url)
                                                            <a href="{{ $user->instagram_url }}" target="_blank" class="text-danger text-decoration-none" title="Instagram">
                                                                <i class="bi bi-instagram"></i>
                                                            </a>
                                                        @endif
                                                        @if($user->facebook_url)
                                                            <a href="{{ $user->facebook_url }}" target="_blank" class="text-primary text-decoration-none" title="Facebook">
                                                                <i class="bi bi-facebook"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @switch($user->role)
                                                @case('admin')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-shield-check me-1"></i>Administrador
                                                    </span>
                                                    @break
                                                @case('pending_artist')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-hourglass-split me-1"></i>Artista (Pendiente)
                                                    </span>
                                                    @break
                                                @case('artist')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-palette me-1"></i>Artista Aprobado
                                                    </span>
                                                    @break
                                                @case('user')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-person me-1"></i>Usuario
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                                <div class="text-muted">{{ $user->created_at->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->isArtist() || $user->isPendingArtist())
                                                @php
                                                    $artworkCount = $user->artworks()->count();
                                                    $approvedCount = $user->artworks()->where('status', 'approved')->count();
                                                    $pendingCount = $user->artworks()->where('status', 'pending')->count();
                                                    $rejectedCount = $user->artworks()->where('status', 'rejected')->count();
                                                @endphp
                                                <div class="text-center">
                                                    <div class="mb-1">
                                                        <span class="badge bg-primary">
                                                            <i class="bi bi-images me-1"></i>{{ $artworkCount }}
                                                        </span>
                                                    </div>
                                                    @if($artworkCount > 0)
                                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                            @if($approvedCount > 0)
                                                                <span class="badge bg-success small">
                                                                    {{ $approvedCount }} <i class="bi bi-check-circle"></i>
                                                                </span>
                                                            @endif
                                                            @if($pendingCount > 0)
                                                                <span class="badge bg-warning small">
                                                                    {{ $pendingCount }} <i class="bi bi-clock"></i>
                                                                </span>
                                                            @endif
                                                            @if($rejectedCount > 0)
                                                                <span class="badge bg-danger small">
                                                                    {{ $rejectedCount }} <i class="bi bi-x-circle"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <small class="text-muted">Sin obras</small>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <span class="text-muted small">
                                                        <i class="bi bi-dash-circle"></i> N/A
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Ver Usuario -->
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#showUserModal-{{ $user->id }}" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                <!-- Editar Usuario -->
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                
                                                <!-- Eliminar Usuario -->
                                                @if($user->id !== Auth::id())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal-{{ $user->id }}" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Modal Ver Usuario -->
                                            <div class="modal fade" id="showUserModal-{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-person-badge me-2"></i>
                                                                Detalles de {{ $user->username }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-4 text-center mb-3">
                                                                    @if($user->profile_picture)
                                                                        <img src="{{ $user->profile_picture }}" alt="Avatar" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                                                    @else
                                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                                                            <span class="fs-1">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                                                                        </div>
                                                                    @endif
                                                                    <h5 class="mt-2">{{ $user->username }}</h5>
                                                                    @switch($user->role)
                                                                        @case('admin')
                                                                            <span class="badge bg-danger">Administrador</span>
                                                                            @break
                                                                        @case('pending_artist')
                                                                            <span class="badge bg-warning">Artista (Pendiente)</span>
                                                                            @break
                                                                        @case('artist')
                                                                            <span class="badge bg-success">Artista Aprobado</span>
                                                                            @break
                                                                        @case('user')
                                                                            <span class="badge bg-info">Usuario</span>
                                                                            @break
                                                                    @endswitch
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <div class="user-info-grid">
                                                                        <div class="user-info-item">
                                                                            <strong>ID:</strong><br>
                                                                            {{ $user->id }}
                                                                        </div>
                                                                        <div class="user-info-item">
                                                                            <strong>Nombre completo:</strong><br>
                                                                            {{ $user->first_name }} {{ $user->last_name }}
                                                                        </div>
                                                                        <div class="user-info-item">
                                                                            <strong>Email:</strong><br>
                                                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                                        </div>
                                                                        @if($user->tlf)
                                                                            <div class="user-info-item">
                                                                                <strong>Teléfono:</strong><br>
                                                                                <a href="tel:{{ $user->tlf }}" class="phone-number">{{ $user->tlf }}</a>
                                                                            </div>
                                                                        @endif
                                                                        @if($user->birthday)
                                                                            <div class="user-info-item">
                                                                                <strong>Fecha de nacimiento:</strong><br>
                                                                                {{ $user->birthday->format('d/m/Y') }} ({{ $user->birthday->age }} años)
                                                                            </div>
                                                                        @endif
                                                                        @if($user->gender)
                                                                            <div class="user-info-item">
                                                                                <strong>Género:</strong><br>
                                                                                {{ $user->gender }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="user-info-item">
                                                                            <strong>Fecha de registro:</strong><br>
                                                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                                                        </div>
                                                                        <div class="user-info-item">
                                                                            <strong>Estado de cuenta:</strong><br>
                                                                            @if($user->is_active ?? true)
                                                                                <span class="badge bg-success">Activa</span>
                                                                            @else
                                                                                <span class="badge bg-danger">Inactiva</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    @if($user->description)
                                                                        <div class="mt-3">
                                                                            <strong>Descripción:</strong>
                                                                            <p class="mt-2">{{ $user->description }}</p>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($user->instagram_url || $user->facebook_url)
                                                                        <div class="mt-3">
                                                                            <strong>Redes sociales:</strong>
                                                                            <div class="mt-2">
                                                                                @if($user->instagram_url)
                                                                                    <a href="{{ $user->instagram_url }}" target="_blank" class="btn btn-sm btn-outline-danger me-2">
                                                                                        <i class="bi bi-instagram me-1"></i>Instagram
                                                                                    </a>
                                                                                @endif
                                                                                @if($user->facebook_url)
                                                                                    <a href="{{ $user->facebook_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                                        <i class="bi bi-facebook me-1"></i>Facebook
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($user->isArtist() || $user->isPendingArtist())
                                                                        <div class="mt-3">
                                                                            <strong>Estadísticas de obras:</strong>
                                                                            <div class="mt-2">
                                                                                <span class="badge bg-primary me-1">{{ $user->artworks()->count() }} total</span>
                                                                                <span class="badge bg-success me-1">{{ $user->artworks()->where('status', 'approved')->count() }} aprobadas</span>
                                                                                <span class="badge bg-warning me-1">{{ $user->artworks()->where('status', 'pending')->count() }} pendientes</span>
                                                                                <span class="badge bg-danger">{{ $user->artworks()->where('status', 'rejected')->count() }} rechazadas</span>
                                                                            </div>
                                                                        </div>

                                                                        @if($user->artworks()->count() > 0)
                                                                            <div class="mt-3">
                                                                                <strong>Obras recientes:</strong>
                                                                                <div class="row mt-2">
                                                                                    @foreach($user->artworks()->latest()->take(6)->get() as $artwork)
                                                                                        <div class="col-md-4 mb-2">
                                                                                            <div class="card h-100">
                                                                                                @if($artwork->image_path && Storage::disk('public')->exists($artwork->image_path))
                                                                                                    <img src="{{ Storage::url($artwork->image_path) }}" 
                                                                                                         class="card-img-top" 
                                                                                                         alt="{{ $artwork->title }}"
                                                                                                         style="height: 120px; object-fit: cover; cursor: pointer;"
                                                                                                         onclick="showArtworkModal('{{ $artwork->id }}', '{{ Storage::url($artwork->image_path) }}', '{{ $artwork->title }}', '{{ $artwork->description }}', '{{ ucfirst($artwork->status) }}', '{{ $artwork->created_at->format('d/m/Y H:i') }}')">
                                                                                                @else
                                                                                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 120px;">
                                                                                                        <i class="bi bi-image fs-3 text-muted"></i>
                                                                                                    </div>
                                                                                                @endif
                                                                                                <div class="card-body p-2">
                                                                                                    <h6 class="card-title mb-1 small">{{ Str::limit($artwork->title, 20) }}</h6>
                                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                                        <span class="badge 
                                                                                                            {{ $artwork->status === 'approved' ? 'bg-success' : ($artwork->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                                                                            {{ ucfirst($artwork->status) }}
                                                                                                        </span>
                                                                                                        <small class="text-muted">{{ $artwork->created_at->format('d/m') }}</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                                @if($user->artworks()->count() > 6)
                                                                                    <div class="text-center mt-2">
                                                                                        <small class="text-muted">Y {{ $user->artworks()->count() - 6 }} obras más...</small>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                                                                <i class="bi bi-pencil me-1"></i>Editar Usuario
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Editar Usuario -->
                                            <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.users.edit', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="bi bi-pencil me-2"></i>Editar Usuario: {{ $user->username }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nombre de Usuario</label>
                                                                            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Email</label>
                                                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nombre</label>
                                                                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Apellido</label>
                                                                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Teléfono</label>
                                                                            <input type="tel" name="tlf" class="form-control" value="{{ $user->tlf }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Rol</label>
                                                                            <select name="role" class="form-select" required>
                                                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Usuario</option>
                                                                                <option value="pending_artist" {{ $user->role == 'pending_artist' ? 'selected' : '' }}>Artista (Pendiente)</option>
                                                                                <option value="artist" {{ $user->role == 'artist' ? 'selected' : '' }}>Artista Aprobado</option>
                                                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Fecha de Nacimiento</label>
                                                                            <input type="date" name="birthday" class="form-control" value="{{ $user->birthday ? $user->birthday->format('Y-m-d') : '' }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Género</label>
                                                                            <input type="text" name="gender" class="form-control" value="{{ $user->gender }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nueva Contraseña (opcional)</label>
                                                                            <input type="password" name="password" class="form-control">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Confirmar Nueva Contraseña</label>
                                                                            <input type="password" name="password_confirmation" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Descripción</label>
                                                                    <textarea name="description" class="form-control" rows="3">{{ $user->description }}</textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">URL de Instagram</label>
                                                                            <input type="url" name="instagram_url" class="form-control" value="{{ $user->instagram_url }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">URL de Facebook</label>
                                                                            <input type="url" name="facebook_url" class="form-control" value="{{ $user->facebook_url }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Eliminar Usuario -->
                                            @if($user->id !== Auth::id())
                                                <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">
                                                                        <i class="bi bi-trash me-2"></i>Eliminar Usuario
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="alert alert-warning">
                                                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                                                        Esta acción eliminará permanentemente al usuario y todos sus datos asociados.
                                                                    </div>
                                                                    <p>¿Estás seguro de que deseas eliminar al usuario <strong>{{ $user->username }}</strong>?</p>
                                                                    <p class="text-danger small">Esta acción no se puede deshacer.</p>
                                                                    
                                                                    @if($user->isArtist() || $user->isPendingArtist())
                                                                        <div class="alert alert-info">
                                                                            <strong>Nota:</strong> Este usuario tiene {{ $user->artworks()->count() }} obras que también serán eliminadas.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="bi bi-trash me-1"></i>Eliminar Usuario
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <img id="artworkModalImage" src="" alt="" class="modal-artwork-preview mb-3"
                    onerror="this.style.display='none'; document.getElementById('artworkModalPlaceholder').style.display='flex';">
                <div id="artworkModalPlaceholder" class="artwork-placeholder mx-auto" style="display: none; width: 200px; height: 200px;">
                    <i class="bi bi-image fs-1 text-muted"></i>
                </div>
                <div id="artworkModalInfo" class="mt-3 text-start">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Título:</strong> <span id="artworkModalTitleText"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong> <span id="artworkModalStatus"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Fecha de creación:</strong> <span id="artworkModalDate"></span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Descripción:</strong>
                        <p id="artworkModalDescription" class="mt-1"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUsers');
        const roleFilter = document.getElementById('filterRole');
        const tableBody = document.querySelector('#usersTable tbody');
        const rows = tableBody.querySelectorAll('tr');

        // Función de filtrado
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRole = roleFilter.value;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const userRole = row.getAttribute('data-role');
                
                const matchesSearch = searchData.includes(searchTerm);
                const matchesRole = selectedRole === '' || userRole === selectedRole;
                
                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Actualizar contador
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            const counter = document.querySelector('.badge.bg-primary.fs-6');
            if (counter) {
                counter.textContent = `${visibleRows.length} usuarios`;
            }
        }

        // Event listeners
        searchInput.addEventListener('input', filterTable);
        roleFilter.addEventListener('change', filterTable);

        // Función para copiar teléfono al portapapeles
        function copyPhone(phone) {
            navigator.clipboard.writeText(phone).then(function() {
                showNotification('Número de teléfono copiado al portapapeles', 'success');
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
            });
        }

        // Agregar eventos de clic derecho a los números de teléfono para copiar
        const phoneLinks = document.querySelectorAll('.phone-number');
        phoneLinks.forEach(link => {
            link.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                const phone = this.textContent.trim();
                copyPhone(phone);
            });
        });

        // Función para mostrar modal de obra
        function showArtworkModal(artworkId, imageSrc, title, description, status, date) {
            const modal = document.getElementById('artworkModal');
            const modalImage = document.getElementById('artworkModalImage');
            const modalTitle = document.getElementById('artworkModalTitle');
            const modalTitleText = document.getElementById('artworkModalTitleText');
            const modalDescription = document.getElementById('artworkModalDescription');
            const modalStatus = document.getElementById('artworkModalStatus');
            const modalDate = document.getElementById('artworkModalDate');
            const modalPlaceholder = document.getElementById('artworkModalPlaceholder');

            // Actualizar contenido del modal
            modalImage.src = imageSrc;
            modalImage.alt = title;
            modalTitle.textContent = title || 'Vista de obra';
            modalTitleText.textContent = title || 'Sin título';
            modalDescription.textContent = description || 'Sin descripción';
            modalDate.textContent = date;
            
            // Configurar badge de estado
            modalStatus.innerHTML = `<span class="badge ${
                status === 'Approved' ? 'bg-success' : 
                status === 'Pending' ? 'bg-warning' : 'bg-danger'
            }">${status}</span>`;

            modalImage.style.display = 'block';
            modalPlaceholder.style.display = 'none';

            // Mostrar el modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }

        // Hacer la función global para uso en onclick
        window.showArtworkModal = showArtworkModal;
        function showNotification(message, type = 'info') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-info';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-info-circle';
            
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                <i class="bi ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alert);

            // Auto-remover después de 4 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 4000);
        }

        // Confirmación antes de eliminar usuario
        const deleteButtons = document.querySelectorAll('button[data-bs-target*="deleteUserModal"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.querySelector(this.getAttribute('data-bs-target'));
                const form = modal.querySelector('form');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (confirm('¿Estás completamente seguro? Esta acción no se puede deshacer.')) {
                        this.submit();
                    }
                });
            });
        });

        // Mejorar la experiencia con tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Validación en tiempo real para URLs de redes sociales en formularios de edición
        const instagramInputs = document.querySelectorAll('input[name="instagram_url"]');
        const facebookInputs = document.querySelectorAll('input[name="facebook_url"]');

        instagramInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateSocialUrl(this, 'instagram');
            });
        });

        facebookInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateSocialUrl(this, 'facebook');
            });
        });

        function validateSocialUrl(input, platform) {
            const value = input.value.trim();
            if (!value) {
                input.classList.remove('is-invalid', 'is-valid');
                return;
            }

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

        // Validación de teléfono en tiempo real
        const phoneInputs = document.querySelectorAll('input[name="tlf"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.trim();
                if (!value) {
                    this.classList.remove('is-invalid', 'is-valid');
                    return;
                }

                // Validación básica de teléfono
                const phoneRegex = /^(\+?[1-9]\d{1,14}|[0-9\s\-\(\)\.]{7,20})$/;
                
                if (phoneRegex.test(value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });

        // Funcionalidad de búsqueda con Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterTable();
            }
        });

        // Limpiar búsqueda con Escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                filterTable();
            }
        });

        // Focus en el campo de búsqueda con Ctrl+F
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    });
</script>
@endsection