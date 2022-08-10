<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $products = $this->productService->findMany($request->all());
        return response()->json($products, 200);
    }


    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->all());
        return response()->json($product, 201);
    }
}
