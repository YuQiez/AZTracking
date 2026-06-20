<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';
    protected $fillable = [
        'name',
        'display_name',
        'order',
        'last_updated_by',
    ];

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
