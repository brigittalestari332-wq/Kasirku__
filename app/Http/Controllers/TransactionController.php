<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $categoryId = $request->query('category_id');
        $transactions = Transaction::with('items')
            ->when($categoryId, fn ($query) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('category_id', $categoryId)))
            ->latest()
            ->get();

        return view('admin.layouts.wrapper', [
            'content' => 'admin.transaction.index',
            'transactions' => $transactions,
            'categories' => Category::orderBy('name')->get(),
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        return view('admin.layouts.wrapper', [
            'content' => 'admin.transaction.form',
            'products' => Product::with('category')->where('stock', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $transaction = DB::transaction(function () use ($data) {
            $products = Product::with('category')
                ->whereIn('id', array_column($data['items'], 'product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $total = 0;

            foreach ($data['items'] as $index => $item) {
                $product = $products->get($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Stok {$product->name} tersisa {$product->stock}.",
                    ]);
                }

                $subtotal = round($product->price * $item['quantity'], 2);
                $total += $subtotal;
                $items[] = [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'product_name' => $product->name,
                    'category_name' => $product->category->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $paidAmount = round($data['paid_amount'], 2);

            if ($paidAmount < $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Jumlah dibayar kurang dari total transaksi.',
                ]);
            }

            $transaction = Transaction::create([
                'transaction_number' => 'TRX-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
                'customer_name' => $data['customer_name'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => round($paidAmount - $total, 2),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $transaction->items()->create($item);
                Product::whereKey($item['product_id'])->decrement('stock', $item['quantity']);
            }

            return $transaction;
        });

        return redirect()->route('transaction.show', $transaction)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load('items');

        return view('admin.layouts.wrapper', [
            'content' => 'admin.transaction.show',
            'transaction' => $transaction,
        ]);
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load('items');

        return view('admin.transaction.receipt', [
            'transaction' => $transaction,
        ]);
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }

            $transaction->delete();
        });

        return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
    }
}
