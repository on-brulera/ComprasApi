<?php

namespace App\Http\Controllers\ModuloCompras;

use App\Http\Controllers\Controller;
use App\Http\Traits\AuditoriaTrait;
use App\Http\Traits\TokenTrait;
use App\Models\Proveedores;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
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
            $proveedores = Proveedores::all();
            if ($proveedores->isEmpty()) {
                return response()->noContent();
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de proveedores', 'Total Proveedores: ' . $proveedores->count());
            return response()->json(['message' => 'consulta exitosa', 'data' => $proveedores]);
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
                'documento_identificacion' => 'required|string|unique:proveedores,documento_identificacion',
                'nombre' => 'required|string',
                'ciudad' => 'required|string',
                'tipo_proveedor' => 'required|string|in:Contado,Crédito',
                'direccion' => 'nullable|string',
                'telefono' => 'required|string',
                'email' => 'required|string|unique:proveedores,email',
                'estado' => 'required|string|in:Activo,Inactivo',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            $proveedor = $this->obtenerDatos($request);
            $proveedor->save();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, 'Post', 'Compras', 'Registrar un Proveedor', 'La identificación del proveedor es: ' . $request->documento_identificacion);
            return response()->json(['message' => 'Proveedor Creado Exitosamente', 'data' => $proveedor], 201);
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
            $proveedores = Proveedores::find($id);
            if ($proveedores == null) {
                return response()->json(['message' => 'Proveedor no encontrado'], 400);
            }
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de un Proveedor', 'La identificación del proveedor es: ' . $proveedores->documento_identificacion);
            return response()->json(['message' => 'Consulta existosa', 'data' => $proveedores]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedores $proveedores)
    {
        try {
            $user = $this->verificarToken($request);
            $documentoIdentificacion = $request->documento_identificacion;
            if ($documentoIdentificacion !== $proveedores->documento_identificacion) {
                $existeProveedor = Proveedores::where('documento_identificacion', $documentoIdentificacion)->exists();
                if ($existeProveedor) {
                    return response()->json(['message' => 'Ya existe un proveedor con ese documento de identificacion'], 400);
                }
            }

            $email = $request->email;
            if ($email !== $proveedores->email) {
                $existeEmail = Proveedores::where('email', $email)->exists();
                if ($existeEmail) {
                    return response()->json(['message' => 'Ya existe un proveedor con ese email'], 400);
                }
            }

            $proveedores = $this->obtenerDatos($request, $proveedores);
            $updated = $proveedores->save();
            if ($updated) {
                $statusCode = $proveedores->wasRecentlyCreated ? 201 : 200;
                // Registrar la auditoría
                $this->registrarAuditoria($user->email, "Update", 'Compras', 'Actualizar un Proveedor', 'La identificación del proveedor es: ' . $request->documento_identificacion);
                return response(['message' => 'Proveedor Actualizado Exitosamente', 'data' => $proveedores], $statusCode);
            } else {
                return response()->json(['message' => 'No se pudo actualizar el proveedor'], 409);
            }
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
            $proveedor = Proveedores::find($id);
            if ($proveedor == null) {
                return response()->json(['message' => 'Proveedor no encontrado'], 404);
            }
            $tieneFacturas = $proveedor->facturas()->exists();
            if ($tieneFacturas) {
                return response()->json(['message' => 'No se puede eliminar el proveedor porque tiene facturas asociadas'], 409);
            }
            $proveedor->delete();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Update", 'Compras', 'Actualizar un Proveedor', 'La identificación del proveedor es: ' . $proveedor->documento_identificacion);
            return response()->json(['message' => 'Proveedor eliminado exitosamente', 'data' => $proveedor]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function cambiarEstadoProveedor(Request $request, $id)
    {
        try {
            $user = $this->verificarToken($request);
            $proveedor = Proveedores::find($id);
            if ($proveedor == null) {
                return response()->json(['message' => 'Proveedor no encontrado'], 404);
            }
            $estadoActual = $proveedor->estado;
            $estadoNuevo = ($estadoActual === 'Activo') ? 'Inactivo' : 'Activo';
            $proveedor->estado = $estadoNuevo;
            $proveedor->save();
            // Registrar la auditoría
            $this->registrarAuditoria($user->email, "Post", 'Compras', 'Cambiar Estado Proveedor', 'Id Proveedor: ' . $id . '. : ' . $proveedor->estado);
            return response()->json(['message' => 'Campo actualizado correctamente']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    private function obtenerDatos(Request $request, Proveedores $proveedores = null): Proveedores
    {
        if ($proveedores == null) {
            $proveedores = new Proveedores;
        }
        $proveedores->documento_identificacion = $request->documento_identificacion;
        $proveedores->nombre = $request->nombre;
        $proveedores->ciudad = $request->ciudad;
        $proveedores->tipo_proveedor = $request->tipo_proveedor;
        $proveedores->direccion = $request->direccion;
        $proveedores->telefono = $request->telefono;
        $proveedores->email = $request->email;
        $proveedores->estado = $request->estado;
        return $proveedores;
    }
}