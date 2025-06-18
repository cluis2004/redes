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
        Schema::create('optical_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre descriptivo de la configuración');
            $table->text('description')->nullable()->comment('Descripción de la configuración');
            $table->json('parameters')->comment('Parámetros de configuración en formato JSON');
            $table->decimal('total_loss_db', 8, 2)->nullable()->comment('Pérdida total calculada en dB');
            $table->decimal('output_power_dbm', 8, 2)->nullable()->comment('Potencia de salida calculada en dBm');
            $table->boolean('is_template')->default(false)->comment('Si es una plantilla predefinida');
            $table->string('created_by')->nullable()->comment('Usuario que creó la configuración');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['is_template', 'created_at']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optical_configurations');
    }
};