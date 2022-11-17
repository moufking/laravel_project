<?php


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReclamationController;
use App\Http\Controllers\Admin\StatistiquesController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\HistoriqueController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Auth\LoginController::class,'showLoginForm']);

Auth::routes();

Route::middleware(['auth', 'admin'])->group( function(){
    Route::prefix("tickets")->group(function(){
        Route::get('liste-des-tickets', [TicketController::class, 'listTickets'])->name('listTickets');
        Route::get('generer-les-tickets', [TicketController::class, 'generateAndSaveTicketToDb'])->name('generateTicket');
        Route::get('/ticket_csv', [TicketController::class, 'exportCsv']);
    });

    /*Route::get('/statistiques-du-jeu', function (){
        return view('admin.stats.statistiques');
    }); */

    Route::get("/statistiques-du-jeu", [StatistiquesController::class, 'getStats'])->name('stats');

    Route::prefix("historical")->group(function(){
        Route::get('liste-des-lots', [HistoriqueController::class, 'historique'])->name('historical');
        Route::get('Voir-plus/{id}', [HistoriqueController::class, 'view_information'])->name('moreinfo');
        Route::post('filte-resultat/', [HistoriqueController::class, 'filter_historique'])->name('filter_historique');
        Route::post('lot-recuperer', [HistoriqueController::class, 'updateHistorique'])->name('update_historical');
        Route::get('/ticket_csv', [HistoriqueController::class, 'exportCsv']);
    });


    Route::prefix("reclamation")->group(function(){
        Route::get('liste-des-reclamation', [ReclamationController::class, 'index'])->name('liste-reclamation');
        Route::post('filte-resultat/', [ReclamationController::class, 'filter_reclaramation'])->name('filter_reclaramation');
        Route::get('Voir-plus/{id}', [ReclamationController::class, 'view_information'])->name('moreinfo_reclamation');
        Route::post('update-reclamation/{id_reclamation}',[ReclamationController::class,'updateStatutReclamation'])->name('updateReclamation');
        Route::get('/ticket_csv', [ReclamationController::class, 'exportCsv']);
    });

    //routes concernant la gestion des utilisateurs
    Route::prefix("users")->group(function(){
        Route::get('list',[AdminController::class, 'usersList'])->name('listeDesUtilisateurs');
        Route::get('profil/{id_user}',[AdminController::class, 'myProfil'])->name('myprofil');
        Route::get('lots/{id_user}',[AdminController::class, 'listeLots'])->name('lots');
        Route::post('update-profil/{id_user}',[AdminController::class,'updateMyProfil'])->name('updateMyProfil');
        Route::get('delete-user/{id_user}',[AdminController::class,'deleteUser'])->name('deleteUser');
        Route::get('/ticket_csv', [AdminController::class, 'exportCsv']);

    });
});
