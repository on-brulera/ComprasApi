<?php

namespace App\Http\Controllers;

use App\Models\Auditorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriasController extends Controller
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

        $auditoria = Auditorias::all();
        if ($auditoria->isEmpty()) {
            return response()->noContent();
        }

        // Registrar la auditoría
        $this->registrarAuditoria($user->email, 'Get', 'Compras', 'Consulta de auditoria', 'Total Filas: ' . $auditoria->count());

        return response()->json(['message' => 'consulta exitosa', 'data' => $auditoria]);
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
}