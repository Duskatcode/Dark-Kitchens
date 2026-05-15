<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $recordsPerPageOptions = [10, 25, 50, 100];
        $recordsPerPage = (int) $request->input('records_per_page', 10);
        $recordsPerPage = in_array($recordsPerPage, $recordsPerPageOptions, true) ? $recordsPerPage : 10;
        $filter = trim((string) $request->input('filter', ''));
        $categoryId = $request->input('category_id');
        $availability = $request->input('availability');

        $categories = Category::query()->orderBy('name')->get();

        $products = Product::query()
            ->with('category')
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
            ->when($availability === 'available', function ($query): void {
                $query->where('is_available', true);
            })
            ->when($availability === 'unavailable', function ($query): void {
                $query->where('is_available', false);
            })
            ->latest('id')
            ->paginate($recordsPerPage)
            ->withQueryString();

        return view('admin.products.index', compact(
            'availability',
            'categories',
            'categoryId',
            'filter',
            'products',
            'recordsPerPage',
            'recordsPerPageOptions'
        ));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_available'] = $request->boolean('is_available');

        Product::query()->create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('category');
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('categories', 'product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_available'] = $request->boolean('is_available');

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderDetails()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors([
                    'delete' => 'No puedes eliminar un producto asociado a pedidos.',
                ]);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto eliminado correctamente.');
    }
}
