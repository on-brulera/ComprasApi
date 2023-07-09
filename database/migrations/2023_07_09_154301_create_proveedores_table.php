<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('documento_identificacion')->unique();
            $table->string('nombre');
            $table->string('ciudad');
            $table->string('tipo_proveedor');
            $table->string('direccion')->nullable();
            $table->string('telefono');
            $table->string('email')->unique();
            $table->string('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
