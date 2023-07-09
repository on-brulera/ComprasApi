<?php

namespace Database\Factories;

use App\Models\Proveedores;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturas>
 */
class FacturasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipoPago = $this->faker->randomElement(['Crédito', 'Contado']);
        $fechaVencimiento = ($tipoPago === 'Crédito') ? $this->faker->date : null;
        $proveedor = Proveedores::factory()->create();

        return [
            'proveedor_id' => $proveedor->id,
            'fecha_factura' => $this->faker->date,
            'tipo_pago' => $tipoPago,
            'fecha_vencimiento' => $fechaVencimiento,
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
            'impreso' => false,
        ];
    }
}