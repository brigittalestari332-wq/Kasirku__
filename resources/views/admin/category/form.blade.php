<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4><b>{{ $category->exists ? 'Edit Kategori Produk' : 'Tambah Kategori Produk' }}</b></h4>

                    @if ($category->exists)
                        <form action="{{ route('category.update', $category) }}" method="POST">
                            @method('PUT')
                    @else
                        <form action="{{ route('category.store') }}" method="POST">
                    @endif
                        @csrf

                        <div class="form-group">
                            <label for="name"><b>Nama Kategori</b></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category->name) }}" placeholder="Contoh: Makanan">

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="stock"><b>Stok</b></label>
                            <input id="stock" type="number" min="0" step="1" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $category->stock ?? 0) }}" placeholder="Jumlah stok">

                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <a href="{{ route('category.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
