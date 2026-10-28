<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize():bool{
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules():array{
        $product=$this->route('product'); 
        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::unique('product_prices')->where('product_id', $product->id),
                Rule::notIn([$product->currency_id]),
            ],
            'price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99']
        ];
    }

    public function messages():array{
        return[
            'currency_id.unique'=>'El producto ya tiene un precio en esta divisa',
            'currency_id.not_in'=>'El precio en la divisa base se debe cargar en el producto'
        ];
    }
}