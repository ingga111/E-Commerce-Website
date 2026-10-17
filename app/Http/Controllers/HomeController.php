<?php

namespace App\Http\Controllers;

use App\Enums\ProductType;
use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the digital store catalog homepage.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->parentsOnly()->with('children')->get();

        $banners = Banner::active()->orderBy('sort_order')->latest('id')->get();

        $flashSale = FlashSale::running()->latest()->first();

        $gameProducts = Product::active()
            ->ofType(ProductType::Game)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->orderBy('sort_order')
            ->get();

        $accountProducts = Product::active()
            ->ofType(ProductType::Account)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->orderBy('sort_order')
            ->get();

        $gameAccountProducts = Product::active()
            ->ofType(ProductType::GameAccount)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->orderBy('sort_order')
            ->get();

        $jokiProducts = Product::active()
            ->ofType(ProductType::Joki)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('price')])
            ->orderBy('sort_order')
            ->get();

        return view('welcome', compact('categories', 'banners', 'flashSale', 'gameProducts', 'accountProducts', 'gameAccountProducts', 'jokiProducts'));
    }
}
