<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerateTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $user = User::where('email',env('ADMIN_EMAIL'))->first();
        Auth::login($user, false);

        $request = Request::create(route("generateTicket"), 'GET');
        $response = app()->handle($request);
        $responsebody = json_decode($response->getContent(), true);

        Auth::logout();

        $this->info('Liste des Tickets générer avec succès');

    }
}
