<?php

namespace atikullahnasar\role\Repositories\Roles;

use atikullahnasar\role\Models\Role;
use atikullahnasar\role\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
    public function getForOwner($ownerId)
    {
        dd(Role::where('owner_id', $ownerId)->get());
        return Role::where('owner_id', $ownerId)->get();
    }
}
