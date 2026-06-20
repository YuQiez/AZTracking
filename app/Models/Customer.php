<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
