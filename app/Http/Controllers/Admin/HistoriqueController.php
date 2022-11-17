<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\HistoriqueGains;

class HistoriqueController extends Controller
{


    public function historique()
    {

        $histories = HistoriqueGains::all();

        return view('admin.historical.list_lots', compact("histories"));
    }

    public function exportCsv()
    {
        $fileName = 'Historique.csv';
        $tasks = HistoriqueGains::latest()->get();


        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        //"number","isUsed", "idUser","idLot"
        $columns = array('number ticket', 'takenAt', 'name user', 'lot', 'Start Date', 'Due Date');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['number ticket'] =  $task->getTicket->number;
                $row['name user'] = $task->getUser ? $task->getUser->name : null;
                $row['isUsed'] = $task->takenAt ? "yes" : "no";
                $row['lot'] = $task->getTicket->getLot->libelle;
                $row['Start Date'] = $task->created_at;
                $row['Due Date'] = $task->updated_at;

                fputcsv($file, array($row['number ticket'], $row['isUsed'], $row['name user'], $row['lot'], $row['Start Date'], $row['Due Date']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateHistorique(Request $request)
    {

        $historical = HistoriqueGains::find($request->historical_id);

        if (!empty($historical)) {
            $historical->takenAt = Carbon::now();
            $historical->update();
            return back()->with(["successNotification" => "Lot marquer comme récupérer"]);

        } else {
            return back()->with(["errorNotification" => "Impossoble de recupérer cette information"]);
        }

    }

    public function view_information($historical_id)
    {

        $historical = HistoriqueGains::where('id', $historical_id)->first();
        return view('admin.historical.more_information', compact("historical"));

    }

    public function filter_historique(Request $request)
    {
        $from = $request->get('date_start');
        $to = $request->get('date_end');


        if (empty($from) and !empty($to)) {
            $histories = HistoriqueGains::where('created_at', '<=', $to)->get();
        }
        if (!empty($from) and empty($to)) {
            $histories = HistoriqueGains::where('created_at', '>=', $from)->get();
        }

        if (!empty($from) and !empty($to)) {
            $histories = HistoriqueGains::whereBetween('created_at', [$from, $to])->get();
        }

        if (empty($from) and empty($to)) {
            return back()->with(["errorNotification" => "Veuillez renseignez au moin un champ pour faire la recherche"]);
        }

        return view('admin.historical.list_lots', compact("histories"));

    }


}
