<?php

declare(strict_types = 1);

namespace App\Charts;

use App\Models\HistoriqueGains;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;

class LotCharts extends BaseChart
{
    public ?string $name =  "lotChart";
    public ?string $routeName = "lotChart";
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $totalOfLotsWon = HistoriqueGains::all();
        $nbrLotRetires =  $totalOfLotsWon->where('takenAt', '!=', null)->count();
        $nbrLotsNonRetires =  $totalOfLotsWon->count() - $nbrLotRetires;
        return Chartisan::build()
            ->labels(['Total', 'retirés', 'Non retirés'])
            ->dataset('Lots gagnés', [$totalOfLotsWon->count(),  $nbrLotRetires, $nbrLotsNonRetires]);
    }
}
