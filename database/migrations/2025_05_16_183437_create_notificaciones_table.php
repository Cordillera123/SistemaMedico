<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('tipo', 50); // 'resultado_nuevo', 'cita', etc.
            $table->boolean('leida')->default(false);
            $table->morphs('notificable'); // Para relacionar con diferentes modelos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};