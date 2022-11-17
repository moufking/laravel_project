
@extends('admin.template')

@section('page_level_css')
<link rel="stylesheet" href="{{asset('css/datable.css')}}"/>
@endsection

@section('content')
<div class="pt-2">
    <div class="bg-white p-2">
        <h3 class="text-bold text-center">
            Tous les lots @if( count ($reclamations) > 0) ( {{ count ($reclamations) }} ) @endif Test
        </h3>
    </div>
</div>
<div class="bg-white p-3 mt-3">
    {{-- debut tableau --}}

    <div class="container">
        @if(!empty(Session::get('errorNotification')))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! \Session::get('errorNotification') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <h3>Chercher les dates limites de livraison.</h3>
        <form action="{{ route('filter_reclaramation') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm">
                    <label for="">Date de debut</label>
                    <input  placeholder= "Date de debut" type="date" class="form-control" name="date_start" value="" >

                </div>
                <div class="col-sm">
                    <label for="">Date de fin</label>
                    <input  placeholder= "Date de fin" type="date" class="form-control" name="date_end" value="">
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

    <div class="d-flex justify-content-end">
        <button class="100"
                data-href="ticket_csv" id="export" class="btn btn-success btn-sm" onclick="exportTasks(event.target);"
                class="btn mt-3 btn-admin-success" type="button" style="width: 228px">
            Exporter en csv
        </button>
    </div>

    <br><br>
    <table  class="display table table-bordered mt-5" id="listetypehabitats">
        <thead>
        <tr>
            <th class="itteration-width">Nom du client </th>
            <th class="image-width">Lieu de livraison</th>
            <th class="image-width">Numéro de téléphone</th>
            <th class="image-width">Etat de la réclamation</th>
            <th class="image-width">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($reclamations as $reclamation)

        <tr>
            <!--"","","date_livraison","statut_reclamation" -->
            <td> {{$reclamation->user->name}}</td>
            <td> {{$reclamation->lieu_livraison}}</td>
            <td> {{$reclamation->phone}}</td>
            <td>
                <span class="badge badge-secondary">
                    {{$reclamation->statut_reclamation}}
                </span>
            </td>
            <td>
                <a href="{{route('moreinfo_reclamation', $reclamation->id)}} data-toggle="tooltip" data-placement="top" title="Voir plus"><i class="fas fa-eye iconSize text-primary" ></i></a> &nbsp;
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
