<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->restrictOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('nombre_completo');
            $table->string('telefono')->nullable();
            $table->string('cargo');
            $table->foreignId('carrera_id')->constrained('carreras')->restrictOnDelete();
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->date('fecha_ingreso');
            $table->unsignedInteger('antiguedad_anios')->default(0);
            $table->unsignedTinyInteger('dias_asignados');
            $table->unsignedInteger('dias_utilizados')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
