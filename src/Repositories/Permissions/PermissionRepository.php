<?php

namespace atikullahnasar\role\Repositories\Permissions;

use atikullahnasar\role\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getAllPermissionsGrouped()
    {
        return Permission::all()->groupBy('group_name');
    }
}
