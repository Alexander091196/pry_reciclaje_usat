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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description');
            $table->string('image')->nullable(); // Nuevo campo para almacenar la ruta de la imagen
            $table->unsignedBigInteger('horarie_id');
            $table->foreign('horarie_id')->references('id')->on('horaries');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
