@extends('admin.template')

@section('content')
<div class="card p-2  col-md-6 mx-auto  mt-1 mb-2">
    <h3 class="text-bold text-center">
        <a href="{{ url()->previous() }}"> <i class="fas fa-backward iconSize text-primary "></i> </a> Détail de la
        réclamation
    </h3>
</div>
<div class="card p-3 mt-3 col-md-6 m-auto">


    @if(session('successNotification'))
    <div class=" alert alert-success alert-dismissible col-md-6 mx-auto tex-center my-2">
        <button class="close" data-dismiss="alert" type="button">&times;</button>
        <p style="text-align: center;"> {{ session('successNotification') }}</p>
    </div>
    @endif
    @if(session('errorNotification'))
    <div class=" alert alert-danger col-md-6 mx-auto alert-dismissible tex-center  my-2">
        <button class="close" data-dismiss="alert" type="button">&times;</button>
        <p style="text-align: center;"> {{ session('errorNotification') }}</p>
    </div>
    @endif

    <form method="POST" action="{{route('updateReclamation',$reclamation->id )}}">
        @csrf
        <div class="form-group">
            <label for="name" class="col-form-label text-md-right">Nom et Prénom</label>
            <input id="name" type="text" value="{{ $reclamation->user->name }}" disabled
                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" autofocus>
        </div>

        <div class="form-group ">
            <label for="email" class="col-form-label text-md-right">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ $reclamation->user->email }}"
                   disabled>
        </div>


        <div class="form-group ">
            <label for="telephone" class="col-form-label text-md-right">Numéro de téléphone</label>
            <input id="telephone" type="text" class="form-control" disabled name="telephone"
                   value="{{ $reclamation->phone }}">
        </div>

        <div class="form-group">
            <label for="role" class="col-form-label text-md-right">Statu de la réclamation</label>
            <select id="role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" name="statut_reclamation">
                <option value="{{env('EN_ATTENTE')}}" @if( $reclamation->statut_reclamation==env("EN_ATTENTE") )selected @endif >en attente </option>
                <option value="{{env('LIVRER')}}" @if( $reclamation->statut_reclamation==env("LIVRER")) selected @endif>livrer</option>
            </select>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn bg-admin-base ">
                    <i class="fas fa-check"></i> Modifier le statut de la reclamation
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
