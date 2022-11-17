
<!-- Sidebar -->
<div class="sidebar bg-defoult leftbar left h-100">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="http://via.placeholder.com/160x160" class="rounded-circle" alt="User Image">
        </div>
        <div class="ml-3 pull-left info">

            @auth
                <p>TheTipTop Administration</p>
                <a href=""  title="accéder à mon profile"><i class="fa fa-circle text-success"></i>
                    {{ Auth::user()->name }}
                    @if( Auth::user()->role==env("EMPLOYEE_ROLE") )
                       ( Employé )
                    @elseif( Auth::user()->role==env('ADMIN_ROLE') )
                        ( Admin )
                    @endif
                </a>
            @else
                <p class="mt-2">TheTipTop Administration</p>
            @endauth

        </div>
    </div>

    @auth
    <ul class="list-sidebar bg-defoult">
        <li> <a href="https://prod-admin.dsp-archiwebo20-mt-ma-ca-fd.fr/"><i class="fas fa-home"></i> <span class="nav-label">Aller sur thétiptop</span></a> </li>

        <li class="active"> <a href="#" data-toggle="collapse" data-target="#dashboard" class="collapsed" > <i class="fas fa-users"></i> Utilisateurs </span> <span class="fa fa-chevron-left pull-right"></span> </a>
            <ul class="sub-menu collapse" id="dashboard">
                <li>
                    <a href="{{ route('register') }}">
                        <i class="fas fa-plus-circle"></i> Nouvel utilisateur - Admin
                    </a>
                </li>
                <li>
                    <a href="{{ route('listeDesUtilisateurs') }}">
                        <i class="fas fa-plus-circle"></i> Tous les utilisateurs
                    </a>
                </li>

            </ul>
        </li>

        <li>
            <a href="{{ route('listTickets') }}">
                 <i class="fas fa-list "></i> Tous les tickets
            </a>
        </li>

        <li>
            <a href="{{ route('historical') }}">
                 <i class="fas fa-list "></i> Historique des gains
            </a>
        </li>

        <li>
            <a href="{{ route('liste-reclamation') }}">
                <i class="fas fa-list "></i> Liste des reclamations
            </a>
        </li>

        <li >
            <a href="{{ route('stats') }}">
                <i class="fas fa-list "></i> Statistiques
            </a>
        </li>

        <li class="mt-5">
            <a class=" text-light text-bold"
               href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fas fa-power-off text-danger"></i> Déconnexion </a>
        </li>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: one;">
            {{ csrf_field() }}
        </form>
    </ul>
    @endauth
</div>
<!-- /#sidebar-wrapper -->



