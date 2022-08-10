<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ProductService
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function findMany(array $filters): Collection
    {
        if(!empty(Arr::except($filters, 'image_url'))){
            return $this->productRepository->findByFilters($filters);
        }

        if(Arr::has($filters, 'image_url')){
            return $this->productRepository->findProductsByImage(
                Arr::get($filters, 'image_url')
            );
        }

        return $this->productRepository->listAll();
    }

    public function create(array $data): Model
    {
        return $this->productRepository->create($data);
    }

    public function findOne(string $id): Model
    {
        return $this->productRepository->findByIdOrFail($id);
    }
}