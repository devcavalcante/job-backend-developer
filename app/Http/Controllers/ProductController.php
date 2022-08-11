<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->findMany($request->all());
        return response()->json($products, 200);
    }


    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->all());
        return response()->json($product, 201);
    }

    public function show(string $id): JsonResponse
    {
        $product = $this->productService->findOne($id);
        return response()->json($product, 200);
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = $this->productService->update($request->all(), $id);
        return response()->json($product, 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productService->delete($id);
        return response()->json([], 204);
    }
}
