<?php

declare(strict_types = 1);

namespace App\Charts;

use App\Models\Ticket;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;

class TicketChart extends BaseChart
{
    public ?string $name =  "ticketChart";
    public ?string $routeName = "ticketChart";
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $tickets =  Ticket::all();
        $nbrTicketsUsed =  count ( $tickets->where("isUsed", "=",1) );
        $nbrTicketRemain =  count( $tickets ) -  $nbrTicketsUsed;

        return Chartisan::build()
            ->labels(['Total', 'Utilisés', 'Reste'])
            ->dataset('Nombre', [$tickets->count(), $nbrTicketsUsed, $nbrTicketRemain]);
    }
}
