<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Resources\RoleResources;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoleResources::collection(Role::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(CreateRoleRequest $request)
        {
            return \DB::transaction(function () use ($request) {
                // 1. Create the role
                $role = Role::create([
                    'name' => $request->validated('name'),
                ]);

                // 2. Sync the permissions
                $permissions = $request->input('permissions', []);
                $role->permissions()->sync($permissions);

                // 3. Load the permissions relationship so the Resource can show them
                $role->load('permissions');

                // 4. Return as a JSON Resource with 201 Created status
                return (new RoleResources($role))
                    ->response()
                    ->setStatusCode(201);
            });
        }

     /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new RoleResources(Role::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, string $id)
        {
            $role = Role::findOrFail($id);

            // 1. Update the role name
            $role->update($request->only('name'));

            // 2. Sync permissions (Replaces the delete and the loop!)
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions'));
            }

            return response()->json(new RoleResources($role->load('permissions')), Response::HTTP_OK);
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)

    {  
        \DB::table('role_permissions')->where('role_id',$id)->delete(); 
        Role::destroy($id); 
        return response()->json(['message' => 'Role deleted successfully'], Response::HTTP_OK);
    }
}
