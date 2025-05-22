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
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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
            return Storage::url($this->artwork_image_path);
        }
        return null;
    }

    /**
     * Obtener el tamaño del archivo formateado.
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->artwork_file_size) {
            return null;
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
        return !empty($this->artwork_image_path);
    }
}