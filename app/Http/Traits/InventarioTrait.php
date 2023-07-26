<?php
namespace App\Http\Traits;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

trait InventarioTrait
{
    public function obtenerTokenInventario()
    {
        $tokenInventario = Http::withBody(
            json_encode([
                "username" => "Mateito",
                "password" => "12345",
            ]),
            'application/json'
        )->get('https://inventarioproductos.onrender.com/auth');
        return $tokenInventario->json();
    }
}