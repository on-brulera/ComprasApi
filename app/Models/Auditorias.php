<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorias extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'aud_usuario',
        'aud_fecha',
        'aud_accion',
        'aud_modulo',
        'aud_funcionalidad',
        'aud_observacion',
    ];
}