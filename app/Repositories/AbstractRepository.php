<?php

namespace App\Repositories;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class AbstractRepository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @return Collection
     */
    public function listAll(): Collection
    {
        return $this->model->get();
    }

    /**
     * @param  string $id
     * @return Model
     * @throws NotFoundException
     */
    public function findByIdOrFail(string $id): Model
    {
        $model = $this->model->find($id);

        if (!$model) {
            throw new NotFoundException($this->model->getNotFoundMessage());
        }

        return $model;
    }

    public function findByFilters(array $filters): array|Collection
    {
        $filters = Arr::only($filters, $this->model->getFillable());

        if(empty($filters)) {
            return [];
        }

        return $this->model->where($filters)->get();
    }

    /**
     * @param  array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param  string $id
     * @param  array  $data
     * @return Model
     * @throws NotFoundException
     */
    public function update(array $data, string $id): Model
    {
        $register = $this->findByIdOrFail($id);
        $register->update($data);
        return $register;
    }

    /**
     * @param  string $id
     * @return Model
     * @throws NotFoundException
     */
    public function destroy(string $id): Model
    {
        $register = $this->findByIdOrFail($id);
        $register->delete();
        return $register;
    }
}