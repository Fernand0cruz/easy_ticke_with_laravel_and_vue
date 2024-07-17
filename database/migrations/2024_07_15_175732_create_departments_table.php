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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); // Campo email obrigatório
            $table->string('location')->nullable(); // Campo localização opcional
            $table->string('phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status com valores ativos e inativos
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Chave estrangeira para Company
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
