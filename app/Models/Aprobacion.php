<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprobacion extends Model
{
    protected $table = 'aprobaciones';

    protected $fillable = [
        'solicitud_id',
        'usuario_id',
        'rol_codigo',
        'etapa',
        'decision',
        'observacion',
        'estado_anterior',
        'estado_nuevo',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
