@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Everlasting Art')

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
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action active">Usuarios</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Usuarios</h4>
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
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha de Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @switch($user->role)
                                                @case('admin')
                                                    <span class="badge bg-danger">Administrador</span>
                                                    @break
                                                @case('artist')
                                                    <span class="badge bg-warning">Artista (Pendiente)</span>
                                                    @break
                                                @case('artist_approved')
                                                    <span class="badge bg-success">Artista Aprobado</span>
                                                    @break
                                                @case('user')
                                                    <span class="badge bg-info">Usuario</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Show User Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#showUserModal-{{ $user->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                <!-- Edit User Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                
                                                <!-- Delete User Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal-{{ $user->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Show User Modal -->
                                            <div class="modal fade" id="showUserModal-{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Detalles de Usuario</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>ID:</strong> {{ $user->id }}</p>
                                                            <p><strong>Nombre de Usuario:</strong> {{ $user->username }}</p>
                                                            <p><strong>Email:</strong> {{ $user->email }}</p>
                                                            <p><strong>Rol:</strong> {{ $user->role }}</p>
                                                            <p><strong>Fecha de Registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit User Modal -->
                                            <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.users.edit', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Editar Usuario</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nombre de Usuario</label>
                                                                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Email</label>
                                                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Rol</label>
                                                                    <select name="role" class="form-select" required>
                                                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Usuario</option>
                                                                        <option value="artist" {{ $user->role == 'artist' ? 'selected' : '' }}>Artista (Pendiente)</option>
                                                                        <option value="artist_approved" {{ $user->role == 'artist_approved' ? 'selected' : '' }}>Artista Aprobado</option>
                                                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                                                                    </select>
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
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete User Modal -->
                                            <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Eliminar Usuario</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro de que deseas eliminar al usuario <strong>{{ $user->username }}</strong>?</p>
                                                                <p class="text-danger">Esta acción no se puede deshacer.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection