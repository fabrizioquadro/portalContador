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
        Schema::create('eventos_xml', function (Blueprint $table) {
            $table->id();
            $table->integer('id_xml');
            $table->string('chNFe')->nullable();
            $table->string('nNFIni')->nullable();
            $table->string('tp_evento');
            $table->dateTime('dhEvento');
            $table->string('processamento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_xml');
    }
};
