<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends AbstractRepository
{
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function findProductsByImage(bool $hasImage): Collection
    {
        return $this->model
        ->when(!$hasImage, function($query){
            return $query->whereNull('image_url');
        })
        ->when($hasImage, function($query){
            return $query->whereNotNull('image_url');
        })
        ->get();
    }
}