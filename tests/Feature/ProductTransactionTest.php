<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_category_and_product_can_be_created_and_displayed(): void
    {
        Storage::fake('public');

        $response = $this->post(route('category.store'), [
            'name' => 'Makanan',
            'stock' => 25,
        ]);

        $response->assertRedirect(route('category.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Makanan', 'stock' => 25]);

        $this->get(route('category.create'))
            ->assertOk()
            ->assertSee('Stok')
            ->assertDontSee('Deskripsi');

        $this->get(route('product.create'))
            ->assertOk()
            ->assertDontSee('SKU');

        $category = Category::where('name', 'Makanan')->firstOrFail();

        $this->post(route('product.store'), [
            'category_id' => $category->id,
            'name' => 'Roti',
            'price' => 15000,
            'stock' => 10,
            'image' => UploadedFile::fake()->createWithContent(
                'roti.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
            ),
        ])->assertRedirect(route('product.index'));

        $product = Product::where('name', 'Roti')->firstOrFail();

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'Roti',
            'image' => $product->image,
        ]);
        Storage::disk('public')->assertExists($product->image);

        $this->get(route('product.index'))
            ->assertOk()
            ->assertSee('Roti')
            ->assertSee('Makanan')
            ->assertSee(asset('storage/'.$product->image), false);
    }

    public function test_transaction_stores_product_category_and_reduces_stock(): void
    {
        $category = Category::create(['name' => 'Minuman']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Air Mineral',
            'price' => 5000,
            'stock' => 8,
        ]);

        $this->get(route('transaction.create'))
            ->assertOk()
            ->assertSee('Dibayar')
            ->assertSee('Kembalian');

        $response = $this->post(route('transaction.store'), [
            'customer_name' => 'Budi',
            'transaction_date' => now()->toDateString(),
            'paid_amount' => 20000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        $transaction = Transaction::firstOrFail();
        $response->assertRedirect(route('transaction.show', $transaction));

        $this->assertDatabaseHas('transaction_items', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'category_id' => $category->id,
            'category_name' => 'Minuman',
            'quantity' => 3,
            'subtotal' => 15000,
        ]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'total' => 15000,
            'paid_amount' => 20000,
            'change_amount' => 5000,
        ]);
        $this->assertSame(5, $product->fresh()->stock);

        $this->get(route('transaction.show', $transaction))
            ->assertOk()
            ->assertSee('Air Mineral')
            ->assertSee('Minuman')
            ->assertSee('Dibayar')
            ->assertSee('Kembalian');

        $this->get(route('transaction.receipt', $transaction))
            ->assertOk()
            ->assertSee('STRUK TRANSAKSI')
            ->assertSee($transaction->transaction_number)
            ->assertSee('Air Mineral')
            ->assertSee('Minuman')
            ->assertSee('Dibayar')
            ->assertSee('Kembalian');

        $this->get(route('transaction.index'))->assertSee(route('transaction.receipt', $transaction));
    }

    public function test_transaction_rejects_insufficient_stock_without_changing_data(): void
    {
        $category = Category::create(['name' => 'Snack']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Keripik',
            'price' => 10000,
            'stock' => 1,
        ]);

        $this->post(route('transaction.store'), [
            'transaction_date' => now()->toDateString(),
            'paid_amount' => 20000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ])->assertSessionHasErrors('items.0.quantity');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_transaction_rejects_payment_below_total(): void
    {
        $category = Category::create(['name' => 'Makanan']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Nasi',
            'price' => 10000,
            'stock' => 5,
        ]);

        $this->post(route('transaction.store'), [
            'transaction_date' => now()->toDateString(),
            'paid_amount' => 5000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ])->assertSessionHasErrors('paid_amount');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_transactions_can_be_filtered_by_category_and_deletion_restores_stock(): void
    {
        $foodCategory = Category::create(['name' => 'Makanan']);
        $drinkCategory = Category::create(['name' => 'Minuman']);
        $food = Product::create([
            'category_id' => $foodCategory->id,
            'name' => 'Nasi Goreng',
            'price' => 20000,
            'stock' => 5,
        ]);
        $drink = Product::create([
            'category_id' => $drinkCategory->id,
            'name' => 'Es Teh',
            'price' => 5000,
            'stock' => 5,
        ]);

        foreach ([$food, $drink] as $product) {
            $this->post(route('transaction.store'), [
                'transaction_date' => now()->toDateString(),
                'paid_amount' => 50000,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ])->assertSessionHasNoErrors();
        }

        $foodTransaction = Transaction::whereHas('items', fn ($query) => $query->where('product_id', $food->id))->firstOrFail();
        $drinkTransaction = Transaction::whereHas('items', fn ($query) => $query->where('product_id', $drink->id))->firstOrFail();

        $this->get(route('transaction.index', ['category_id' => $foodCategory->id]))
            ->assertOk()
            ->assertSee($foodTransaction->transaction_number)
            ->assertDontSee($drinkTransaction->transaction_number);

        $this->delete(route('transaction.destroy', $foodTransaction))
            ->assertRedirect(route('transaction.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $foodTransaction->id]);
        $this->assertSame(5, $food->fresh()->stock);
        $this->assertSame(4, $drink->fresh()->stock);
    }

    public function test_used_category_and_product_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'Alat Tulis']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Buku',
            'price' => 10000,
            'stock' => 5,
        ]);

        $this->delete(route('category.destroy', $category))
            ->assertRedirect(route('category.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        $this->post(route('transaction.store'), [
            'transaction_date' => now()->toDateString(),
            'paid_amount' => 10000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ])->assertSessionHasNoErrors();

        $this->delete(route('product.destroy', $product))
            ->assertRedirect(route('product.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
