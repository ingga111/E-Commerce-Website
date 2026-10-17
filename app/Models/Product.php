<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::deleted(function (Product $product) {
            $product->variants()->delete();
        });

        static::restored(function (Product $product) {
            $product->variants()->restore();
        });
    }

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'type',
        'publisher',
        'thumbnail',
        'banner',
        'gallery',
        'description',
        'instructions',
        'account_specs',
        'requires_user_id',
        'user_id_label',
        'requires_zone_id',
        'zone_id_label',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'gallery' => 'array',
            'account_specs' => 'array',
            'requires_user_id' => 'boolean',
            'requires_zone_id' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get array of image URLs for gallery.
     *
     * @return array<int, string>
     */
    public function getGalleryUrls(): array
    {
        $urls = [];

        if (! empty($this->thumbnail)) {
            $urls[] = $this->resolveImageUrl($this->thumbnail);
        }

        if (is_array($this->gallery)) {
            foreach ($this->gallery as $item) {
                if (is_string($item) && $item !== '') {
                    $resolved = $this->resolveImageUrl($item);
                    if (! in_array($resolved, $urls, true)) {
                        $urls[] = $resolved;
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * Get primary thumbnail URL.
     */
    public function getThumbnailUrl(): ?string
    {
        if (! empty($this->thumbnail)) {
            return $this->resolveImageUrl($this->thumbnail);
        }

        $gallery = $this->getGalleryUrls();

        return $gallery[0] ?? null;
    }

    /**
     * Resolve image path or URL.
     */
    protected function resolveImageUrl(string $path): string
    {
        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'assets/') || str_starts_with($path, 'build/')) {
            return asset($path);
        }

        if (str_starts_with($path, '/')) {
            $clean = ltrim($path, '/');
            if (str_starts_with($clean, 'images/') || str_starts_with($clean, 'assets/')) {
                return asset($clean);
            }

            return asset('storage/'.$clean);
        }

        // Uploaded files stored on public disk (e.g. products/xxx.jpg)
        return asset('storage/'.$path);
    }

    /**
     * Category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Variations or denominations of the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    /**
     * Active variants only.
     */
    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    /**
     * Check if product is Game Top-up.
     */
    public function isGame(): bool
    {
        return $this->type === ProductType::Game;
    }

    /**
     * Check if product is Premium Account.
     */
    public function isAccount(): bool
    {
        return $this->type === ProductType::Account;
    }

    /**
     * Check if product is Game Account (Jual Beli Akun).
     */
    public function isGameAccount(): bool
    {
        return $this->type === ProductType::GameAccount;
    }

    /**
     * Check if product is Joki Rank Game.
     */
    public function isJoki(): bool
    {
        return $this->type === ProductType::Joki;
    }

    /**
     * Scope active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type (game or account).
     */
    public function scopeOfType(Builder $query, ProductType|string $type): Builder
    {
        $value = $type instanceof ProductType ? $type->value : $type;

        return $query->where('type', $value);
    }
}
