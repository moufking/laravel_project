<?php

namespace App\Providers;


use App\Charts\JeuParJourChart;
use App\Charts\LotCharts;
use App\Charts\TicketChart;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use ConsoleTVs\Charts\Registrar as Charts;
use App\Charts\UserChart;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        If (env('APP_ENV') !== 'local' and env('APP_ENV') !== 'testing') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Charts $charts)
    {
        $charts->register([
            TicketChart::class,
            JeuParJourChart::class,
            LotCharts::class,
            UserChart::class
        ]);
    }
}
