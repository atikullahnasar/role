<?php

namespace atikullahnasar\role\Repositories\Permissions;

use atikullahnasar\role\Repositories\BaseRepositoryInterface;

interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPermissionsGrouped();
}
