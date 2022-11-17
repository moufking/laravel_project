<?php

declare(strict_types = 1);

namespace App\Charts;

use App\Models\HistoriqueGains;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;

class JeuParJourChart extends BaseChart
{
    public ?string $name =  "jeuParJourChart";
    public ?string $routeName = "jeuParJourChart";
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {   $nbrGamePerDay =  HistoriqueGains::selectRaw('count(idTicket) as nbrGamePerDay, date_format(created_at, "%d/%m/%Y" ) as date')
                                                          ->groupBy('date')->get();
        return Chartisan::build()
            ->labels($nbrGamePerDay->pluck("date")->toArray())
            ->dataset('Nombre de jeu par jour', $nbrGamePerDay->pluck("nbrGamePerDay")->all());

    }
}
