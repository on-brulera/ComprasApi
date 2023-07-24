<?php
namespace App\Http\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait UserTrait
{
    public function registerUser($name, $identificacion, $email, $password)
    {
        $user = User::create([
            'name' => $name,
            'identificacion' => $identificacion,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        return $user;
    }
}