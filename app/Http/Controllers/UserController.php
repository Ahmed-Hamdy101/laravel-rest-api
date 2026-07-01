<?php

namespace App\Http\Controllers;

// Importing request validation classes (custom FormRequests for validation rules)
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
// Importing the User model (represents the `users` table in the DB)
use App\Http\Resources\UserResources;
use App\Models\User;
// Importing response/request helpers from Laravel
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Tests\Integration\Http\Resources\JsonApi\Fixtures\UserResource;

// UserController handles CRUD and profile-related actions for users
class UserController extends Controller
{
    /**
     * Display a paginated list of users.
     * Example: GET /api/users?page=2
     */
    public function index(): JsonResponse
    {
        $user = User::with('role')->paginate(10);
        return response()->json(UserResources::collection($user), 200);
    }

    /**
     * Show a single user by ID.
     * Example: GET /api/users/5
     */
    public function show($id): JsonResponse
    {
        // Find user by primary key (id)
        $user = User::find($id);

        // If user doesn’t exist → return 404
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Otherwise return user data with 200 OK
        return response()->json(new UserResources($user), 200);
    }

    /**
     * Create a new user.
     * Example: POST /api/users
     * Body: { "f_name": "...", "l_name": "...", "email": "...", "password": "..." }
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        // Get only allowed fields from request
        $data = $request->only(['f_name', 'l_name', 'email','role_id']);

        // Hash password before saving (security!)
        $data['password'] = Hash::make($request->input('password'));

        // Create new user in DB
        $user = User::create($data);

        // Return new user with 201 Created status
        return response()->json(new UserResources($user), 201);
    }

    /**
     * Update an existing user by ID.
     * Example: PUT /api/users/5
     */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        // Find the user
        $user = User::find($id);

        // If user not found → 404
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Collect fields to update (f_name, l_name, email)
        $data = $request->only(['f_name', 'l_name', 'email','role_id']);

        // If password field is present → hash it and include
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        // Update user in DB
        $user->update($data);

        // Return updated user with 202 Accepted
        return response()->json(new UserResources($user), 202);
    }

    /**
     * Delete a user by ID.
     * Example: DELETE /api/users/5
     */
    public function destroy($id): JsonResponse
    {
        // Find user
        $user = User::find($id);

        // If not found → 404
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete from DB
        $user->delete();

        // Return success message
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Example: GET /api/user (with token)
     */
     // Change JsonResponse to object, or remove the type hint entirely
        public function user(): JsonResponse
            {
                $user = \Auth::user();
                
                // FIXED: Changed 'data' to 'meta' to stop JSON key overwriting conflicts,
                // and chained ->response() so it strictly honors the JsonResponse return type.
                return (new UserResources($user))->additional([
                    'meta' => [
                        'permissions' => $user->permissions()
                    ]
                ])->response();
            }
    /**
     * Update logged-in user’s profile info.
     * Example: PUT /api/user/info
     */
    public function updateInfo(Request $request): JsonResponse
    {
        // Get currently authenticated user
        $user = auth()->user();

        // Extract only fields we want to allow
        $data = $request->only(['f_name', 'l_name', 'email']);

        // Update user in DB
        $user->update($data);

        // Return updated user
        return response()->json(new UserResources($user), 202);
    }

    /**
     * Update logged-in user’s password.
     * Example: PUT /api/user/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        // Validate the new password
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get currently authenticated user
        $user = auth()->user();

        // Hash the new password
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Return success message
        return response()->json([
            'message' => 'Password updated successfully',
            'user'    => new UserResources($user),
        ], 202);
    }
}
