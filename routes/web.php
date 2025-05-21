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
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas específicas para artistas
    Route::middleware('role.artist')->prefix('artist')->group(function () {
        Route::get('/', [ArtistController::class, 'index'])->name('artist.index');
        Route::get('/obras', [ArtistController::class, 'obras'])->name('artist.obras');
        Route::get('/nueva-obra', [ArtistController::class, 'nuevaObra'])->name('artist.nueva-obra');
        Route::post('/subir-obra', [ArtistController::class, 'subirObra'])->name('artist.subir-obra');
        Route::get('/perfil', [ArtistController::class, 'perfil'])->name('artist.perfil');
        Route::get('/editar-perfil', [ArtistController::class, 'editarPerfil'])->name('artist.editar-perfil');
        Route::post('/actualizar-perfil', [ArtistController::class, 'actualizarPerfil'])->name('artist.actualizar-perfil');
        Route::get('/estadisticas', [ArtistController::class, 'estadisticas'])->name('artist.estadisticas');
    });

    // Rutas específicas para users
    Route::middleware('role.user')->prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/favoritos', [UserController::class, 'favoritos'])->name('user.favoritos');
        Route::get('/explorar', [UserController::class, 'explorar'])->name('user.explorar');
        Route::get('/artista/{username}', [UserController::class, 'verArtista'])->name('user.ver-artista');
        Route::post('/favorito/agregar/{obra_id}', [UserController::class, 'agregarFavorito'])->name('user.agregar-favorito');
        Route::delete('/favorito/eliminar/{obra_id}', [UserController::class, 'eliminarFavorito'])->name('user.eliminar-favorito');
        Route::get('/perfil', [UserController::class, 'perfil'])->name('user.perfil');
        Route::get('/editar-perfil', [UserController::class, 'editarPerfil'])->name('user.editar-perfil');
        Route::post('/actualizar-perfil', [UserController::class, 'actualizarPerfil'])->name('user.actualizar-perfil');
    });
});

// Rutas de administración
Route::middleware('auth')->group(function () {
    Route::post('/admin/artist/{id}/approve', [AdminController::class, 'approveArtist'])->name('admin.approve.artist');
    Route::post('/admin/artist/{id}/reject', [AdminController::class, 'rejectArtist'])->name('admin.reject.artist');
    Route::get('/admin/users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::get('/admin/users/{id}/show', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::put('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::delete('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});