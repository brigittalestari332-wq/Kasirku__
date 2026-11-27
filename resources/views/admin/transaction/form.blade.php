<div class="container-fluid pt-2">
    <div class="card">
        <div class="card-body">
            <h4><b>Tambah Transaksi</b></h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="alert alert-warning">
                    Belum ada produk dengan stok tersedia. <a href="{{ route('product.create') }}">Tambahkan produk terlebih dahulu.</a>
                </div>
            @else
                @php($items = old('items', [['product_id' => '', 'quantity' => 1]]))

                <form action="{{ route('transaction.store') }}" method="POST">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="transaction_date"><b>Tanggal Transaksi</b></label>
                            <input id="transaction_date" type="date" class="form-control" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                        </div>

                        <div class="form-group col-md-8">
                            <label for="customer_name"><b>Nama Pelanggan</b></label>
                            <input id="customer_name" type="text" class="form-control" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nama pelanggan (opsional)">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Produk</th>
                                    <th style="width: 20%">Kategori</th>
                                    <th style="width: 15%">Harga</th>
                                    <th style="width: 15%">Jumlah</th>
                                    <th style="width: 15%">Subtotal</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="transaction-items">
                                @foreach ($items as $index => $item)
                                    @php($selectedProduct = $products->firstWhere('id', $item['product_id'] ?? null))
                                    <tr data-item-row>
                                        <td>
                                            <select name="items[{{ $index }}][product_id]" class="form-control" data-product required>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-category="{{ $product->category->name }}" data-price="{{ number_format($product->price, 2, '.', '') }}" @selected(($item['product_id'] ?? '') == $product->id)>{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" data-category-display value="{{ $selectedProduct?->category?->name }}" placeholder="-" readonly>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.01" class="form-control" data-price-display value="{{ $selectedProduct ? number_format($selectedProduct->price, 2, '.', '') : '' }}" placeholder="-" readonly>
                                        </td>
                                        <td>
                                            <input type="number" min="1" step="1" class="form-control" name="items[{{ $index }}][quantity]" data-quantity value="{{ $item['quantity'] ?? 1 }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" data-subtotal value="Rp 0" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" data-remove-item>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th><input type="text" class="form-control font-weight-bold" data-grand-total value="Rp 0" readonly></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success mb-3" id="add-item">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="paid_amount"><b>Dibayar</b></label>
                            <input id="paid_amount" type="number" min="0" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" name="paid_amount" data-paid-amount value="{{ old('paid_amount') }}" placeholder="Jumlah dibayar" required>

                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="change_amount"><b>Kembalian</b></label>
                            <input id="change_amount" type="text" class="form-control font-weight-bold" data-change-amount value="Rp 0" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes"><b>Catatan</b></label>
                        <textarea id="notes" class="form-control" name="notes" rows="3" placeholder="Catatan transaksi (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </form>

                <template id="transaction-item-template">
                    <tr data-item-row>
                        <td>
                            <select name="items[][product_id]" class="form-control" data-product required>
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-category="{{ $product->category->name }}" data-price="{{ number_format($product->price, 2, '.', '') }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" data-category-display placeholder="-" readonly>
                        </td>
                        <td>
                            <input type="number" min="0" step="0.01" class="form-control" data-price-display placeholder="-" readonly>
                        </td>
                        <td>
                            <input type="number" min="1" step="1" class="form-control" name="items[][quantity]" data-quantity value="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" data-subtotal value="Rp 0" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" data-remove-item>
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            @endif
        </div>
    </div>
</div>

@if (!$products->isEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsContainer = document.getElementById('transaction-items');
            const template = document.getElementById('transaction-item-template');
            const addButton = document.getElementById('add-item');
            const grandTotal = document.querySelector('[data-grand-total]');
            const paidAmount = document.querySelector('[data-paid-amount]');
            const changeAmount = document.querySelector('[data-change-amount]');
            const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' });

            function calculateChange() {
                const total = parseFloat(grandTotal.dataset.amount) || 0;
                const paid = parseFloat(paidAmount.value) || 0;

                changeAmount.value = currency.format(Math.max(paid - total, 0));
            }

            function calculateTotal() {
                let total = 0;
                const rows = itemsContainer.querySelectorAll('[data-item-row]');

                rows.forEach(function (row) {
                    const quantity = parseFloat(row.querySelector('[data-quantity]').value) || 0;
                    const price = parseFloat(row.querySelector('[data-price-display]').value) || 0;
                    const subtotal = quantity * price;

                    row.querySelector('[data-subtotal]').value = currency.format(subtotal);
                    row.querySelector('[data-remove-item]').disabled = rows.length === 1;
                    total += subtotal;
                });

                grandTotal.value = currency.format(total);
                grandTotal.dataset.amount = total;
                calculateChange();
            }

            function syncProduct(select) {
                const option = select.options[select.selectedIndex];
                const row = select.closest('[data-item-row]');

                row.querySelector('[data-category-display]').value = option && option.value ? option.dataset.category : '';
                row.querySelector('[data-price-display]').value = option && option.value ? option.dataset.price : '';
                calculateTotal();
            }

            itemsContainer.addEventListener('change', function (event) {
                if (event.target.matches('[data-product]')) {
                    syncProduct(event.target);
                }
            });

            itemsContainer.addEventListener('input', function (event) {
                if (event.target.matches('[data-quantity]')) {
                    calculateTotal();
                }
            });

            itemsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-remove-item]');

                if (!removeButton || itemsContainer.querySelectorAll('[data-item-row]').length === 1) {
                    return;
                }

                removeButton.closest('[data-item-row]').remove();
                calculateTotal();
            });

            addButton.addEventListener('click', function () {
                itemsContainer.appendChild(template.content.cloneNode(true));
                calculateTotal();
            });

            paidAmount.addEventListener('input', calculateChange);

            itemsContainer.querySelectorAll('[data-product]').forEach(syncProduct);
        });
    </script>
@endif
