<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas autenticadas para todos los usuarios
Route::middleware('auth')->group(function () {
    // Dashboard principal - ahora solo usa HomeController
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas específicas para artistas
    Route::middleware('role:artist')->prefix('artist')->name('artist.')->group(function () {
        Route::get('/', [ArtistController::class, 'index'])->name('index');
        
        // Gestión de perfil
        Route::get('/perfil', [ArtistController::class, 'perfil'])->name('perfil');
        Route::get('/perfil/editar', [ArtistController::class, 'editarPerfil'])->name('editar-perfil');
        Route::put('/perfil', [ArtistController::class, 'actualizarPerfil'])->name('actualizar-perfil');
        
        // Gestión de obras
        Route::get('/obras', [ArtistController::class, 'misObras'])->name('mis-obras');
        Route::get('/obras/crear', [ArtistController::class, 'crearObra'])->name('crear-obra');
        Route::post('/obras', [ArtistController::class, 'guardarObra'])->name('guardar-obra');
        Route::get('/obras/{id}', [ArtistController::class, 'verObra'])->name('ver-obra');
        Route::get('/obras/{id}/editar', [ArtistController::class, 'editarObra'])->name('editar-obra');
        Route::put('/obras/{id}', [ArtistController::class, 'actualizarObra'])->name('actualizar-obra');
        Route::delete('/obras/{id}', [ArtistController::class, 'eliminarObra'])->name('eliminar-obra');
        
        // Mantener rutas antiguas para compatibilidad
        Route::get('/nueva-obra', [ArtistController::class, 'crearObra'])->name('nueva-obra');
        Route::post('/subir-obra', [ArtistController::class, 'guardarObra'])->name('subir-obra');
        Route::post('/actualizar-perfil', [ArtistController::class, 'actualizarPerfil'])->name('actualizar-perfil');
    });

    // Rutas específicas para usuarios normales
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/favoritos', [UserController::class, 'favoritos'])->name('favoritos');
        Route::get('/explorar', [UserController::class, 'explorar'])->name('explorar');
        Route::get('/artista/{username}', [UserController::class, 'verArtista'])->name('ver-artista');
        Route::post('/favorito/agregar/{obra_id}', [UserController::class, 'agregarFavorito'])->name('agregar-favorito');
        Route::delete('/favorito/eliminar/{obra_id}', [UserController::class, 'eliminarFavorito'])->name('eliminar-favorito');
        Route::get('/perfil', [UserController::class, 'perfil'])->name('perfil');
        Route::get('/editar-perfil', [UserController::class, 'editarPerfil'])->name('editar-perfil');
        Route::post('/actualizar-perfil', [UserController::class, 'actualizarPerfil'])->name('actualizar-perfil');
    });
});

// Rutas de administración
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestión de usuarios
    Route::get('/users', [AdminController::class, 'listUsers'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{id}', [AdminController::class, 'editUser'])->name('users.edit');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Gestión de solicitudes de artistas
    Route::get('/artist-requests', [AdminController::class, 'listArtistRequests'])->name('artist-requests');
    Route::get('/artist-requests/{id}', [AdminController::class, 'showArtistRequest'])->name('artist-requests.show');
    Route::post('/artist-requests/{id}/approve', [AdminController::class, 'approveArtist'])->name('artist-requests.approve');
    Route::post('/artist-requests/{id}/reject', [AdminController::class, 'rejectArtist'])->name('artist-requests.reject');
    
    // Mantener rutas antiguas para compatibilidad
    Route::post('/artist/{id}/approve', [AdminController::class, 'approveArtist'])->name('approve.artist');
    Route::post('/artist/{id}/reject', [AdminController::class, 'rejectArtist'])->name('reject.artist');
    
    // Mantener rutas antiguas para compatibilidad
    Route::get('/users/{id}/show', [AdminController::class, 'showUser'])->name('users.show.old');
    Route::put('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit.old');
    Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete.old');
});