<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    public function index()
    {

        $reclamations = Reclamation::all();
        return view('admin.reclamation.list_reclamation', compact("reclamations"));
    }

    public function exportCsv()
    {
        $fileName = 'Reclamation.csv';
        $reclamations = Reclamation::latest()->get();


        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        //'user_id',"","phone","", "history_id"
        $columns = array('nom', 'lieu_livraison', 'statut_reclamation', 'lot', 'Start Date', 'Due Date');

        $callback = function () use ($reclamations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reclamations as $reclamation) {
                $row['nom'] =  $reclamation->user->name;
                $row['lieu_livraison'] = $reclamation->lieu_livraison;
                $row['statut_reclamation'] = $reclamation->statut_reclamation;
                $row['lot'] = $reclamation->historique->getTicket->getLot->libelle;
                $row['Start Date'] = $reclamation->created_at;
                $row['Due Date'] = $reclamation->updated_at;

                fputcsv($file, array($row['nom'], $row['lieu_livraison'], $row['statut_reclamation'], $row['lot'], $row['Start Date'], $row['Due Date']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function filter_reclaramation(Request $request)
    {

        $from = $request->get('date_start');
        $to = $request->get('date_end');


        if (empty($from) and !empty($to)) {
            $reclamations = Reclamation::where('created_at', '<=', $to)->get();
        }
        if (!empty($from) and empty($to)) {
            $reclamations = Reclamation::where('created_at', '>=', $from)->get();
        }

        if (!empty($from) and !empty($to)) {
            $reclamations = Reclamation::whereBetween('created_at', [$from, $to])->get();
        }

        if (empty($from) and empty($to)) {
            return back()->with(["errorNotification" => "Veuillez renseignez au moin un champ pour faire la recherche"]);
        }

        return view('admin.reclamation.list_reclamation', compact("reclamations"));

    }


    public function view_information($reclamation_id)
    {
        $reclamation = Reclamation::where('id', $reclamation_id)->first();
        return view('admin.reclamation.more_information', compact("reclamation"));

    }

    public function updateStatutReclamation($reclamation_id, Request $request) {



      $reclamation= Reclamation::find($reclamation_id);
      $reclamation->statut_reclamation = $request->statut_reclamation;
      $response = $reclamation->save();



        if ($response) {
            return back()->with(["successNotification" => "Le statut a été modifier avec succès"]);
        }else {
            return back()->with(["errorNotification" => "Une erreur c'est produit lors de la modification du statut"]);
        }
    }
}
