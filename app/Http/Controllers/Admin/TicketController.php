<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private $ticketAssociatedToLot = [];

    public function listTickets()
    {
        $tickets = Ticket::latest()->get();
        return view('admin.tickets.list_tickets', compact("tickets"));
    }


    public function exportCsv()
    {
        $fileName = 'list_ticket.csv';
        $tasks = Ticket::latest()->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        //"number","isUsed", "idUser","idLot"
        $columns = array('number', 'isUsed', 'name user', 'lot', 'Start Date', 'Due Date');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['number'] = $task->number;
                $row['name user'] = $task->getUser ? $task->getUser->name : null;
                $row['isUsed'] = $task->isUsed ? "yes" : "no";
                $row['lot'] = $task->getLot ? $task->getLot->libelle : null;
                $row['Start Date'] = $task->startDate;
                $row['Due Date'] = $task->endDate;

                fputcsv($file, array($row['number'], $row['isUsed'], $row['name user'], $row['lot'], $row['Start Date'], $row['Due Date']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sauvegarde les tickets générés en db
     */
    public function generateAndSaveTicketToDb()
    {
        $ticketsGenerated = $this->generateRandomNumber(env("NBR_TICKET_TO_GENERATE"));
        $lots = $this->getAllLots();
        $allTicketsToSaveToDb = []; // contient tout les tickets à sauvegarder dans la db
        if (count($lots) > 0) {
            if (count($ticketsGenerated) > 0) {
                $ticketsAssociatedWithLots = $this->associateTicketAndLot($ticketsGenerated, $lots);
                $allTicketsToSaveToDb = $this->creatingTicketModelInstance($ticketsAssociatedWithLots);
                $insertIsSuccessful = Ticket::insert($allTicketsToSaveToDb);

                if ($insertIsSuccessful) {
                    return back()->with(["successNotification" => "tickets générés avec succès"]);
                } else {
                    return back()->with(["errorNotification" => "Une erreur est survenue lors de la génération des tickets ! Veuillez reessayer"]);
                }
            }
        } else {
            return back()->with([
                'errorNotification' => 'Impossible de générer les tickets : Aucun  lots existants'
            ]);
        }
    }

    /**
     * Génère un tableau de nombre(numéro de tickets) aléatoire de 10 chiffres.
     * @param int $nbrOfNumberToGenerate { réprésente le nombre de tickets à générer }
     * @return array  $ticketsdArrayNumbers { tableau contenant les tickets généré }
     */


    private function generateRandomNumber(int $nbrOfNumberToGenerate)
    {
        $arrayOfNumbers = [1, 2, 3, 0, 4, 5, 8, 9, 7, 6];
        $ticketsdArrayNumbers = [];
        $i = 1;
        while (count($ticketsdArrayNumbers) !== $nbrOfNumberToGenerate) {
            $isShuffled = shuffle($arrayOfNumbers);
            if ($isShuffled) {
                $number = implode($arrayOfNumbers);
                if (!in_array((int)$number, $ticketsdArrayNumbers) && strlen($number) == 10
                    && !str_starts_with($number, '0') ) {
                    array_push($ticketsdArrayNumbers, (int)$number);
                }
            }
            $i++;
        }

        return $ticketsdArrayNumbers;
    }

    public function getAllLots()
    {
        $lots = Lot::all("id", "pourcentage");
        if (count($lots) > 0) {
            return $lots;
        } else {
            return [];
        }
    }

    /**
     * Associe  un numéro de ticket à un lot (présicément à l'id du lot concerné)
     * @param array $tickets {ensemble des tickets généré par la fonction generateRandomNumber }
     * @param array $lotsAndPercentages {tableau contenant l'id du lot et le pourcentage qui lui est associé
     * Ex :  20% des tickets gagneront le lot 1
     * }
     */


    private function associateTicketAndLot($tickets, $lotsAndPercentages)
    {
        $ticketAssociatedToLot = [];
        $lastIndex = 0;
        foreach ($lotsAndPercentages as $lot) {

            $numberOfTicketForThisLot = (count($tickets) * $lot->pourcentage) / 100;
            for ($i = $lastIndex; $i < $numberOfTicketForThisLot + $lastIndex; $i++) {
                $numeroDeTicket = $tickets[$i];
                $ticketAssociatedToLot[(string)$numeroDeTicket] = $lot->id;
            }
            $lastIndex += $numberOfTicketForThisLot;
        }
        return $ticketAssociatedToLot;
    }

    /**
     * Crée des objets du modèle Ticket, afin de
     * les insérer en bd
     */


    private function creatingTicketModelInstance(array $ticketsAssociatedWithLots)
    {
        $ticketsComposed = [];
        foreach ($ticketsAssociatedWithLots as $numeroDeTicket => $lotId) {
            array_push($ticketsComposed, [
                "number" => $numeroDeTicket,
                "isUsed" => 0, // 0 équivaut à non
                "idLot" => $lotId,
                "startDate" => Carbon::now(),
                "endDate" => Carbon::now()->addDays(29)
            ]);


        }

        shuffle($ticketsComposed); //  mélange tous les tickets
        return $ticketsComposed;
    }


}
