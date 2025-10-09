<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    // ✅ List all users
    public function index()
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Create new user
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'status' => 'nullable|boolean',
            ]);

            // Hash the password
            $validated['password'] = bcrypt($validated['password']);

            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = true; // default to active (true)
            }

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }


    // ✅ Show single user by ID
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Update user
    public function update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validation rules
        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        // Update fields if provided
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Update status (boolean value, no conversion needed)
        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    // ✅ Delete user
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }
}
