<?php

namespace App\Http\Controllers;

use App\Models\DetalleFacturas;
use Illuminate\Http\Request;

class DetalleFacturasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detallesConFactura = DetalleFacturas::with('factura')->get();
        return response()->json(['message' => 'Consulta Exitosa', 'data' => $detallesConFactura]);
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
    public function show($id)
    {
        $detallesFactura = DetalleFacturas::with('factura')->where('producto_id', $id)->get();        
        return response()->json(['message' => 'Consulta exitosa', 'data' => $detallesFactura]);
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
}