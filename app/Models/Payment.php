<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['name', 'amount', 'category', 'description', 'source', 'date', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
