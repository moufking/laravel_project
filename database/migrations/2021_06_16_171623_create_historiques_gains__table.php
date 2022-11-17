<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriquesGainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historique_gains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTicket');
            $table->unsignedBigInteger('idUser');
            $table->dateTime('takenAt')->nullable(); // date à laquelle le lot a été retiré
            $table->timestamps();

            $table->foreign('idTicket')
                  ->references('id')
                  ->on('tickets');

            $table->foreign('idUser')
                  ->references('id')
                  ->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historiques_gains');
    }
}
