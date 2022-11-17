<!doctype html>

<html lang="en">
    <head>
        {{--debut balise meta --}}
        @include('loadMeta')
        {{-- fin balise meta --}}

        {{--debut fichier css --}}
        @include('loadCss')
        <style>
            .dropdown-menu {
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 1000;
                display: none;
                float: left;
                min-width: 10rem;
                padding: 0.5rem 0;
                margin: 0.125rem 0 0;
                font-size: 1rem;
                text-align: left;
                list-style: none;
                background-clip: padding-box;
                border: 1px solid rgba(0, 0, 0, 0.15);
                border-radius: 0.25rem;
            }
        </style>
        {{--fin fichier css --}}

        <title>@yield('titre')</title>
    </head>


    <body>
        <div class="row h-100" >
           <div class="col-md-3 col-lg-2">
               <div  style="position: relative !important">
                   @include('admin.left_menu')
               </div>
           </div>

            <div class="col-9  col-lg-10 p-3 pr-5 light ">
                @yield('content')
            </div>
        </div>


        {{--debut fichiers javascript --}}
        @include('loadJs')
        {{-- fin fichier javascript--}}
    </body>
</html>
