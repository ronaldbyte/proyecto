<?php

namespace App\Services;

use App\Models\Auditoria;
use App\Models\Usuario;

class ServicioAuditoria
{
    public function registrar(
        string $accion,
        string $entidad,
        int $entidadId,
        ?array $anteriores = null,
        ?array $nuevos = null,
        ?Usuario $usuario = null
    ): void {
        Auditoria::create([
            'usuario_id' => $usuario?->id ?? auth()->id(),
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'datos_anteriores' => $anteriores,
            'datos_nuevos' => $nuevos,
            'ip' => request()?->ip(),
        ]);
    }
}
