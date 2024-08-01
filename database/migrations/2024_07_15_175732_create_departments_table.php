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
        // Cria a tabela 'departments'
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' do tipo BIGINT com auto-incremento e chave primária
            $table->string('name'); // Cria uma coluna 'name' do tipo VARCHAR para armazenar o nome do departamento
            $table->string('email'); // Cria uma coluna 'email' do tipo VARCHAR para armazenar o email do departamento
            $table->string('phone')->nullable(); // Cria uma coluna 'phone' do tipo VARCHAR que pode ser nula (opcional)
            $table->enum('status', ['active', 'inactive'])->default('active'); // Cria uma coluna 'status' com valores 'active' e 'inactive', definindo 'active' como padrão
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Cria uma coluna 'company_id' como chave estrangeira referenciando 'id' na tabela 'companies' com exclusão em cascata
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at' para controlar as datas de criação e atualização

            // Adiciona um índice único para garantir que o nome do departamento seja único por empresa
            $table->unique(['name', 'company_id']);
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Remove a tabela 'departments' caso exista
        Schema::dropIfExists('departments');
    }
};
