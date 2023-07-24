<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\AuditoriaTrait;
use App\Http\Traits\TokenTrait;
use App\Http\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use UserTrait;
    use TokenTrait;
    use AuditoriaTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token = $this->generarToken($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $this->usuarioToken();
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identificacion' => 'required|string|max:13',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = $this->registerUser($request->name, $request->identificacion, $request->email, $request->password);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user->email != "mbcachimuell@utn.edu.ec" and $user->email != "edenriquezg@utn.edu.ec" and $user->email != "jafaicanp@utn.edu.ec") {
            $this->registrarAuditoria($user->email, "Logout", "Compras", "Salir del sistema Compras", "Nombre del usuario: " . $user->name);
            $user->delete();
        } else {
            $this->registrarAuditoria($user->email, "Logout", "Compras", "Salir del sistema Compras", "Usuario Deafult del Modulo Compras: " . $user->name);
        }
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}