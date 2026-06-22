<?php

namespace App\Http\Controllers;

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
        return RoleResources::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::create($request->only('name'));
        if($permissions = $request->input('permissions')){
            foreach($permissions as $permission_id){
                \DB::table('role_permissions')->insert([
                    'role_id'=>$role->id,
                    'permission_id'=>$permission_id
                ]);
            }
        }
        
        return response()->json(new RoleResources($role), Response::HTTP_CREATED);
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
        // find the role
        $role = Role::findOrFail($id);
        // update role name
        $role->update($request->only('name'));
        // delete existing permissions for the role
        \DB::table('role_permissions')->where('role_id',$role->id)->delete(); 
        // assign new permissions to the role
        if($permissions = $request->input('permissions')){
            foreach($permissions as $permission_id){
                \Db::table('role_permissions')->insert([
                    'role_id'=>$role->id,
                    'permission_id'=>$permission_id
                ]);
            }
        }
        return response()->json(new RoleResources($role), Response::HTTP_OK);
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
