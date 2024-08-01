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
        // Cria a tabela 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' do tipo BIGINT com auto-incremento e chave primária
            $table->string('name'); // Cria uma coluna 'name' do tipo VARCHAR para armazenar o nome do usuário
            $table->string('email'); // Cria uma coluna 'email' do tipo VARCHAR para armazenar o email do usuário
            $table->timestamp('email_verified_at')->nullable(); // Cria uma coluna 'email_verified_at' para armazenar a data de verificação do email (opcional)
            $table->string('password'); // Cria uma coluna 'password' do tipo VARCHAR para armazenar a senha do usuário
            $table->string('phone'); // Cria uma coluna 'phone' do tipo VARCHAR para armazenar o telefone do usuário
            $table->enum('role', ['admin', 'user'])->default('user'); // Cria uma coluna 'role' com valores 'admin' e 'user', definindo 'user' como padrão
            $table->enum('status', ['active', 'inactive'])->default('active'); // Cria uma coluna 'status' com valores 'active' e 'inactive', definindo 'active' como padrão
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Cria uma coluna 'company_id' como chave estrangeira referenciando 'id' na tabela 'companies' com exclusão em cascata
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); // Cria uma coluna 'department_id' como chave estrangeira referenciando 'id' na tabela 'departments' com exclusão em cascata
            $table->rememberToken(); // Cria uma coluna 'remember_token' do tipo VARCHAR para armazenar o token de "lembrar de mim" para autenticação
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at' para controlar as datas de criação e atualização
        });

        // Cria a tabela 'password_reset_tokens'
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Cria uma coluna 'email' do tipo VARCHAR e define como chave primária
            $table->string('token'); // Cria uma coluna 'token' do tipo VARCHAR para armazenar o token de redefinição de senha
            $table->timestamp('created_at')->nullable(); // Cria uma coluna 'created_at' para armazenar a data de criação do token (opcional)
        });

        // Cria a tabela 'sessions'
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Cria uma coluna 'id' do tipo VARCHAR e define como chave primária
            $table->foreignId('user_id')->nullable()->index(); // Cria uma coluna 'user_id' como chave estrangeira, opcional, e adiciona um índice
            $table->string('ip_address', 45)->nullable(); // Cria uma coluna 'ip_address' do tipo VARCHAR com comprimento 45, opcional
            $table->text('user_agent')->nullable(); // Cria uma coluna 'user_agent' do tipo TEXT, opcional
            $table->longText('payload'); // Cria uma coluna 'payload' do tipo LONGTEXT para armazenar os dados da sessão
            $table->integer('last_activity')->index(); // Cria uma coluna 'last_activity' do tipo INTEGER e adiciona um índice
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Remove as tabelas 'users', 'password_reset_tokens' e 'sessions' caso existam
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
