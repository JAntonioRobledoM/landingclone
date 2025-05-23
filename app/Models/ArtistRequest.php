<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArtistRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'motivation',
        'artwork_image_path',
        'artwork_title',
        'artwork_description',
        'artwork_original_filename',
        'artwork_mime_type',
        'artwork_file_size',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'artwork_file_size' => 'integer',
    ];

    /**
     * Obtener el usuario asociado con la solicitud.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el administrador que aprobó/rechazó la solicitud.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtener la URL completa de la imagen de la obra.
     */
    public function getArtworkImageUrlAttribute()
    {
        if ($this->artwork_image_path) {
            // Verificar si el archivo existe antes de devolver la URL
            if (Storage::disk('public')->exists($this->artwork_image_path)) {
                return Storage::url($this->artwork_image_path);
            }
            
            // Si no existe el archivo, devolver una imagen por defecto o null
            return asset('images/no-image.png'); // o return null;
        }
        return null;
    }

    /**
     * Obtener el tamaño del archivo formateado.
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->artwork_file_size) {
            return 'N/A';
        }

        $bytes = $this->artwork_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Verificar si tiene obra de muestra.
     */
    public function hasArtwork()
    {
        return !empty($this->artwork_image_path) && !empty($this->artwork_title);
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getUserFullNameAttribute()
    {
        return $this->user ? $this->user->first_name . ' ' . $this->user->last_name : 'Usuario eliminado';
    }

    /**
     * Obtener el username del usuario
     */
    public function getUsernameAttribute()
    {
        return $this->user ? $this->user->username : 'N/A';
    }

    /**
     * Obtener el email del usuario
     */
    public function getUserEmailAttribute()
    {
        return $this->user ? $this->user->email : 'N/A';
    }

    /**
     * Scope para solicitudes pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para solicitudes aprobadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope para solicitudes rechazadas
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}