<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('products', 'name')->ignore($product?->id),
            ],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'is_available' => ['required', 'boolean'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
        ];
    }
}
