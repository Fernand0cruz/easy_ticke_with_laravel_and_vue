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
        // Cria a tabela 'ticket_responses'
        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' do tipo BIGINT com auto-incremento e chave primária
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');// Cria uma coluna 'ticket_id' como chave estrangeira referenciando 'id' na tabela 'tickets' com exclusão em cascata
            $table->foreignId('user_id')->constrained()->onDelete('cascade');// Cria uma coluna 'user_id' como chave estrangeira referenciando 'id' na tabela 'users' com exclusão em cascata (usuário que respondeu)
            $table->longText('response'); // Cria uma coluna 'response' do tipo TEXT para armazenar o texto da resposta
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at' para controlar as datas de criação e atualização
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Remove a tabela 'ticket_responses' caso exista
        Schema::dropIfExists('ticket_responses');
    }
};
