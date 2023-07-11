<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleFacturas>
 */
class DetalleFacturasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'factura_id' => $this->faker->randomElement([1, 2, 3]),
            'producto_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9]),
            'cantidad' => $this->faker->numberBetween(1, 10),
            'subtotal' => $this->faker->randomFloat(2, 10, 100),
            'total' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}