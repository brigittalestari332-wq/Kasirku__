<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4><b>{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</b></h4>

                    @if ($product->exists)
                        <form action="{{ route('product.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @method('PUT')
                    @else
                        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                    @endif
                        @csrf

                        <div class="form-group">
                            <label for="category_id"><b>Kategori Produk</b></label>
                            <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name"><b>Nama Produk</b></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" placeholder="Nama produk">

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="price"><b>Harga</b></label>
                                <input id="price" type="number" min="0" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}">

                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="stock"><b>Stok</b></label>
                                <input id="stock" type="number" min="0" step="1" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $product->stock) }}">

                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image"><b>Gambar Produk</b></label>
                            <input id="image" type="file" class="form-control-file @error('image') is-invalid @enderror" name="image" accept="image/jpeg,image/png,image/webp">

                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <small class="form-text text-muted">Format JPG, PNG, atau WebP. Maksimal 2 MB.</small>

                            @if ($product->image)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Gambar {{ $product->name }}" class="img-thumbnail" style="max-width: 180px; max-height: 180px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('product.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
