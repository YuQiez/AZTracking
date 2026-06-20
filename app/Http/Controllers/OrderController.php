<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view orders', only: ['index']),
            new Middleware('permission:create orders', only: ['store']),
            new Middleware('permission:edit orders', only: ['update']),
            new Middleware('permission:delete orders', only: ['destroy'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['customer', 'status'])->get();
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'orders' => $orders
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
                'address' => 'required|string',
                'customer_id' => 'required|exists:customers,id',
                'status_id' => 'required|exists:statuses,id',
            ]);

            $order = Order::create($validated);
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while creating the order',
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

            $order = Order::with(['customer', 'status'])->findOrFail($id);

            return response()->json([
                'message' => 'Order retrieved successfully',
                'order' => $order
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
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

            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'address' => 'sometimes|required|string',
                'customer_id' => 'sometimes|required|exists:customers,id',
                'status_id' => 'sometimes|required|exists:statuses,id',
            ]);

            $order->update($validated);

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while updating the order',
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

            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
