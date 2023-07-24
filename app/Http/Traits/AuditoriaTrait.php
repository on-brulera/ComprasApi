<?php
namespace App\Http\Traits;

use App\Models\Auditorias;

trait AuditoriaTrait
{
    /**
     * Registrar acciones en la tabla de auditoría.
     */
    public function registrarAuditoria($usuario, $accion, $modulo, $funcionalidad, $observacion)
    {
        Auditorias::create([
            'aud_usuario' => $usuario,
            'aud_fecha' => now(),
            'aud_accion' => $accion,
            'aud_modulo' => $modulo,
            'aud_funcionalidad' => $funcionalidad,
            'aud_observacion' => $observacion,
        ]);
    }
}