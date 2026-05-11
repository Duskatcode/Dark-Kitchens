<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $filter = trim((string) $request->input('filter', ''));
        $categoryId = $request->input('category_id');

        $categories = Category::query()
            ->whereHas('products', function ($query): void {
                $query->where('is_available', true);
            })
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->where('is_available', true)
            ->when($filter !== '', function ($query) use ($filter): void {
                $query->where(function ($query) use ($filter): void {
                    $query->where('name', 'like', "%{$filter}%")
                        ->orWhere('description', 'like', "%{$filter}%")
                        ->orWhereHas('category', function ($query) use ($filter): void {
                            $query->where('name', 'like', "%{$filter}%");
                        });
                });
            })
            ->when(filled($categoryId), function ($query) use ($categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('client.menu.index', compact(
            'categories',
            'categoryId',
            'filter',
            'products'
        ));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_available, 404);

        $product->load('category');

        return view('client.menu.show', compact('product'));
    }
}
