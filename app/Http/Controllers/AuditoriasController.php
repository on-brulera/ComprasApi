<?php

namespace App\Http\Controllers;

use App\Models\Auditorias;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriasController extends Controller
{

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Auditorias $auditorias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auditorias $auditorias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auditorias $auditorias)
    {
        //
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

    private function verificarToken(Request $request)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            throw new AuthorizationException('Falta el token');
        }
        $user = Auth::guard('api')->user();
        if (!$user) {
            throw new AuthorizationException('Token inválido o caducado');
        }
        return $user;
    }
}