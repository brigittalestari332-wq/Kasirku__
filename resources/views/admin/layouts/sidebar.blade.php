  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/admin/dashboard') }}" class="brand-link">
      <span class="brand-text"><b>Kasirku</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
     

     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-item">
            <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ request()->is('/') || request()->is('admin/dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-th-large"></i>
              <p>Dashboard</p>
            </a>
          </li>

          @if (auth()->user()->isAdmin())
              <li class="nav-item">
                <a href="{{ route('user.index') }}" class="nav-link @if(request()->routeIs('user.*')) active @endif">
                  <i class="nav-icon fas fa-users"></i>
                  <p>user</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('category.index') }}" class="nav-link @if(request()->routeIs('category.*')) active @endif">
                  <i class="nav-icon fas fa-tags"></i>
                  <p>Kategori Produk</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('product.index') }}" class="nav-link @if(request()->routeIs('product.*')) active @endif">
                  <i class="nav-icon fas fa-box"></i>
                  <p>Produk</p>
                </a>
              </li>
          @endif

          <li class="nav-item">
            <a href="{{ route('transaction.index') }}" class="nav-link @if(request()->routeIs('transaction.*')) active @endif">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>Transaksi</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <div class="content-wrapper">