<div class="container-fluid mt-2 dashboard-welcome">
    <div class="alert alert-success d-flex align-items-center">
        <i class="fas fa-heart mr-3" aria-hidden="true"></i>
        <div>
            <strong>Hallo, {{ auth()->user()->name }}!</strong><br>
            <span>Selamat datang di halaman dashboard.</span>
        </div>
    </div>
</div>

