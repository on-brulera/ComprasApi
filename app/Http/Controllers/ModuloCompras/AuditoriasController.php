<?php

namespace App\Http\Controllers\ModuloCompras;

use App\Http\Controllers\Controller;
use App\Http\Traits\TokenTrait;
use App\Models\Auditorias;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriasController extends Controller
{

    use TokenTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $this->verificarToken($request);
            $auditoria = Auditorias::all();
            if ($auditoria->isEmpty()) {
                return response()->noContent();
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de auditoria', 'Total Filas: ' . $auditoria->count());
            return response()->json(['message' => 'consulta exitosa', 'data' => $auditoria]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Registrar una entrada en la tabla de auditoría.
     */
    private function registrarAuditoria($usuario, $accion, $modulo, $funcionalidad, $observacion)
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