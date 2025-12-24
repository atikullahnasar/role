<?php

namespace atikullahnasar\role\Services\Permissions;

use atikullahnasar\role\Repositories\Permissions\PermissionRepositoryInterface;

class PermissionService implements PermissionServiceInterface
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function create(array $data)
    {
        $role = $this->permissionRepository->create($data);
        return $role;
    }

    public function update($id, array $data)
    {
        return $this->permissionRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->permissionRepository->delete($id);
    }

    public function find($id)
    {
        return $this->permissionRepository->find($id);
    }

    public function all()
    {
        return $this->permissionRepository->all();
    }


    public function getAllPermissionsGrouped()
    {
        return $this->permissionRepository->getAllPermissionsGrouped();
    }

}
