<?php

namespace atikullahnasar\role\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->get($columns);
    }

    public function paginate($query = null, int $perPage = 10, array $columns = ['*'], array $relations = [], array $where = [])
    {
        if (is_null($query)) {
            $query = $this->model->newQuery();
        }

        foreach ($where as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }
        return $query->with($relations)->select($columns)->paginate($perPage);
    }

    public function find(int $id, array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->select($columns)->findOrFail($id);
    }

    public function findWhere(array $where, array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->select($columns)->where($where)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        return $item ? $item->update($data) : null;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }
}
