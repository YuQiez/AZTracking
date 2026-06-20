<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;

class StatusController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view status', only: ['index']),
            new Middleware('permission:create status', only: ['store']),
            new Middleware('permission:edit status', only: ['update']),
            new Middleware('permission:delete status', only: ['destroy'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::with('lastUpdatedBy')->orderBy('order')->get();
        return response()->json([
            'message' => 'Statuses retrieved successfully',
            'statuses' => $statuses
        ]);
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:statuses,name',
                'display_name' => 'sometimes|string',
                'order' => 'sometimes|integer',
            ]);

            $status = Status::create($validated);

            return response()->json(['message'=>'Status created','status'=>$status], 201);
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

            $status = Status::with('lastUpdatedBy')->findOrFail($id);
            return response()->json([
                'message' => 'Status retrieved successfully',
                'status' => $status
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Status not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        //
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

            $status = Status::findOrFail($id);
            $validated = $request->validate([
                'name' => 'sometimes|required|string|unique:statuses,name,' . $status->id,
                'display_name' => 'sometimes|string',
                'order' => 'sometimes|integer',
            ]);

            $status->update($validated);

            return response()->json(['message'=>'Status updated','status'=>$status]);
        } catch (ValidationException $e) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Status not found'], 404);
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

            $status = Status::findOrFail($id);
            $status->delete();
            return response()->json(['message'=>'Status deleted']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Status not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
