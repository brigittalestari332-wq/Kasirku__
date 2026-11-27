<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">
                    <h4><b>Tambah Data</b></h4>

                    @isset($user)
                        <form action="/admin/user/{{$user->id}}" method="POST">
                            @method('put')
                    @else
                    <!-- <form dari bawah pindah ke atas -->
                    <form action="/admin/user" method="POST">
                    @endisset

                    
                        @csrf
                        <div class="form-group">
                            <label for="name"><b>Nama Lengkap</b></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama Lengkap" value="{{ old('name', $user->name ?? '') }}">

                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email"><b>Email</b></label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email', $user->email ?? '') }}">

                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role"><b>Role</b></label>
                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror">
                                <option value="user" @selected(old('role', $user->role ?? 'user') === 'user')>User</option>
                                <option value="admin" @selected(old('role', $user->role ?? 'user') === 'admin')>Admin</option>
                            </select>

                            @error('role')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password"><b>Password</b></label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password">

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="re_password"><b>Konfirmasi Password</b></label>
                            <input id="re_password" type="password" class="form-control @error('re_password') is-invalid @enderror" name="re_password" placeholder="Konfirmasi Password">

                            @error('re_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <a href="/admin/user" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>