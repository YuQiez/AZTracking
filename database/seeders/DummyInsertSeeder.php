<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Status;
use App\Models\Order;
use App\Models\Feedback;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DummyInsertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles & Permissions
        for ($i = 1; $i <= 5; $i++) {
            Role::firstOrCreate(['name' => "role_seed_$i", 'guard_name' => 'sanctum']);
            Permission::firstOrCreate(['name' => "perm_seed_$i", 'guard_name' => 'sanctum']);
        }

        // Customers
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = Customer::create([
                'name' => "Customer Seed $i",
                'email' => "cust_seed_{$i}@example.com",
                'phone' => sprintf('000%02d', $i),
            ]);
        }

        // Statuses
        $statuses = [];
        for ($i = 1; $i <= 5; $i++) {
            $statuses[] = Status::create([
                'name' => "status_seed_$i",
                'display_name' => "Status Seed $i",
                'order' => $i,
            ]);
        }

        // Users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "User Seed $i",
                'email' => "user_seed_{$i}@example.com",
                'password' => Hash::make('secret123'),
            ]);
        }

        // Orders: attach first two statuses with second active
        foreach (range(1, 5) as $i) {
            $order = Order::create([
                'name' => "Order Seed $i",
                'address' => "Addr Seed $i",
                'customer_id' => $customers[0]->id ?? null,
            ]);

            if (isset($statuses[0]) && isset($statuses[1])) {
                // attach both statuses and mark second as active
                $order->statuses()->attach([
                    $statuses[0]->id => ['active' => 0],
                    $statuses[1]->id => ['active' => 1],
                ]);
            }
        }

        // Feedbacks
        for ($i = 1; $i <= 5; $i++) {
            Feedback::create([
                'name' => "FB Seed $i",
                'message' => "Hello Seed $i",
            ]);
        }
    }
}
