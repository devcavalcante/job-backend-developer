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
        if($hasImage){
            return $this->model->whereNotNull('image_url')->get();
        }

        return $this->model->whereNull('image_url')->get();
    }
}