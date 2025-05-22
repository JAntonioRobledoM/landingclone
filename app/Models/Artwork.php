<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_portfolio_piece',
        'is_featured',
        'status'
    ];

    protected $casts = [
        'is_portfolio_piece' => 'boolean',
        'is_featured' => 'boolean',
        'file_size' => 'integer'
    ];

    /**
     * Relación con el usuario (artista)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para obtener la URL completa de la imagen
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    /**
     * Scope para obtener solo obras aprobadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope para obtener obras destacadas
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para obtener obras del portafolio
     */
    public function scopePortfolio($query)
    {
        return $query->where('is_portfolio_piece', true);
    }

    /**
     * Método para formatear el tamaño del archivo
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}