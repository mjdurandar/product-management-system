<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'description',
        'price',
        'image',
        'api_source'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
