<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audiorias', function (Blueprint $table) {
            $table->id();
            $table->string('aud_usuario');
            $table->date('aud_fecha');
            $table->string('aud_accion');
            $table->string('aud_modulo');
            $table->string('aud_funcionalidad');
            $table->string('aud_observacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiorias');
    }
};