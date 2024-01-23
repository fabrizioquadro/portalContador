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
        Schema::create('xmls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_import');
            $table->unsignedBigInteger('id_cliente');
            $table->string('arquivo');
            $table->string('situacao')->nullable();
            $table->string('modDoc')->nullable();
            $table->string('serie')->nullable();
            $table->string('numero')->nullable();
            $table->dateTime('dhEmi')->nullable();
            $table->string('destXnome')->nullable();
            $table->string('destCpfCnpj')->nullable();
            $table->string('chNfe')->nullable();
            $table->double('vPag', 10,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xmls');
    }
};
