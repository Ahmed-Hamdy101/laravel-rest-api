<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Http\Requests\CreatePermissionRequset;

class PermissionController extends Controller
{

        public function index()
        {
            // Use pagination to keep memory usage constant regardless of table size
            return PermissionResource::collection(Permission::paginate(20));
        }

        public function store(CreatePermissionRequset $request)
        {
                // alidated data is automatically returned by the FormRequest
                $validated = $request->validated();

                //  Create the permission
                $permission = Permission::create($validated);

                //  Return the newly created permission using your API Resource
                // This ensures your JSON response format is consistent
                return new PermissionResource($permission);
        }
}