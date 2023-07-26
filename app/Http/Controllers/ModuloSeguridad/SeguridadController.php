<?php

namespace App\Http\Controllers\ModuloSeguridad;


use App\Http\Controllers\Controller;
use App\Http\Traits\AuditoriaTrait;
use App\Http\Traits\dafaultDataTrait;
use App\Http\Traits\InventarioTrait;
use App\Http\Traits\TokenTrait;
use App\Http\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class SeguridadController extends Controller
{
    use AuditoriaTrait;
    use UserTrait;
    use TokenTrait;
    use dafaultDataTrait;
    use InventarioTrait;
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario' => 'required|string|min:1',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }            

            $usuario = $request->usuario;
            $password = $request->password;

            #VERIFICAR SI ES USUARIO DEFAULT DEL SISTEMA
            if ($this->verificarUserDefault($usuario, $password)) {
                return $this->obtenerUsuarioComprasDefault($usuario, $password);
            }

            #OBTENER TOKEN DE MODULO SEGURIDAD
            $tokenSeguridad = $this->obtenerTokenSeguridad();

            #VERIFICAR EMAIL DE SEGURIDADES
            $usuario = ($this->esCorreoElectronicoValido($usuario)) ? $this->obtenerUserbyEmail($tokenSeguridad, $usuario)['usr_user'] : $request->usuario;

            #OBTENER PERMISOS Y USUARIO DE MODULO SEGURIDAD
            $permisosUsuario = $this->obtenerPermisosUsuarioSeguridad($usuario, $password, $tokenSeguridad);

            #USUARIO PARA MODULO COMPRAS
            $usuarioCompras = $this->registerUser($permisosUsuario['user']['usr_full_name'], $permisosUsuario['user']['usr_id'], $permisosUsuario['user']['usr_email'], $password);

            #TOKEN PARA MODULO DE COMPRAS
            $tokenCompras = $this->generarToken(['email' => $permisosUsuario['user']['usr_email'], 'password' => $password]);

            #TOKEN PARA INVENTARIO
            $tokenInventario = $this->obtenerTokenInventario();

            #AUDITORIA
            $this->registrarAuditoria($permisosUsuario['user']['usr_email'], "Login", "Compras", "Ingreso al sistema del Modulo Compras", "Nombre usuario: " . $permisosUsuario['user']['usr_full_name']);

            return response()->json([
                'user' => $usuarioCompras,
                'permisos' => $permisosUsuario['functions'],
                'authorization' => [
                    'token' => $tokenCompras,
                    'type' => 'bearer',
                ],
                'tokenInventario' => $tokenInventario['token']
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Usuario o contraseña incorrectos o módulo de Seguridades sin servicio'], 400);
        }
    }

    private function obtenerPermisosUsuarioSeguridad($usuario, $password, $token)
    {
        $permisosUsuario = Http::withToken($token['access_token'])
            ->get('http://20.163.192.189:8080/api/login', [
                'user_username' => $usuario,
                'user_password' => $password,
                'mod_name' => 'Compras',
            ]);
        return $permisosUsuario->json();
    }



    private function obtenerTokenSeguridad()
    {
        $tokenSeguridad = Http::asForm()->post('http://20.163.192.189:8080/token', [
            'username' => '1005009475',
            'password' => 'lhramirezm2023',
        ]);
        return $tokenSeguridad->json();
    }

    private function obtenerUserbyEmail($token, $email)
    {
        $url = "http://20.163.192.189:8080/api/user_email/" . urlencode($email);
        $usuario = Http::withToken($token['access_token'])
            ->get($url);
        return $usuario->json();
    }

    private function esCorreoElectronicoValido($correo)
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }
}