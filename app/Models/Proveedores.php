<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;
    protected $fillable = [
        'documento_identificacion',
        'nombre',
        'ciudad',
        'tipo_proveedor',
        'direccion',
        'telefono',
        'email',
        'estado',
    ];
    public $timestamps = false;
}