<?php
namespace App\Http\Traits;

use App\Models\User;



trait dafaultDataTrait
{
    use TokenTrait;
    use AuditoriaTrait;
    public function verificarUserDefault($email, $password)
    {
        $emailsPermitidos = ["edenriquezg@utn.edu.ec", "jafaicanp@utn.edu.ec", "mbcachimuell@utn.edu.ec"];
        return in_array($email, $emailsPermitidos) and $password == "passwordutn";
    }

    public function obtenerUsuarioComprasDefault($email, $password)
    {
        $token = $this->generarToken(['email' => $email, 'password' => $password]);
        $usuario = $this->usuarioToken();
        $this->registrarAuditoria($email, "Login", "Compras", "Ingreso al sistema del Modulo Compras", "Nombre usuario: " . "Usuario default del modulo Compras: " . $usuario->name);
        return response()->json([
            'user' => $usuario,
            'permisos' => $this->obtenerPermisos($email),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function obtenerPermisos($email)
    {
        if ($email == "edenriquezg@utn.edu.ec")
            return ["1" => "Facturas", "2" => "Proveedores", "3" => "Auditoria"];
        if ($email == "jafaicanp@utn.edu.ec")
            return ["1" => "Facturas", "2" => "Proveedores"];
        if ($email == "mbcachimuell@utn.edu.ec")
            return ["1" => "Facturas"];
    }

    public function userDefaultControl()
    {
        $usuario = User::whereNotIn('email', ["edenriquezg@utn.edu.ec", "jafaicanp@utn.edu.ec", "mbcachimuell@utn.edu.ec"])->first();
        if ($usuario) {
            $usuario->delete();
        }
    }
}