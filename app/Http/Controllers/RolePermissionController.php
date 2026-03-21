<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RolePermissionController extends Controller
{
    /**
     * Get all roles
     */
    public function getAllRoles(): JsonResponse
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): JsonResponse
    {
        try {
            $permissions = Permission::all();
            return response()->json([
                'success' => true,
                'data' => $permissions,
                'message' => 'Permissions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific role with its permissions
     */
    public function getRole($id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->find($id);
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a role to a user
     */
    public function assignRoleToUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|exists:roles,name'
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Assign the role (this will add to existing roles)
            $user->assignRole($validated['role']);

            return response()->json([
                'success' => true,
                'data' => $user->load('roles'),
                'message' => 'Role assigned successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a role from a user
     */
    public function removeRoleFromUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|exists:roles,name'
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Remove the role
            $user->removeRole($validated['role']);

            return response()->json([
                'success' => true,
                'data' => $user->load('roles'),
                'message' => 'Role removed successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's roles and permissions
     */
    public function getUserRolesAndPermissions($userId): JsonResponse
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $roles = $user->roles()->get();
            $permissions = $user->getAllPermissions();

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'direct_permissions' => $user->permissions()->get()
                ],
                'message' => 'User roles and permissions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user roles and permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync roles for a user (replace all existing roles)
     */
    public function syncUserRoles(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,name'
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Sync roles (remove old ones and assign new ones)
            $user->syncRoles($validated['roles']);

            return response()->json([
                'success' => true,
                'data' => $user->load('roles'),
                'message' => 'User roles synced successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users with their roles and permissions
     */
    public function getAllUsersWithRoles(): JsonResponse
    {
        try {
            $users = User::with('roles', 'permissions')->get();
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users with roles and permissions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users: ' . $e->getMessage()
            ], 500);
        }
    }
}
