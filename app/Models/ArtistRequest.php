<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'motivation',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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
}