<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:create roles', only: ['store']),
            new Middleware('permission:edit roles', only: ['update']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return response()->json([
            'message' => 'Roles retrieved successfully',
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'guard_name' => 'sometimes|string',
                'permissions' => 'sometimes|array',
                'permissions.*' => 'string',
            ]);

            $role = Role::create($request->only(['name', 'guard_name']));

            $permissions = $request->input('permissions', []);
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            $role->load('permissions');

            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id = null)
    {
        try {
            $id ??= $request->query('id');

            if (empty($id)) {
                return response()->json([
                    'message' => 'Not Found'
                ], 400);
            }

            $role = Role::findOrFail($id);
            $role->load('permissions');

            return response()->json([
                'message' => 'Role retrieved successfully',
                'role' => $role,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Role not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id = null)
    {
        try {
            $id ??= $request->query('id');

            if (empty($id)) {
                return response()->json([
                    'message' => 'Not Found'
                ], 400);
            }
            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'guard_name' => 'sometimes|string',
                'permissions' => 'sometimes|array',
                'permissions.*' => 'string',
            ]);

            $role = Role::findOrFail($id);

            $role->update($request->only(['name', 'guard_name']));

            $permissions = $request->input('permissions', []);
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            $role->load('permissions');

            return response()->json([
                'message' => 'Role updated successfully',
                'role' => $role,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error', 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   
        public function destroy(string $id = null)
    {
        $id ??= request()->query('id');

        if (empty($id)) {
            return response()->json([
                'message' => 'Not Found'
            ], 400);
        }

        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

}
