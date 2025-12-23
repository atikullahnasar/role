<?php

namespace atikullahnasar\role\Http\Controllers;

use App\Http\Controllers\Controller;
use atikullahnasar\role\Models\Role;
use atikullahnasar\role\Services\Permissions\PermissionServiceInterface;
use atikullahnasar\role\Services\Roles\RoleServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $roleService;
    protected $permissionService;

    public function __construct(RoleServiceInterface $roleService, PermissionServiceInterface $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            dd($request->all());
            $roles = $this->roleService->getForOwner();
            return datatables()->of($roles)->toJson();
        }
        $permissions = $this->permissionService->getAllPermissionsGrouped();
        return view('roles::roles.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:beft_roles,name,NULL,id,owner_id,' . Auth::id(),
            'permissions' => 'array',
            'permissions.*' => 'exists:beft_permissions,id',
        ], [
            'name.unique' => 'This role name already exists for your account.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only('name');
        $data['owner_id'] = Auth::id();
        $this->roleService->create($data, $request->input('permissions', []));

        return response()->json(['success' => true, 'message' => 'Role created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    public function edit(Role $role)
    {
        $role->load('permissions');
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:beft_roles,name,' . $role->id . ',id,owner_id,' . Auth::id(),
            'permissions' => 'array',
            'permissions.*' => 'exists:beft_permissions,id',
        ], [
            'name.unique' => 'This role name already exists for your account.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->only('name');
        $this->roleService->update($role->id, $data, $request->input('permissions', []));

        return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->roleService->delete($id);
        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }
}
