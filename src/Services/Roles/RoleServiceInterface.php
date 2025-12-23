<?php

namespace atikullahnasar\role\Services\Roles;

interface RoleServiceInterface
{
    public function create(array $data, array $permissions);

    public function update($id, array $data, array $permissions);

    public function delete($id);

    public function find($id);

    public function getForOwner();
}
