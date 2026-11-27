<div class="container-fluid pt-2">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Detail Transaksi <small>{{ $transaction->transaction_number }}</small></h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <b>Tanggal</b><br>
                    {{ $transaction->transaction_date->format('d/m/Y') }}
                </div>
                <div class="col-md-3">
                    <b>Pelanggan</b><br>
                    {{ $transaction->customer_name ?: '-' }}
                </div>
                <div class="col-md-6">
                    <b>Catatan</b><br>
                    {{ $transaction->notes ?: '-' }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td><span class="badge badge-info">{{ $item->category_name }}</span></td>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total</th>
                            <th>Rp {{ number_format($transaction->total, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Dibayar</th>
                            <th>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Kembalian</th>
                            <th>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <a href="{{ route('transaction.receipt', $transaction) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-print"></i> Lihat Struk
            </a>
            <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
