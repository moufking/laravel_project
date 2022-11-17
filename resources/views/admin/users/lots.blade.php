
@extends('admin.template')

@section('page_level_css')
<link rel="stylesheet" href="{{asset('css/datable.css')}}"/>
@endsection

@section('content')
    <?php
        $histories = $user ->getHistorisqueDeGain;
    ?>
<div class="pt-2">
    <div class="bg-white p-2">
        <h3 class="text-bold text-center">
            Liste des lots gagnés par {{$user->name}}
        </h3>
    </div>
</div>
<div class="bg-white p-3 mt-3">
    {{-- debut tableau --}}

    <div class="container">
        <form action="{{ route('filter_historique') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm">
                    <label for="">Date de debut</label>
                    <input  placeholder= "Date de debut" type="date" class="form-control" name="date_start" value="" required>

                </div>
                <div class="col-sm">
                    <label for="">Date de fin</label>
                    <input  placeholder= "Date de fin" type="date" class="form-control" name="date_end" value="" required>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-6">
                    <button type="submit" class="btn bg-admin-base ">
                        <i class="fas fa-search"></i> Filter le resultat
                    </button>

                </div>
            </div>
        </form>
    </div>

    <br><br>
    <table  class="display table table-bordered mt-5" id="listetypehabitats">
        <thead>
        <tr>
            <th class="itteration-width">N° du ticket</th>
            <th class="image-width">Lot gagné</th>
            <th class="image-width">Date de jeu </th>
            <th class="image-width">Lot déja récupéré <b>(oui ou non)</b></th>
            <th class="image-width">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($histories as $histories)
        <tr>

            <td class="image-width">{{ $histories->getTicket->number }}</td>
            <th class="image-width">{{ $histories->getTicket->getLot->libelle }}</th>
            <td class="image-width">
                {{ \Carbon\Carbon::parse($histories->startDate)->format('d/m/Y')}}
            </td>
            <td class="image-width">@if (!empty($histories->takenAt)) oui @else non @endif</td>

            <td class="image-width">
                <form action="{{ route('update_historical')}}" method="POST" >
                    @csrf
                    <input type="hidden" value="{{ $histories->id }}" name="historical_id">

                    <button type="submit" @if(!empty($histories->takenAt)) disabled @endif class="btn bg-admin-base ">
                        <i class="fas fa-check"></i> marquer comme recupérer
                    </button>
                </form>
                <a class="btn btn-success" href="{{route('moreinfo',$histories->id)}}"> Voir plus d'informations</a>
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
