<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFacturas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'factura_id',
        'producto_id',
        'cantidad',
        'subtotal',
        'total',
    ];

    public function factura()
    {
        return $this->belongsTo(Facturas::class, 'factura_id');
    }
}