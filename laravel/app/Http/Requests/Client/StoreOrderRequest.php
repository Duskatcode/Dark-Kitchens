<?php

namespace App\Http\Requests\Client;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(Role::CLIENT) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],
        ];
    }
}
