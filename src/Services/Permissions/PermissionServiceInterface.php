<?php

namespace atikullahnasar\role\Services\Permissions;

interface PermissionServiceInterface
{
    public function getAllPermissionsGrouped();
    public function all();

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function find($id);
}
