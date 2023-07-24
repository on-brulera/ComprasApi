<?php

namespace App\Http\Controllers\ModuloCompras;

use App\Http\Controllers\Controller;
use App\Http\Traits\AuditoriaTrait;
use App\Http\Traits\TokenTrait;
use App\Models\DetalleFacturas;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class DetalleFacturasController extends Controller
{

    use AuditoriaTrait;
    use TokenTrait;

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
}