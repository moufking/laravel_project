<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Mail\LotEmail;
use App\Models\Ticket;
use App\Models\HistoriqueGains;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class LotController extends Controller
{

    public function  searchLot(Request $request) {

        $validator = Validator::make($request->all(), [
            'numberTicket' => 'required|string|regex:/^[-0-9\+]+$/',
            //string|digits:10
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 401);
        }

        $ticket = Ticket::where('number', $request->numberTicket )->first();
        if( $ticket ) {

            if( !$ticket->isValid() ){
                return response()->json(['message'=>'Ticket invalide'], 402);
            }

            if($ticket->isUsed) {
                return response()->json(['message' => 'Ticket déjà utilisé' ], 403);
            }

            $ticket->isUsed = true;
            $ticket->idUser = Auth::id();
            $isSaved =  $ticket->save();

            if( $isSaved) {
                $historical  = new  HistoriqueGains();
                $historical ->idTicket =  $ticket->id;
                $historical ->idUser =  Auth::id();
                $historical ->save();
               try{

                    //'administrateur@chezmoi.com'
                Mail::to(Auth::user()->email)
                ->send(new LotEmail($ticket ));

                //TODO  joindre le ticket avec lequel l'utilisateur pourra récupérer le code en caisse.

               } catch(Exception $e){
                    Log::info("Error from LotController when sending LotEmail to user:". Auth::id(). " : ". $e->getMessage());
               }

               return response()->json([
                "ticket" => new TicketResource($ticket),
               ], 200);

            } else {

                return response()->json([
                    "message" => "Erreur serveur : veuillez rejouer",
                ], 500);

            }
        }

        return response()->json(['message' => 'Lot introuvable'], 400);
    }
}
