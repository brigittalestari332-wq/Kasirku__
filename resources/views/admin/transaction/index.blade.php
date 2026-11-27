<div class="container-fluid pt-2">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Transaksi</h4>
                <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Transaksi
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('transaction.index') }}" method="GET" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="category_id" class="mr-2"><b>Filter Kategori</b></label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kategori Produk</th>
                            <th>Total</th>
                            <th>Dibayar</th>
                            <th>Kembalian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->transaction_number }}</td>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->customer_name ?: '-' }}</td>
                                <td><span class="badge badge-soft">{{ $transaction->items->pluck('category_name')->unique()->implode(', ') }}</span></td>
                                <td class="font-weight-bold text-nowrap">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td class="text-nowrap">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
                                <td class="font-weight-bold text-nowrap">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('transaction.show', $transaction) }}" class="btn btn-info btn-sm" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transaction.receipt', $transaction) }}" class="btn btn-success btn-sm ml-1" target="_blank" title="Struk">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="{{ route('transaction.destroy', $transaction) }}" method="POST" class="ml-1" onsubmit="return confirm('Hapus transaksi ini dan kembalikan stok produk?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
