<?php

namespace atikullahnasar\role\Repositories;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function paginate($query = null, int $perPage = 10, array $columns = ['*'], array $relations = [], array $where = []);
    public function find(int $id, array $columns = ['*'], array $relations = []);
    public function findWhere(array $where, array $columns = ['*'], array $relations = []);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
