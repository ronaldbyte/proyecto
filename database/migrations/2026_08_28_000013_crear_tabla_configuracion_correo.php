<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_correo', function (Blueprint $table) {
            $table->id();
            $table->boolean('activo')->default(false);
            $table->string('host')->default('smtp.gmail.com');
            $table->unsignedInteger('puerto')->default(587);
            $table->string('usuario')->nullable();
            $table->text('contrasena')->nullable();
            $table->string('nombre_remitente')->default('Jefatura de Personal UNSXX');
            $table->text('ultimo_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_correo');
    }
};
