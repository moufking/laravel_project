<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number')->unique();
            $table->boolean('isUsed')->default(0);
            $table->unsignedBigInteger('idUser')->nullable();
            $table->unsignedBigInteger('idLot');
            $table->boolean('associatedToACommand')->default(0); //permet de savoir si ticket est imprimé sur une commande
            $table->date('startDate');
            $table->date('endDate');
            $table->timestamps();

            $table->foreign('idUser')
                  ->references('id')
                  ->on('users');

            $table->foreign('idLot')
                  ->references('id')
                  ->on('lots');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
