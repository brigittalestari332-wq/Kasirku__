  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">



      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Layar penuh">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

      <li class="nav-item user-menu d-none d-sm-block">
        <span class="user-chip">
          <span class="user-avatar">{{ strtoupper(substr(auth()->user()->isAdmin() ? auth()->user()->name : 'AdminLTE3', 0, 1)) }}</span>
          <span class="user-meta">
            <span class="user-name">{{ auth()->user()->isAdmin() ? auth()->user()->name : 'AdminLTE3' }}</span>
            <span class="user-role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}</span>
          </span>
        </span>
      </li>

      <li class="nav-item">
        <a href="{{ url('/logout') }}" class="nav-link header-logout" title="Keluar">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->