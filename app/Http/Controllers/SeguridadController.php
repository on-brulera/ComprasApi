<?php

namespace App\Http\Controllers;

use App\Models\Auditorias;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        #CONSULTA DE LOS PERMISOS QUE TIENE EL USUARIO

        $tokenSeguridad = $this->obtenerTokenSeguridad();

        $seguridades = $this->consultaPermisoSeguridad($email, $password);

        

        $usuario = [
            "id" => $seguridades['usuario']['usr_id'],
            "name" => $seguridades['usuario']['usr_full_name'],
            "email" => $seguridades['usuario']['usr_email']
        ];

        #OBTENER TOKEN PARA COMPRAS

        $token = $this->obtenerToken();

        return response()->json(['usuario' => $usuario, 'permisos' => $seguridades['permisos'], "token" => $token]);
    }

    private function consultaPermisoSeguridad($email, $password)
    {
        $moduleName = 'Compras';
        $token = $this->obtenerTokenSeguridad();
        $url = "http://20.163.192.189:8080/api/user_email/lhramirezm@utn.edu.ec";
        $url = "http://20.163.192.189:8080/api/user_email/" . urlencode($email);

        $usuario = Http::withToken($token['access_token'])
            ->get($url);

        $permisos = Http::withToken($token['access_token'])
            ->get('http://20.163.192.189:8080/api/login', [
                'user_username' => $usuario['usr_user'],
                'user_password' => $password,
                'mod_name' => $moduleName,
            ]);

        return ["usuario" => $usuario->json(), "permisos" => $permisos->json()];
    }

    private function obtenerTokenSeguridad()
    {
        $tokenSeguridad = Http::asForm()->post('http://20.163.192.189:8080/token', [
            'username' => '1005009475',
            'password' => '100102',
        ]);
        return $tokenSeguridad->json();
    }


    public function obtenerToken()
    {
        $credentials = [
            'email' => 'lhramirezm@utn.edu.ec',
            'password' => 'password'
        ];

        $response = Http::post('https://compras-api-2wmv.onrender.com/api/login', $credentials);
        // $response = Http::post('http://127.0.0.1:8000/api/login', $credentials);

        if ($response->status() === 200) {
            $responseData = $response->json();

            if (isset($responseData['authorization']['token'])) {
                $token = $responseData['authorization']['token'];
                return $token;
            }
        }

        return null; // Si no se pudo obtener el token, retorna null o maneja el error de acuerdo a tus necesidades.
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