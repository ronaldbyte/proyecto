<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ConfiguracionCorreo extends Model
{
    protected $table = 'configuracion_correo';

    protected $fillable = [
        'activo',
        'host',
        'puerto',
        'usuario',
        'contrasena',
        'nombre_remitente',
        'ultimo_error',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'puerto' => 'integer',
        ];
    }

    public static function actual(): self
    {
        return static::query()->first() ?? static::create([
            'activo' => false,
            'host' => 'smtp.gmail.com',
            'puerto' => 587,
            'nombre_remitente' => 'Licencias UNSXX',
        ]);
    }

    public function setContrasenaAttribute(?string $valor): void
    {
        if ($valor === null || $valor === '') {
            return;
        }

        $this->attributes['contrasena'] = Crypt::encryptString($valor);
    }

    public function contrasenaPlana(): ?string
    {
        if (empty($this->attributes['contrasena'] ?? null)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['contrasena']);
        } catch (\Throwable) {
            return null;
        }
    }

    public function estaLista(): bool
    {
        return $this->activo
            && filled($this->usuario)
            && filled($this->contrasenaPlana());
    }
}
