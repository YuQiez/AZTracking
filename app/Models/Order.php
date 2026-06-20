<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'name',
        'address',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'order_status')
            ->withPivot('active')
            ->withTimestamps();
    }

    /**
     * Get the active status for the order.
     * If no pivot active flag is set, return the first status ordered by `order` column.
     */
    public function activeStatus()
    {
        $active = $this->statuses()->wherePivot('active', true)->first();
        if ($active) {
            return $active;
        }

        return $this->statuses()->orderBy('order', 'asc')->first();
    }
}
