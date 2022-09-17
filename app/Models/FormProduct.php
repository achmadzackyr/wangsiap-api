<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
