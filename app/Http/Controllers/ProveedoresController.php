<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditorias;

class ProveedoresController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $proveedores = Proveedores::all();
        if ($proveedores->isEmpty()) {
            return response()->noContent();
        }

        // Registrar la auditoría
        $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de proveedores', 'Total Proveedores: ' . $proveedores->count());

        return response()->json(['message' => 'consulta exitosa', 'data' => $proveedores]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $existingProveedor = Proveedores::where('documento_identificacion', $request->documento_identificacion)->exists();
        if ($existingProveedor) {
            return response()->json(['message' => 'Ya existe un proveedor con ese documento de identificacion'], 400);
        }

        $existingEmail = Proveedores::where('email', $request->email)->exists();
        if ($existingEmail) {
            return response()->json(['message' => 'Ya existe un proveedor con ese email'], 400);
        }

        $proveedor = $this->obtenerDatos($request);
        $proveedor->save();

        // Registrar la auditoría
        $this->registrarAuditoria($user->email, 'Post', 'Compras', 'Registrar un Proveedor', 'La identificación del proveedor es: ' . $request->documento_identificacion);

        return response()->json(['message' => 'Proveedor Creado Exitosamente', 'data' => $proveedor], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $proveedores = Proveedores::find($id);
        if ($proveedores == null) {
            return response()->json(['message' => 'Proveedor no encontrado'], 400);
        }

        // Registrar la auditoría
        $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de un Proveedor', 'La identificación del proveedor es: ' . $proveedores->documento_identificacion);

        return response()->json(['message' => 'Consulta existosa', 'data' => $proveedores]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedores $proveedores)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $proveedor = Proveedores::find($id);
        if ($proveedor == null) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }
        $proveedor->delete();
        // Registrar la auditoría
        $this->registrarAuditoria($user->email, "Update", 'Compras', 'Actualizar un Proveedor', 'La identificación del proveedor es: ' . $proveedor->documento_identificacion);

        return response()->json(['message' => 'Proveedor eliminado exitosamente', 'data' => $proveedor]);
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