<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\AccountStock;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = Product::with(['category', 'variants.accountStocks'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        $types = ProductType::cases();

        return view('admin.products.create', compact('categories', 'types'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ProductType::class)],
            'publisher' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'account_specs_raw' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'requires_user_id' => ['boolean'],
            'user_id_label' => ['nullable', 'string', 'max:50'],
            'requires_zone_id' => ['boolean'],
            'zone_id_label' => ['nullable', 'string', 'max:50'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.name' => ['required', 'string', 'max:255'],
            'variants.*.sku' => ['required', 'string', 'max:50', 'unique:product_variants,sku'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_type' => ['required', 'string', 'in:unlimited,auto_stock,manual'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.credentials' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Pilih kategori produk.',
            'variants.required' => 'Tambahkan minimal satu variasi / nominal harga.',
            'variants.*.name.required' => 'Nama variasi wajib diisi.',
            'variants.*.sku.required' => 'SKU / Kode variasi wajib diisi.',
            'variants.*.sku.unique' => 'SKU variasi sudah digunakan.',
            'variants.*.price.required' => 'Harga jual variasi wajib diisi.',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $photo) {
                $galleryPaths[] = $photo->store('products', 'public');
            }
        }

        if (empty($thumbnailPath) && ! empty($galleryPaths)) {
            $thumbnailPath = $galleryPaths[0];
        }

        $accountSpecs = $this->parseAccountSpecs($request->input('account_specs_raw'));

        DB::transaction(function () use ($validated, $request, $thumbnailPath, $galleryPaths, $accountSpecs) {
            $slug = Str::slug($validated['name']);
            $count = Product::withTrashed()->where('slug', 'LIKE', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-".($count + 1);
            }

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'type' => $validated['type'],
                'publisher' => $validated['publisher'] ?? null,
                'thumbnail' => $thumbnailPath,
                'gallery' => ! empty($galleryPaths) ? $galleryPaths : ($thumbnailPath ? [$thumbnailPath] : null),
                'account_specs' => $accountSpecs,
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'requires_user_id' => $request->boolean('requires_user_id', true),
                'user_id_label' => $validated['user_id_label'] ?? 'User ID',
                'requires_zone_id' => $request->boolean('requires_zone_id', false),
                'zone_id_label' => $validated['zone_id_label'] ?? 'Zone ID',
                'is_active' => true,
            ]);

            foreach ($validated['variants'] as $vData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $vData['name'],
                    'sku' => $vData['sku'],
                    'price' => $vData['price'],
                    'cost_price' => $vData['cost_price'] ?? 0,
                    'stock_type' => $vData['stock_type'],
                    'stock' => $vData['stock_type'] === 'unlimited' ? 0 : (int) ($vData['stock'] ?? 0),
                    'is_active' => true,
                ]);

                if (! empty($vData['credentials'])) {
                    $lines = preg_split('/\r\n|\r|\n|---/', (string) $vData['credentials']);
                    $credCount = 0;
                    foreach ($lines as $cred) {
                        $cred = trim($cred);
                        if (! empty($cred)) {
                            AccountStock::create([
                                'product_variant_id' => $variant->id,
                                'credentials' => $cred,
                                'is_used' => false,
                            ]);
                            $credCount++;
                        }
                    }
                    if ($credCount > 0 && $variant->stock_type === 'auto_stock') {
                        $variant->update(['stock' => $credCount]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk dan variasi berhasil ditambahkan!');
    }

    /**
     * Show form to edit product and manage variants.
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'variants.accountStocks']);
        $categories = Category::active()->get();
        $types = ProductType::cases();

        return view('admin.products.edit', compact('product', 'categories', 'types'));
    }

    /**
     * Update product details.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ProductType::class)],
            'publisher' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'account_specs_raw' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'requires_user_id' => ['boolean'],
            'user_id_label' => ['nullable', 'string', 'max:50'],
            'requires_zone_id' => ['boolean'],
            'zone_id_label' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $updateData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'publisher' => $validated['publisher'] ?? null,
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'requires_user_id' => $request->boolean('requires_user_id'),
            'user_id_label' => $validated['user_id_label'] ?? 'User ID',
            'requires_zone_id' => $request->boolean('requires_zone_id'),
            'zone_id_label' => $validated['zone_id_label'] ?? 'Zone ID',
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('thumbnail')) {
            $updateData['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = is_array($product->gallery) ? $product->gallery : [];
            foreach ($request->file('gallery') as $photo) {
                $galleryPaths[] = $photo->store('products', 'public');
            }
            $updateData['gallery'] = $galleryPaths;
        }

        // If thumbnail is empty and gallery exists, set thumbnail from gallery
        if (empty($product->thumbnail) && empty($updateData['thumbnail'])) {
            $currentGallery = $updateData['gallery'] ?? $product->gallery;
            if (is_array($currentGallery) && ! empty($currentGallery)) {
                $updateData['thumbnail'] = $currentGallery[0];
            }
        }

        if ($request->has('account_specs_raw')) {
            $updateData['account_specs'] = $this->parseAccountSpecs($request->input('account_specs_raw'));
        }

        $product->update($updateData);

        return back()->with('success', 'Informasi produk berhasil diperbarui!');
    }

    /**
     * Parse raw string specs into key-value array.
     *
     * @return array<string, string>|null
     */
    protected function parseAccountSpecs(?string $raw): ?array
    {
        if (blank($raw)) {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $specs = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ($key !== '') {
                    $specs[$key] = $value;
                }
            } else {
                $specs['Info '.count($specs) + 1] = $line;
            }
        }

        return ! empty($specs) ? $specs : null;
    }

    /**
     * Add a new variant to an existing product.
     */
    public function storeVariant(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:product_variants,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_type' => ['required', 'string', 'in:unlimited,auto_stock,manual'],
            'stock' => ['nullable', 'integer', 'min:0'],
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'stock_type' => $validated['stock_type'],
            'stock' => $validated['stock_type'] === 'unlimited' ? 0 : (int) $request->input('stock', 0),
            'is_active' => true,
        ]);

        return back()->with('success', 'Variasi nominal baru berhasil ditambahkan!');
    }

    /**
     * Add or update variant stock.
     */
    public function addStock(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'stock_to_add' => ['nullable', 'integer'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'credentials' => ['nullable', 'string'],
        ]);

        if ($request->filled('stock_to_add')) {
            $amount = (int) $request->input('stock_to_add');
            $variant->increment('stock', $amount);
            $newStock = $variant->fresh()->stock;

            return back()->with('success', "Berhasil menambahkan {$amount} stok untuk variasi {$variant->name}! Total stok sekarang: {$newStock}.");
        }

        if ($request->has('stock') && $request->input('stock') !== null) {
            $variant->update(['stock' => max(0, (int) $request->input('stock'))]);

            return back()->with('success', "Stok variasi {$variant->name} berhasil diperbarui menjadi {$variant->stock}!");
        }

        if ($request->filled('credentials')) {
            $lines = preg_split('/\r\n|\r|\n|---/', (string) $validated['credentials']);
            $count = count(array_filter(array_map('trim', $lines)));
            if ($count > 0) {
                $variant->increment('stock', $count);

                return back()->with('success', "Berhasil menambahkan {$count} stok ke dalam variasi!");
            }
        }

        return back()->with('error', 'Masukkan jumlah stok yang ingin ditambahkan.');
    }

    /**
     * Remove the product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Remove a variant from a product.
     */
    public function destroyVariant(ProductVariant $variant): RedirectResponse
    {
        $name = $variant->name;
        $variant->delete();

        return back()->with('success', "Variasi {$name} berhasil dihapus!");
    }
}
