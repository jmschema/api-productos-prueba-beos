<?php

namespace App\Http\Resources;

use Database\Factories\CurrencyFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request):array{
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'price'=>$this->price,
            'currency_id'=>$this->currency_id,
            'currency'=> new CurrencyResource('currency'),
            'tax_cost'=>$this->tax_cost,
            'manufacturing_cost'=>$this->manufacturing_cost,
            'prices'=> ProductPriceResource::collection('prices'),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}