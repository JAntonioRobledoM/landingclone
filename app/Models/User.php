<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'birthday',
        'gender',
        'profile_picture',
        'banner_url',
        'description',
        'id_number',
        'tlf',
        'instagram_url',
        'facebook_url',
        'profile_views',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_active' => 'boolean',
        'profile_views' => 'integer',
    ];

    /**
     * Relación con las obras de arte
     */
    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * Obtener la solicitud de artista del usuario.
     */
    public function artistRequest()
    {
        return $this->hasOne(ArtistRequest::class);
    }

    /**
     * Verificar si el usuario es artista
     */
    public function isArtist()
    {
        return $this->role === 'artist';
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario tiene solicitud de artista pendiente
     */
    public function isPendingArtist()
    {
        return $this->role === 'pending_artist';
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Obtener la URL de la imagen de perfil o un placeholder
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return $this->profile_picture;
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Obtener la red social activa del usuario
     */
    public function getActiveSocialMediaAttribute()
    {
        if ($this->instagram_url) {
            return [
                'type' => 'instagram',
                'url' => $this->instagram_url,
                'icon' => 'fab fa-instagram',
                'color' => 'text-danger',
                'username' => $this->instagram_username
            ];
        }
        
        if ($this->facebook_url) {
            return [
                'type' => 'facebook',
                'url' => $this->facebook_url,
                'icon' => 'fab fa-facebook',
                'color' => 'text-primary',
                'username' => $this->facebook_username
            ];
        }
        
        return null;
    }

    /**
     * Verificar si el usuario tiene alguna red social configurada
     */
    public function hasSocialMedia()
    {
        return !empty($this->instagram_url) || !empty($this->facebook_url);
    }

    /**
     * Obtener el nombre de usuario de Instagram (sin la URL completa)
     */
    public function getInstagramUsernameAttribute()
    {
        if (!$this->instagram_url) {
            return null;
        }
        
        $path = parse_url($this->instagram_url, PHP_URL_PATH);
        return trim($path, '/');
    }

    /**
     * Obtener el nombre de usuario de Facebook (sin la URL completa)
     */
    public function getFacebookUsernameAttribute()
    {
        if (!$this->facebook_url) {
            return null;
        }
        
        $path = parse_url($this->facebook_url, PHP_URL_PATH);
        return trim($path, '/');
    }

    /**
     * Scope para obtener solo artistas activos
     */
    public function scopeActiveArtists($query)
    {
        return $query->where('role', 'artist')->where('is_active', true);
    }

    /**
     * Scope para obtener usuarios con obras aprobadas
     */
    public function scopeWithApprovedArtworks($query)
    {
        return $query->whereHas('artworks', function($q) {
            $q->where('status', 'approved');
        });
    }

    /**
     * Scope para obtener solicitudes de artistas pendientes
     */
    public function scopePendingArtists($query)
    {
        return $query->where('role', 'pending_artist');
    }

    /**
     * Obtener el tipo de red social configurada
     */
    public function getSocialMediaTypeAttribute()
    {
        if ($this->instagram_url) {
            return 'instagram';
        } elseif ($this->facebook_url) {
            return 'facebook';
        }
        
        return 'none';
    }
}