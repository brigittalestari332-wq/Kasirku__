<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Struk {{ $transaction->transaction_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px 12px;
            color: #111;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .receipt-actions {
            width: 100%;
            max-width: 360px;
            margin: 0 auto 16px;
            display: flex;
            gap: 8px;
        }

        .receipt-actions a,
        .receipt-actions button {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #333;
            border-radius: 4px;
            color: #111;
            background: #fff;
            font: inherit;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .receipt-actions .print-button {
            color: #fff;
            background: #333;
        }

        .receipt {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .12);
        }

        .receipt-header {
            margin-bottom: 16px;
            text-align: center;
        }

        .receipt-header h1 {
            margin: 0 0 6px;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .receipt-header p {
            margin: 3px 0;
        }

        .receipt-divider {
            margin: 14px 0;
            border-top: 1px dashed #333;
        }

        .receipt-items {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-items th {
            padding-bottom: 8px;
            border-bottom: 1px dashed #333;
            font-size: 11px;
            text-align: right;
        }

        .receipt-items th:first-child,
        .receipt-items td:first-child {
            text-align: left;
        }

        .receipt-items td {
            padding: 9px 0;
            border-bottom: 1px dashed #ddd;
            vertical-align: top;
            text-align: right;
            white-space: nowrap;
        }

        .receipt-items .product-category {
            display: block;
            margin-top: 2px;
            color: #555;
            font-size: 11px;
            white-space: normal;
        }

        .receipt-totals {
            margin-top: 14px;
        }

        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 7px 0;
        }

        .receipt-total-row.grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #333;
            font-size: 15px;
            font-weight: bold;
        }

        .receipt-notes {
            margin: 14px 0 0;
            white-space: pre-line;
        }

        .receipt-footer {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            @page {
                size: auto;
                margin: 5mm;
            }

            body {
                padding: 0;
                background: #fff;
                font-size: 11px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .receipt {
                max-width: 80mm;
                padding: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 12px 8px;
            }

            .receipt {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-actions no-print">
        <a href="{{ route('transaction.show', $transaction) }}">Kembali</a>
        <button type="button" class="print-button" onclick="window.print()">Cetak Struk</button>
    </div>

    <main class="receipt">
        <header class="receipt-header">
            <h1>STRUK TRANSAKSI</h1>
            <p>{{ $transaction->transaction_number }}</p>
            <p>{{ $transaction->transaction_date->format('d/m/Y') }}</p>
        </header>

        <div class="receipt-divider"></div>

        <p><strong>Pelanggan:</strong> {{ $transaction->customer_name ?: '-' }}</p>

        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            <span class="product-category">{{ $item->category_name }}</span>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="receipt-totals">
            <div class="receipt-total-row grand-total">
                <span>Total</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Dibayar</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </section>

        @if ($transaction->notes)
            <p class="receipt-notes"><strong>Catatan:</strong><br>{{ $transaction->notes }}</p>
        @endif

        <footer class="receipt-footer">
            <div class="receipt-divider"></div>
            <p>Terima kasih telah berbelanja.</p>
        </footer>
    </main>
</body>
</html>
