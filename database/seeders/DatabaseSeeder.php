<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Traits\UserTrait;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use UserTrait;
    public function run(): void
    {
        \App\Models\Proveedores::factory(6)->create();
        $this->registerUser('Faican Peñafiel Jonathan Alexis', '1892230172', 'jafaicanp@utn.edu.ec', 'passwordutn');
        $this->registerUser('Cachimuel Loyo Marlon Brandon', '1984344193', 'mbcachimuell@utn.edu.ec', 'passwordutn');
        $this->registerUser('Enriquez Estaban David Enriquez', '1662893182', 'edenriquezg@utn.edu.ec', 'passwordutn');
    }
}