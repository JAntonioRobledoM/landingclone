<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckArtistRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        
        // Cambio de artist_approved a artist
        if (Auth::user()->role != 'artist') {
            return redirect('dashboard')->with('error', 'Acceso denegado. Esta sección es solo para artistas.');
        }
        
        return $next($request);
    }
}