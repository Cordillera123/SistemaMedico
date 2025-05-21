
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('telefono', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('role_id')->constrained('roles');
            $table->boolean('activo')->default(true);
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token_recuperacion')->nullable();
            $table->timestamp('expiracion_token')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Para mantener registros históricos
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};