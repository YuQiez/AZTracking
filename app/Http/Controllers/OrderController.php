<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
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
        $orders = Order::with(['customer', 'statuses'])->get();
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
                'status_id' => 'nullable|array',
                'status_id.*' => 'exists:statuses,id',
                'active_status_id' => 'nullable|exists:statuses,id',
            ]);

            $order = Order::create(collect($validated)->only(['name', 'address', 'customer_id'])->toArray());

            $statusIds = $request->input('status_id', []);
            $activeId = $request->input('active_status_id');

            if (!empty($statusIds)) {
                $sync = [];
                foreach ($statusIds as $sid) {
                    $sync[$sid] = ['active' => ($activeId && $activeId == $sid) ? true : false];
                }
                $order->statuses()->sync($sync);
            } elseif (!empty($activeId)) {
                // attach active status without removing existing
                $order->statuses()->attach($activeId, ['active' => true]);
            }
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

            $order = Order::with(['customer', 'statuses'])->findOrFail($id);

            $active = $order->activeStatus();

            return response()->json([
                'message' => 'Order retrieved successfully',
                'order' => $order,
                'active_status' => $active
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
                'status_id' => 'nullable|array',
                'status_id.*' => 'exists:statuses,id',
                'active_status_id' => 'nullable|exists:statuses,id',
            ]);

            $order->update(collect($validated)->only(['name', 'address', 'customer_id'])->toArray());

            $statusIds = $request->input('status_id', null);
            $activeId = $request->input('active_status_id');

            if (is_array($statusIds)) {
                $sync = [];
                foreach ($statusIds as $sid) {
                    $sync[$sid] = ['active' => ($activeId && $activeId == $sid) ? true : false];
                }
                $order->statuses()->sync($sync);
            }

            if ($activeId) {
                // clear existing actives
                DB::table('order_status')->where('order_id', $order->id)->update(['active' => false]);
                // attach or update pivot for active id
                if ($order->statuses()->where('statuses.id', $activeId)->exists()) {
                    $order->statuses()->updateExistingPivot($activeId, ['active' => true]);
                } else {
                    $order->statuses()->attach($activeId, ['active' => true]);
                }
            }

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
