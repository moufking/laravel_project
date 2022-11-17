<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lots')->insert([
            'pourcentage' => 60,
            'libelle' => 'un infuseur thé',
        ]);
        DB::table('lots')->insert([
            'pourcentage' => 20,
            'libelle' => 'une boite de 100g d’un thé',
        ]);
        DB::table('lots')->insert([
            'pourcentage' => 10,
            'libelle' => 'une boite de 100g d’un thé signature',
        ]);
        DB::table('lots')->insert([
            'pourcentage' => 6,
            'libelle' => 'un coffret découverte',
        ]);
        DB::table('lots')->insert([
            'pourcentage' => 4,
            'libelle' => 'un coffret découverte d’une valeur de 69€',
        ]);
    }
}
