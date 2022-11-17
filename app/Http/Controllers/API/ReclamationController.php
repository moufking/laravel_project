<?php

namespace App\Http\Controllers\API;

use App\Models\HistoriqueGains;
use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReclamationController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|int',
            'lieu_livraison' => "required|string|regex:/^[-'a-zA-ZÀ-ÿ0-9Ññ\s]+$/",
            'phone' => 'required|string|between:5,15|regex:/^[-0-9\+]+$/',
            'history_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $historique_id = $request->get('history_id');

       $historique= HistoriqueGains::find($historique_id);


       if(Auth::user()->id !=$request->get('user_id')) {

           return response()->json([
               "message" => "Utilisateur non trouvé",
           ], 400);
       }

        if($historique) {

            if(!empty($historique->takenAt)) {

                return response()->json([
                    "message" => 'Des lots déjà récupérés',
                ], 400);
            }else {

                if(isset($historique->getReclamation) && !empty($historique->getReclamation) ) {
                    return response()->json([
                        "message" => 'Une reclamation existe déja  dans la base de données .',
                    ], 400);
                }

                $user = User::find($request->user_id);
                if($user) {

                    //$array = $request->all();
                    //$all_input = array_push($array, "historique_id"=>$historique_id);

                    $response =  Reclamation::create($request->all());

                    if($response) {
                        return response()->json([
                            "message" => 'Réclamation prise en compte avec succès.',
                            "reclamation" => $response
                        ], 200);
                    }else {

                        return response()->json([
                            "message" => "Impossible d'enregistrer la réclamation.",
                        ], 400);
                    }

                }
            }
        }else {
            return response()->json([
                "message" => "historique introuvable",
            ], 400);
        }




    }

}
