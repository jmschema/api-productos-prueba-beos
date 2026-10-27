<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request):array{
        return[
            'id'=>$this->id,
            'product_id'=>$this->product_id,
            'currency'=> new CurrencyResource($this->whenLoaded('currency')),
            'price'=>$this->price,
            'created_at'=>$this->created_at?->toIso8601String()
        ];
    }
}