<?php

namespace App\Http\Controllers;

use App\Models\DetalleFacturas;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditorias;

class DetalleFacturasController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $this->verificarToken($request);
            $detallesConFactura = DetalleFacturas::with('factura')->get();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Get", 'Compras', 'Obtener Detalles Factura', 'Total de detalles: ' . $detallesConFactura->count());
            return response()->json(['message' => 'Consulta Exitosa', 'data' => $detallesConFactura]);
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
    public function show(Request $request, $id)
    {
        try {
            $user = $this->verificarToken($request);
            $detallesFactura = DetalleFacturas::with('factura')->where('producto_id', $id)->get();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Get", 'Compras', 'Obtener Detalles Factura', 'Id Producto: ' . $id . '. Total de detalles: '->$detallesFactura->count());
            return response()->json(['message' => 'Consulta exitosa', 'data' => $detallesFactura]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetalleFacturas $detalleFacturas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetalleFacturas $detalleFacturas)
    {
        //
    }

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