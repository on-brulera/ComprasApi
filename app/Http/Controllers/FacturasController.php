<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditorias;

class FacturasController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $this->verificarToken($request);
            $facturasConDetalles = Facturas::with('detalles')->get();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Get", 'Compras', 'Obtener facturas', 'Número Total Facturas: ' . $facturasConDetalles->count());
            return response()->json(['message' => 'Consulta Exitosa', 'data' => $facturasConDetalles]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = $this->verificarToken($request);
            $validator = Validator::make($request->all(), [
                'proveedor_id' => 'required|exists:proveedores,id',
                'fecha_factura' => 'required|date',
                'tipo_pago' => 'required|in:Crédito,Contado',
                'fecha_vencimiento' => 'nullable|date',
                'total' => 'required|numeric',
                'estado' => 'required|in:Activo,Inactivo',
                'detalles.*.cantidad' => 'required|integer|min:0',
                'detalles.*.subtotal' => 'required|numeric',
                'detalles.*.total' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            // Crear la factura y guardarla en la base de datos
            $factura = new Facturas([
                'proveedor_id' => $request->proveedor_id,
                'fecha_factura' => $request->fecha_factura,
                'tipo_pago' => $request->tipo_pago,
                'fecha_vencimiento' => ($request->tipo_pago === 'Crédito') ? $request->fecha_vencimiento : null,
                'total' => $request->total,
                'estado' => $request->estado,
            ]);
            $factura->save();
            // Crear los detalles de la factura y guardarlos en la base de datos
            foreach ($request->detalles as $detalle) {
                $factura->detalles()->create([
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal'],
                    'total' => $detalle['total'],
                ]);
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Post", 'Compras', 'Crear facturas', 'Total Factura : ' . $request->total . '. Tipos Productos ' . count($request->detalles));
            return response()->json(['message' => 'Factura creada exitosamente', 'data' => $factura->load('detalles')], 201);
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
            $facturaConDetalles = Facturas::with('detalles')->find($id);
            if ($facturaConDetalles == null) {
                return response()->json(['message' => 'no existe una factura con ese id'], 400);
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Get", 'Compras', 'Obtener factura', 'Total Factura : ' . $facturaConDetalles->total . '. Tipos Productos ' . count($facturaConDetalles->detalles));
            return response()->json(['message' => 'Consulta exitosa', 'data' => $facturaConDetalles]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facturas $facturas)
    {
        try {
            $user = $this->verificarToken($request);
            $validator = Validator::make($request->all(), [
                'proveedor_id' => 'required|exists:proveedores,id',
                'fecha_factura' => 'required|date',
                'tipo_pago' => 'required|in:Crédito,Contado',
                'fecha_vencimiento' => 'nullable|date',
                'total' => 'required|numeric',
                'estado' => 'required|in:Activo,Inactivo',
                'detalles.*.cantidad' => 'required|integer',
                'detalles.*.subtotal' => 'required|numeric',
                'detalles.*.total' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Actualizar los datos de la factura
            $facturas->proveedor_id = $request->proveedor_id;
            $facturas->fecha_factura = $request->fecha_factura;
            $facturas->tipo_pago = $request->tipo_pago;
            $facturas->fecha_vencimiento = ($request->tipo_pago === 'Crédito') ? $request->fecha_vencimiento : null;
            $facturas->total = $request->total;
            $facturas->estado = $request->estado;
            $facturas->save();
            // Eliminar los detalles antiguos de la factura
            $facturas->detalles()->delete();
            // Crear los nuevos detalles de la factura y guardarlos en la base de datos
            foreach ($request->detalles as $detalle) {
                $facturas->detalles()->create([
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal'],
                    'total' => $detalle['total'],
                ]);
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Put", 'Compras', 'Actualizar factura', 'Total Factura : ' . $request->total . '. Tipos Productos ' . count($request->detalles));
            return response()->json(['message' => 'Factura actualizada exitosamente', 'data' => $facturas->load('detalles')]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $this->verificarToken($request);
            $factura = Facturas::find($id);
            if (!$factura) {
                return response()->json(['message' => 'Factura no encontrada'], 404);
            }
            $factura->detalles()->delete();
            $factura->delete();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Delete", 'Compras', 'Eliminar factura', 'Id Factura : ' . $id);
            return response()->json(['message' => 'Factura eliminada correctamente'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function cambiarEstadoFactura(Request $request, $id)
    {
        try {
            $user = $this->verificarToken($request);
            $factura = Facturas::find($id);
            if ($factura == null) {
                return response()->json(['message' => 'Factura no encontrada'], 404);
            }
            $estadoActual = $factura->estado;
            $estadoNuevo = ($estadoActual === 'Activo') ? 'Inactivo' : 'Activo';
            $factura->estado = $estadoNuevo;
            $factura->save();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Post", 'Compras', 'Cambiar Estado Factura', 'Id Factura : ' . $id . '. Factura estado: ' . $factura->estado);
            return response()->json(['message' => 'Campo actualizado correctamente']);
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