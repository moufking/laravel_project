<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function getATicketNumber(Request $request){

        $validation =  Validator::make( $request->all(), [
            'prixCommande'=>'required|int|min:49|regex:/^[-0-9\+]+$/'
        ]);


        if ($validation->fails()){
            return response()->json([
                $validation->errors(),
            ], 404);
        }

        $ticket =  Ticket::where('associatedToACommand', '!=', 1)->first();
        if( $ticket ) {
            $ticket->associatedToACommand = 1;
            $isSaved = $ticket->save();

            if( $isSaved ){
                return response()->json([
                    'numero' => $ticket->number,
                ], 200);

            } else {
                return response()->json([
                    'message' => 'erreur interne veuillez ressayer',
                ], 500);
            }

        } else {
            return response()->json([
                'message'=>'Aucun ticket trouvé',
            ], 404);
        }

    }
}
