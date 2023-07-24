<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

trait TokenTrait
{
    public function generarToken($credentials)
    {
        return Auth::attempt($credentials);
    }

    public function usuarioToken()
    {
        return Auth::user();
    }

    public function verificarToken(Request $request)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            throw new AuthorizationException('Falta el token');
        }
        $user = Auth::guard('api')->user();
        if (!$user) {
            throw new AuthorizationException('Token inválido o caducado local');
        }
        return $user;
    }    
}