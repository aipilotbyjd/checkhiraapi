<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = ['name', 'description', 'date', 'user_id', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workItems()
    {
        return $this->hasMany(WorkItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
