<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.html">
              <span class="align-middle">Sistem Informasi Fasilitas dan Targeting</span>
        </a>

        <ul class="sidebar-nav">
            {{-- <li class="sidebar-header">
                Settings
            </li> --}}

            <li class="sidebar-item {{ (request()->is('/')) ? 'active' : '' }}">
                <a class="sidebar-link" href="/">
                      <i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-item {{ (request()->is('scoring*')) ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ url('/scoring') }}">
                    <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Skoring</span>
                </a>
            </li>
            <li class="sidebar-item {{ (request()->is('kawasan_berikat*')) ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ url('/kawasan_berikat') }}">
                    <i class="align-middle" data-feather="hexagon"></i> <span class="align-middle">Kawasan Berikat</span>
                </a>
            </li>
            <li class="sidebar-item {{ (request()->is('users*')) ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ url('/users') }}">
                    <i class="align-middle" data-feather="users"></i> <span class="align-middle">Pengguna</span>
                </a>
            </li>

        

           

          
        {{-- <div class="sidebar-cta">
            <div class="sidebar-cta-content">
                <strong class="d-inline-block mb-2">Upgrade to Pro</strong>
                <div class="mb-3 text-sm">
                    Are you looking for more components? Check out our premium version.
                </div>
                <div class="d-grid">
                    <a href="upgrade-to-pro.html" class="btn btn-primary">Upgrade to Pro</a>
                </div>
            </div>
        </div> --}}
    </div>
</nav>