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
        Schema::create('xml_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_xml');
            $table->string('cProd')->nullable();
            $table->string('cEAN')->nullable();
            $table->string('xProd')->nullable();
            $table->string('NCM')->nullable();
            $table->string('EXTIPI')->nullable();
            $table->string('CFOP')->nullable();
            $table->string('uCom')->nullable();
            $table->string('qCom')->nullable();
            $table->double('vUnCom', 10,2)->nullable();
            $table->double('vProd', 10,2)->nullable();
            $table->double('vDesc', 10,2)->nullable();
            $table->double('vOutro', 10,2)->nullable();
            $table->string('cEANTrib')->nullable();
            $table->string('uTrib')->nullable();
            $table->string('qTrib')->nullable();
            $table->double('vUnTrib',10,2)->nullable();
            $table->string('indTot')->nullable();
            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_xml')->references('id')->on('xmls');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml_produtos');
    }
};
