<div class="d-flex justify-content-start px-5 pt-2">
    <button class="navbar-toggler navbar-toggler-open" type="button">
        <span class="mdi mdi-menu mdi-36px" style="color: #0f172a;"></span>
    </button>
</div>

<aside class="show sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
    id="navbarNav">
    <div class="sidenav-header d-flex justify-content-around  align-items-center">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="ms-3 navbar-brand d-flex justify-content-start align-items-center" href="/" style="margin:0; padding:0;">
            <i class="bi bi-building" style="color: #fff; font-size: 26px;" alt="main_logo"></i> &nbsp;&nbsp;&nbsp;
            <h3 class="mt-2 font-weight-bold" style="color: #fff; margin:0; padding:0;">Decameron</h3>
        </a>
        <button class="navbar-toggler navbar-toggler-close btn shadow-none" type="button">
            <span class="mdi mdi-close-thick" style="color:#fff;"></span>
        </button>

    </div>
    <hr class="horizontal mt-0" style="color: white !important;">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @can('hotel_management')
            <li class="nav-item" style="color: white !important;">
                <a class="nav-link d-flex align-items-center {{ Request::is('/') ? 'active' : '' }}" href="/">
                    <div class="rounded p-2 me-2 bg-light">
                        <i class="bi bi-house"
                            style="background-color: inherit; border-radius: inherit; color:#344767 !important"></i>
                    </div>
                    <span class="nav-link-text">
                        Hoteles
                    </span>
                </a>
            </li>
            @endcan
            @can('manage_hotel_room')
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('hotel_rooms') ? 'active' : '' }}"
                    href="/hotel_rooms">
                    <div class="rounded p-2 me-2 bg-light">
                        <i class="bi bi-building"
                            style="background-color: inherit; border-radius: inherit; color:#344767 !important"></i>
                    </div>
                    <span class="nav-link-text">
                        Habitaciones
                    </span>
                </a>
            </li>
            @endcan
            @can('user_management')
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('users') ? 'active' : '' }}" href="/users">
                    <div class="rounded p-2 me-2 bg-light">
                        <i class="bi bi-person-fill"
                            style="background-color: inherit; border-radius: inherit; color:#344767 !important"></i>
                    </div>
                    <span class="nav-link-text">
                        Usuarios
                    </span>
                </a>
            </li>
            @endcan
        </ul>
    </div>
    <div class="mx-auto"
        style="position: absolute; bottom:0; width: 200px; left:24px;">
        <a class="btn bg-gradient-light mt-3 w-100" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>



<main class="app-body mt-2">
    @yield('content')
</main>

<style>
    .app-body {
        margin-left: 270px;
        padding: 0 10px;
    }

    @media (max-width: 768px) {
        .navbar-toggler {
            display: block !important;
        }

        #navbarNav {
            display: none;
        }

        .app-body {
            margin-left: 0;
        }

    }

    .app-body.menu-open {
        margin-left: 270px;
    }

    /* Cuando el menú está cerrado */
    .app-body.menu-closed {
        margin-left: 0;
    }

    @media (max-width: 992px) {
        .boton_cerrar {
            display: flex;
            flex-direction: column
        }
    }

    .navbar-toggler-open {
        display: none;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var navbarTogglerOpen = document.querySelector(".navbar-toggler-open");
        var navbarTogglerClose = document.querySelector(".navbar-toggler-close");
        var navbarCollapse = document.querySelector("#navbarNav");
        var appBody = document.querySelector(".app-body");

        navbarTogglerOpen.addEventListener("click", toggleMenu);
        navbarTogglerClose.addEventListener("click", toggleMenu);

        function toggleMenu() {
            appBody.classList.toggle("menu-open");
            appBody.classList.toggle("menu-closed");

            if (navbarCollapse.classList.contains("show")) {
                navbarCollapse.classList.remove("show");
                navbarCollapse.style.display = "none";
                navbarTogglerOpen.style.display = "block";
            } else {
                navbarCollapse.classList.toggle("show");
                navbarCollapse.style.display = "block";
                navbarTogglerOpen.style.display = "none";

            }
        }
    });
</script>