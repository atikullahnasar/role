<?php

namespace atikullahnasar\role\Repositories\Permissions;

use atikullahnasar\role\Models\Permission;

use atikullahnasar\role\Repositories\BaseRepository;
use atikullahnasar\role\Repositories\Permissions\PermissionRepositoryInterface;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function getAllPermissionsGrouped()
    {
        return Permission::all()->groupBy('group_name');
    }
}
