<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Throwable;

class CustomerController extends Controller implements HasMiddleware
{

/**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view customers', only: ['index']),
            new Middleware('permission:create customers', only: ['store']),
            new Middleware('permission:update customers', only: ['update']),
            new Middleware('permission:delete customers', only: ['destroy'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return response()->json([
            'message' => 'Customers retrieved successfully',
            'customers' => $customers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:customers',
                'phone' => 'sometimes|string',
            ]);

            $customer = Customer::create($validated);

            return response()->json(['message'=>'Customer created','customer'=>$customer], 201);
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
        $id = $id ?? $request->query('id');
        $customer = Customer::findOrFail($id);
        return response()->json([
            'message' => 'Customer retrieved successfully',
            'customer' => $customer
        ]);
     }
    
        
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
                'phone' => 'sometimes|string',
            ]);

            $customer->update($validated);

            return response()->json(['message'=>'Customer updated','customer'=>$customer]);
        } catch (ValidationException $e) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return response()->json(['message'=>'Customer deleted']);
        } catch (Throwable $e) {
            return response()->json(['message'=>'Error','error'=>config('app.debug') ? $e->getMessage() : 'Internal server error'], 500);
        }
    }
}
