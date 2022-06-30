<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order_status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function ordered_products()
    {
        return $this->hasMany(OrderedProduct::class);
    }
}
