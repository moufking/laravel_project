<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclamationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->string("lieu_livraison");
            $table->string("phone");
            $table->enum('statut_reclamation',["en attente", "livrer"])->default(env("en attente"));
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('history_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->foreign('history_id')
                ->references('id')
                ->on('historique_gains');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reclamations');
    }
}
