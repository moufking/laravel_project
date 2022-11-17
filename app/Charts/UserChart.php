<?php

declare(strict_types = 1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\User;
class UserChart extends BaseChart
{

   public ?string $name =  "userChart";
    public ?string $routeName = "userChart";
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $totalUsers = User::all();
        $usersForNewsLetters =  $totalUsers->where("newsletter", "=", 1)->count();
        return Chartisan::build()
            ->labels(['Total Participants', 'Inscrits à la newsletter'])
            ->dataset('Nombre', [$totalUsers->count(), $usersForNewsLetters]);

    }
}
