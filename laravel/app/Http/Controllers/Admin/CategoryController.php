<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $recordsPerPageOptions = [10, 25, 50, 100];
        $recordsPerPage = (int) $request->input('records_per_page', 10);
        $recordsPerPage = in_array($recordsPerPage, $recordsPerPageOptions, true) ? $recordsPerPage : 10;
        $filter = trim((string) $request->input('filter', ''));

        $categories = Category::query()
            ->withCount('products')
            ->when($filter !== '', function ($query) use ($filter): void {
                $query->where('name', 'like', "%{$filter}%");
            })
            ->latest('id')
            ->paginate($recordsPerPage)
            ->withQueryString();

        return view('admin.categories.index', compact(
            'categories',
            'filter',
            'recordsPerPage',
            'recordsPerPageOptions'
        ));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::query()->create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->withErrors([
                    'delete' => 'No puedes eliminar una categoría con productos asociados.',
                ]);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría eliminada correctamente.');
    }
}
