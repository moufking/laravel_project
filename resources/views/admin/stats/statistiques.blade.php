
@extends('admin.template')

@section('page_level_css')
    <link rel="stylesheet" href="{{asset('css/datable.css')}}"/>
@endsection

@section('content')
    <div class="pt-2">
        <div class="bg-white p-2">
            <h3 class="text-bold text-center">
                Statistiques du jeu concours
            </h3>
        </div>
    </div>

    <div class="row bg-white p-3 mt-2 mx-1">
        <div class="col-md-4  ">
            <div id="ticketChart" style="height: 300px;"></div>
        </div>

        <div class="col-md-4">
            <div  id="lotChart" style="height: 300px;"></div>
        </div>

        <div class="col-md-4">
            <div  id="userChart" style="height: 300px;"></div>
        </div>
    </div>
    <div class="bg-white p-3 mt-2" id="jeuParJourChart" style="height: 300px;"></div>
@endsection


@section('optional_js')
    <script src="https://unpkg.com/chart.js@2.9.3/dist/Chart.min.js"></script>
    <script src="https://unpkg.com/@chartisan/chartjs@^2.1.0/dist/chartisan_chartjs.umd.js"></script>
    <script>
        const ticketChart = new Chartisan({
            el: '#ticketChart',
            url: "@chart('ticketChart')",
            hooks: new ChartisanHooks()
                        .title('Statistiques des tickets')
                        .datasets("doughnut")
                        .pieColors(),
        });
        const jeuParJourChart = new Chartisan({
            el: '#jeuParJourChart',
            url: "@chart('jeuParJourChart')",
            hooks: new ChartisanHooks()
                    .colors(['green'])
        });

        const lotChart = new Chartisan({
            el: '#lotChart',
            url: "@chart('lotChart')",
            hooks: new ChartisanHooks()
                .title('Statistiques des lots gagnés')
                .datasets("doughnut")
                .pieColors(),
        });

        const userChart = new Chartisan({
            el: '#userChart',
            url: "@chart('userChart')",
            hooks: new ChartisanHooks()
                        .title('Statistiques des participants')
                        .datasets("doughnut")
                        .pieColors(),
        });
    </script>
@endsection
