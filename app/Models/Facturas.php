<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'proveedor_id',
        'fecha_factura',
        'tipo_pago',
        'fecha_vencimiento',
        'total',
        'estado',
        'impreso',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleFacturas::class, 'factura_id');
    }
}