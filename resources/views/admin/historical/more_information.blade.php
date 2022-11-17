
@extends('admin.template')

@section('content')
    <div class="card p-2  col-md-6 mx-auto  mt-1 mb-2">
        <h3 class="text-bold text-center">
                Détail de l'historique
        </h3>
    </div>
    <div class="card p-3 mt-3 col-md-6 m-auto">
        <div class="row">
            <div class="col-lg-8">
                <p> Numéro du ticket : <b> {{ $historical->getTicket->number }} </b> </p>
                <p> Numéro du gagnant : <b> {{ $historical->getUser->name }} </b>  </p>
                <p> Date de jeu :  <b> {{ \Carbon\Carbon::parse($historical->created_at)->format('d/m/Y')}}  </b></p>
                <p> Date du retrait  :  @if(!empty($historical->takenAt))  <b> {{ \Carbon\Carbon::parse($historical->takenAt)->format('d/m/Y') }} </b> @else <b> Lot n'est pas encore recupérer. </b>  @endif</p>
                <p> Lot Gagner : <b> {{ $historical->getTicket->getLot->libelle }} </b> </p>

            </div>
        </div>
        </form>
    </div>
@endsection
