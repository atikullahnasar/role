<?php

namespace atikullahnasar\role\Repositories\Roles;

use atikullahnasar\role\Repositories\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getForOwner($ownerId);
}
