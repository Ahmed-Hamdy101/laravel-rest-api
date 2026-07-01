<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{

        public function index()
        {
            // Use pagination to keep memory usage constant regardless of table size
            return PermissionResource::collection(Permission::paginate(20));
        }
}