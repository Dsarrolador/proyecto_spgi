<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerimiento_cliente', function (Blueprint $table) {
            $table->id();
            $table->text('texto_imagen')->nullable();   // Puede ser texto, enlace, o ruta de imagen
            $table->string('cliente');                  // Nombre del cliente o ID si luego lo relacionas
            $table->string('tiempo_transcurrido')->nullable(); // Ej: "2 horas", "5 días"
            $table->string('estado')->default('pendiente'); // Estado: pendiente, proceso, completado
            $table->timestamps();   // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerimiento_cliente');
    }
};
