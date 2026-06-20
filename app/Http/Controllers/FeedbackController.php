<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;


class FeedbackController extends Controller 
{
    /**
     * Get the middleware that should be assigned to the controller.
     */

    /**
     * Display a listing of the resource.
     */
    public function index(){
        $feedback = Feedback::all();
        return response()->json([
            'message' => 'Feedback retrieved successfully',
            'feedback' => $feedback
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
                'name' => 'required|string',
                'message' => 'required|string',
            ]);

            $feedback = Feedback::create($validated);

            return response()->json([
                'message' => 'Feedback created successfully',
                'feedback' => $feedback
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while creating feedback',
                'error' => $e->getMessage()
            ], 500);
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

            $feedback = Feedback::findOrFail($id);

            return response()->json([
                'message' => 'Feedback retrieved successfully',
                'feedback' => $feedback
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Feedback not found'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
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

            $feedback = Feedback::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'message' => 'sometimes|required|string',
            ]);

            $feedback->update($validated);

            return response()->json([
                'message' => 'Feedback updated successfully',
                'feedback' => $feedback
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Feedback not found'
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while updating feedback',
                'error' => $e->getMessage()
            ], 500);
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

            $feedback = Feedback::findOrFail($id);
            $feedback->delete();

            return response()->json([
                'message' => 'Feedback deleted successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while deleting feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
  
}
