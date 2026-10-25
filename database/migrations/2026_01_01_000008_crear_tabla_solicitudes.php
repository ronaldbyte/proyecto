<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedInteger('dias_solicitados');
            $table->text('motivo');
            $table->string('estado', 40)->index();
            $table->foreignId('cancelada_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('cancelada_en')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamps();

            $table->index(['docente_id', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
