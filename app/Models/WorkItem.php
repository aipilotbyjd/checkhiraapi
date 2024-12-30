<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkItem extends Model
{
    protected $fillable = ['type', 'diamond', 'price', 'work_id', 'is_active'];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
