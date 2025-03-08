<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}