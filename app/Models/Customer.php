<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(CustomerStatus::class, 'customer_status_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
