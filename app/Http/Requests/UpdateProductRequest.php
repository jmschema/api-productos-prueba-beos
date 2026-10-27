<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends StoreProductRequest{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules():array{
        return array_map(
            fn (array $rules) => ['sometimes', ...$rules],
            parent::rules()
        );
    }
}