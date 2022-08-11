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

    public function findMany(array $filters): Collection|array
    {
        if(!empty(Arr::except($filters, 'image_url'))) {
            return $this->productRepository->findByFilters($filters);
        }

        if(Arr::has($filters, 'image_url')) {
            return $this->productRepository->findProductsByImage(
                filter_var(Arr::get($filters, 'image_url'), FILTER_VALIDATE_BOOLEAN)
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

    public function update(array $data, string $id): Model
    {
        return $this->productRepository->update($data, $id);
    }

    public function delete(string $id): Model
    {
        return $this->productRepository->destroy($id);
    }
}