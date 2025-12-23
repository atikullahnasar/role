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

    public function getAllPermissionsGrouped()
    {
        return $this->permissionRepository->getAllPermissionsGrouped();
    }
}
