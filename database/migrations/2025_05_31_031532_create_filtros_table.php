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
        Schema::create('filtros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_programa');
            $table->string('tramite');
            $table->decimal('min', 10, 2);
            $table->decimal('max', 10, 2);
            $table->unsignedBigInteger('ficha_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filtros');
    }
};
