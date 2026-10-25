<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre',
        'codigo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class, 'area_id');
    }

    public function docentes(): HasMany
    {
        return $this->hasMany(Docente::class, 'area_id');
    }
}
