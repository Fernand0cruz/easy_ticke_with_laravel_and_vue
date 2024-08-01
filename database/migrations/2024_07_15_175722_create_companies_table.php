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
        // Cria a tabela 'companies'
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' do tipo BIGINT com auto-incremento e chave primária
            $table->string('name'); // Cria uma coluna 'name' do tipo VARCHAR para armazenar o nome da empresa
            $table->string('logo')->nullable(); // Cria uma coluna 'logo' do tipo VARCHAR que pode ser nula (opcional)
            $table->string('cnpj')->nullable(); // Cria uma coluna 'cnpj' do tipo VARCHAR que pode ser nula (opcional)
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at' para controlar as datas de criação e atualização
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Remove a tabela 'companies' caso exista
        Schema::dropIfExists('companies');
    }
};
