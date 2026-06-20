<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;


class PermissionController extends Controller implements HasMiddleware
{

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permissions', only: ['index']),
            new Middleware('permission:create permissions', only: ['store']),
            new Middleware('permission:edit permissions', only: ['update']),
            new Middleware('permission:delete permissions', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json([
            'message' => 'Permissions retrieved successfully',
            'permissions' => $permissions,
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
            'guard_name' => 'required|string',
        ]);

        $permission = Permission::create($validated);

        return response()->json(['message'=>'Permission created','permission'=>$permission], 201);
    } catch (ValidationException $e) {
        return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
    } catch (Throwable $e) {
        return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
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

            $permission = Permission::findOrFail($id);
            return response()->json([
                'message' => 'Permission retrieved successfully',
                'permission' => $permission,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permission not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

     
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'guard_name' => 'required|string',
            ]);

            $permission = Permission::findOrFail($id);
            $permission->update($validated);

            return response()->json([
                'message' => 'Permission updated successfully',
                'permission' => $permission,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permission not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id = null)
    {
        try {
            $id ??= request()->query('id');

            if (empty($id)) {
                return response()->json([
                    'message' => 'Not Found'
                ], 400);
            }

            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'message' => 'Permission soft-deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Permission not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
