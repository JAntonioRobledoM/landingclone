<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artwork;
use App\Models\ArtistRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Inicializar pendingRequests como colección vacía para todos los roles
        $pendingRequests = collect();
        $stats = [];
        
        // Si es admin, obtener solicitudes pendientes
        if ($user->role === 'admin') {
            $pendingRequests = ArtistRequest::where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('dashboard', compact('pendingRequests', 'stats'));
    }
}