<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller{
    public function index(Request $request):AnonymousResourceCollection{
        $perPage = max(1, min($request->integer('per_page', 25), 100));
        $products = Product::with('currency')->latest()->paginate($perPage);
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request):JsonResponse{
        $product = Product::create($request->validated());
        return ProductResource::make($product->load('currency'))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Product $product):ProductResource{
        return ProductResource::make($product->load(['currency', 'prices.currency']));
    }

    public function update(UpdateProductRequest $request, Product $product):ProductResource{
        $product->update($request->validated());
        return ProductResource::make($product->load('currency'));
    }

    public function destroy(Product $product):Response{
        $product->delete();
        return response()->noContent();
    }
}