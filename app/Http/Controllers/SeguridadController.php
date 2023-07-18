<?php

namespace App\Http\Controllers;

use App\Models\Auditorias;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class SeguridadController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $email = $request->email;
        $password = $request->password;

        #VERIFICAR USUARIO LOCAL Y REMOTO

        $usuario = DB::table('users')->where('email', "=", $email)->get();
        $tokenSeguridad = $this->obtenerTokenSeguridad();
        $permisos = [["1" => "Facturas"], ["2" => "Proveedores"], ["3" => "Auditoría"]];

        if ($usuario->isEmpty()) {
            $usuario = $this->obtenerUsuarioSeguridad($tokenSeguridad, $email);
            if (isset($usuario['detail'])) {
                return response()->json(['message' => 'El usuario no se encuentra en la BDD del módulo de seguridades'], 404);
            }
            $registrarUser = $this->registrarUsuarioCompras($usuario['usr_full_name'], $usuario['usr_email'], $usuario['usr_id'], $usuario['usr_password']);
            if (!$registrarUser) {
                return response()->json(['message' => 'No se pudo registrar el usuario en la BDD compras'], 404);
            }
            $permisos = $this->obtenerPermisosSeguridad($tokenSeguridad, $email, $password);
        }

        $tokenCompras = $this->obtenerTokenCompras($email, $password);
        $this->registrarAuditoria($email, "Login", "Compras", "Ingreso al sistema del Modulo Compras", "Nombre usuario: " . $tokenCompras['user']['name']);
        $tokenCompras['permisos'] = $permisos;
        return response()->json($tokenCompras);
    }

    private function obtenerPermisosSeguridad($token, $email, $password)
    {
        $usuario = str_replace(strstr($email, '@'), '', $email);
        $permisos = Http::withToken($token['access_token'])
            ->get('http://20.163.192.189:8080/api/login', [
                'user_username' => $usuario,
                'user_password' => $password,
                'mod_name' => 'Compras',
            ]);
        return $permisos->json();
    }

    private function obtenerUsuarioSeguridad($token, $email)
    {
        $token = $this->obtenerTokenSeguridad();
        $url = "http://20.163.192.189:8080/api/user_email/" . urlencode($email);

        $usuario = Http::withToken($token['access_token'])
            ->get($url);
        return $usuario->json();
    }


    private function obtenerTokenSeguridad()
    {
        $tokenSeguridad = Http::asForm()->post('http://20.163.192.189:8080/token', [
            'username' => '1005009475',
            'password' => '100102',
        ]);
        return $tokenSeguridad->json();
    }


    public function registrarUsuarioCompras($name, $email, $identificacion, $password)
    {
        $credentials = [
            'name' => $name,
            'identificacion' => $identificacion,
            'email' => $email,
            'password' => $password,
        ];
        $response = Http::post('https://compras-api-2wmv.onrender.com/api/register', $credentials);
        print_r("PASOOO");
        if ($response->status() === 200) {
            return true;
        }
        return null;
    }

    public function obtenerTokenCompras($email, $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        $response = Http::post('https://compras-api-2wmv.onrender.com/api/login', $credentials);

        if ($response->status() === 200) {
            return $response->json();
        }

        return null;
    }

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