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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // ID do chamado
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); // Chave estrangeira para a tabela departments
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Chave estrangeira para a tabela users
            $table->string('title'); // Título do chamado
            $table->text('description'); // Descrição do chamado
            $table->enum('priority', ['high', 'medium', 'low'])->default('low'); // Prioridade do chamado
            $table->timestamps(); // Data de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
