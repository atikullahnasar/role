<?php

namespace atikullahnasar\role\Services\Roles;
use atikullahnasar\role\Repositories\Roles\RoleRepositoryInterface;

class RoleService implements RoleServiceInterface
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function create(array $data, array $permissions)
    {
        $role = $this->roleRepository->create($data);
        $role->permissions()->sync($permissions);
        return $role;
    }

    public function update($id, array $data, array $permissions)
    {
        $this->roleRepository->update($id, $data);
        $role = $this->roleRepository->find($id);
        $role->permissions()->sync($permissions);
        return $role;
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }

    public function find($id)
    {
        return $this->roleRepository->find($id);
    }

    public function getForOwner()
    {
        $ownerId = auth()->user()->owner_id ?? auth()->user()->id;
        return $this->roleRepository->getForOwner($ownerId);
    }
}
