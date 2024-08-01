<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Definição da classe anônima para a migration
return new class extends Migration
{
    /**
     * Executa as migrações.
     */
    public function up(): void
    {
        // Cria a tabela 'tickets'
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' do tipo BIGINT com auto-incremento e chave primária
            $table->foreignId('department_id')->constrained()->onDelete('cascade');// Cria uma coluna 'department_id' como chave estrangeira referenciando 'id' na tabela 'departments' com exclusão em cascata
            $table->foreignId('user_id')->constrained()->onDelete('cascade');// Cria uma coluna 'user_id' como chave estrangeira referenciando 'id' na tabela 'users' com exclusão em cascata
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('cascade');// Cria uma coluna 'assigned_to_user_id' como chave estrangeira referenciando 'id' na tabela 'users' com exclusão em cascata (opcional)
            $table->foreignId('opened_by_department_id')->constrained('departments')->onDelete('cascade');// Cria uma coluna 'opened_by_department_id' como chave estrangeira referenciando 'id' na tabela 'departments' com exclusão em cascata
            $table->string('title'); // Cria uma coluna 'title' do tipo VARCHAR para armazenar o título do chamado
            $table->text('description'); // Cria uma coluna 'description' do tipo TEXT para armazenar a descrição do chamado
            $table->enum('priority', ['high', 'medium', 'low'])->default('low');// Cria uma coluna 'priority' com valores 'high', 'medium' e 'low', definindo 'low' como padrão
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at' para controlar as datas de criação e atualização
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Remove a tabela 'tickets' caso exista
        Schema::dropIfExists('tickets');
    }
};
