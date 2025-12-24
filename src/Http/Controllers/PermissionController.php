<?php

namespace atikullahnasar\role\Http\Controllers;

use App\Http\Controllers\Controller;
use atikullahnasar\role\Models\Permission;
use atikullahnasar\role\Models\Role;
use atikullahnasar\role\Services\Permissions\PermissionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct( PermissionServiceInterface $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $permissions = $this->permissionService->all();
            return datatables()->of($permissions)->toJson();
        }

        return view('roles::permissions.index');
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
        $validator = Validator::make($request->all(), [
            'name' => [ 'required','string', 'max:255', 'regex:/^[A-Za-z_]+$/'],
            'group_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z_]+$/'],
        ],[
            'name.regex' => 'The name must be a single word (letters only, no spaces).',
            'group_name.regex' => 'The group name must be a single word (letters only, no spaces).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $group = strtolower($request->group_name);
        $name  = strtolower($request->name);

        $data = [
            'group_name' => $request->group_name,
            'name'       => $group . '.' . $name,   // 👈 build "group.name"
        ];

        $this->permissionService->create($data);

        return response()->json(['success' => true, 'message' => 'Permission created successfully.']);
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
    public function edit(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z_]+$/'],
            'group_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z_]+$/'],
        ], [
            'name.regex' => 'The name must be a single word (letters only, no spaces).',
            'group_name.regex' => 'The group name must be a single word (letters only, no spaces).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $group = strtolower($request->group_name);
        $name  = strtolower($request->name);

        $data = [
            'group_name' => $request->group_name,
            'name'       => $group . '.' . $name,   //build "group.name"
        ];
        $this->permissionService->update($permission->id, $data);

        return response()->json(['success' => true, 'message' => 'Permission updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->permissionService->delete($id);
        return response()->json(['success' => true, 'message' => 'Permission deleted successfully.']);
    }
}
