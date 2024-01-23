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
        Schema::create('imports_xml', function (Blueprint $table) {
            $table->id();
            $table->integer('id_import');
            $table->integer('id_cliente');
            $table->string('arquivo');
            $table->string('tp_xml')->nullable();
            $table->string('st_xml');
            $table->text('retorno')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports_xml');
    }
};
