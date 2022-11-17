
@extends('admin.template')

@section('page_level_css')
    <link rel="stylesheet" href="{{asset('css/datable.css')}}"/>
@endsection

@section('content')
    <div class="pt-2">
        <div class="bg-white p-2">
            <h3 class="text-bold text-center">
               Tous les tickets @if( count ($tickets) > 0) ( {{ count ($tickets) }} ) @endif
            </h3>
        </div>
    </div>

    @if( count ($tickets) == 0)
        <div class="d-flex justify-content-end">
            <a href="{{route("generateTicket")}}" class="100">
                <button class="btn mt-3 btn-admin-success" type="button" style="width: 228px">
                    Générer les tickets
                </button>
            </a>
        </div>
    @endif
<br>
        <div class="d-flex justify-content-end">
            <button class="100"
                data-href="ticket_csv" id="export" class="btn btn-success btn-sm" onclick="exportTasks(event.target);"
                class="btn mt-3 btn-admin-success" type="button" style="width: 228px">
                Exporter en csv
            </button>
        </div>

    <div class="bg-white p-3 mt-3">
        {{-- debut tableau --}}
        <table  class="display table table-bordered mt-5" id="listetypehabitats">
            <thead>
            <tr>
                <th class="itteration-width">N°</th>
                <th class="image-width">Lot</th>
                <th class="image-width">Déjà utilisé</th>
                <th class="image-width">Utilisé par</th>
                <th class="image-width">Date validité</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tickets as $ticket)
                <tr>
                    <td class="image-width">{{ $ticket->number }}</td>
                    <th class="image-width">{{ $ticket->getLot->libelle }}</th>
                    <td class="image-width"> @if($ticket->isUsed) Oui @else Non @endif</td>
                    <td class="image-width"> @if($ticket->idUser) {{ $ticket->getUser->name }} @else - @endif</td>
                    <td class="image-width">
                        {{ \Carbon\Carbon::parse($ticket->startDate)->format('d/m/Y')}}
                        -
                        {{ \Carbon\Carbon::parse($ticket->endDate)->format('d/m/Y')}}

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{-- fin tableau --}}


    </div>
@endsection

@section('optional_js')
    <script src="{{ asset('js/bootstrap-confirmation.min.js') }}"></script>
    <script src="{{ asset('js/datable.js') }}"></script>
    <script src="{{ asset('js/lang-all.js') }}"></script>
    <script>

        function exportTasks(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
            console.log(_url, "information")
        }

        $('document').ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle=confirmation]').confirmation({ rootSelector: '[data-toggle=confirmation]' });
        });

        //jquery datatables
        $('#listetypehabitats').DataTable({
            ordering: false,
            language: {
                processing:     "Traitement en cours...",
                search:         "Rechercher&nbsp;:",
                lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
                info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                infoPostFix:    "",
                loadingRecords: "Chargement en cours...",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "Aucune donnée disponible dans le tableau",
                paginate: {
                    first:      "Premier",
                    previous:   "Pr&eacute;c&eacute;dent",
                    next:       "Suivant",
                    last:       "Dernier"
                },
                 aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });

    </script>
@endsection
