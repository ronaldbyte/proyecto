<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'solicitud_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'leida_en',
    ];

    protected function casts(): array
    {
        return [
            'leida' => 'boolean',
            'leida_en' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function marcarLeida(): void
    {
        if ($this->leida) {
            return;
        }

        $this->leida = true;
        $this->leida_en = now();
        $this->save();
    }
}
