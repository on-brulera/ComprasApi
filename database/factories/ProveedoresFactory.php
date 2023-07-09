<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proveedores>
 */
class ProveedoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'documento_identificacion' => $this->faker->randomElement([
                $this->faker->numerify('##########'), 
                $this->faker->numerify('#############'), 
            ]),
            'nombre' => $this->faker->name,
            'ciudad' => $this->faker->city,
            'tipo_proveedor' => $this->faker->randomElement(['Crédito', 'Contado']),
            'direccion' => $this->faker->address,
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }
}