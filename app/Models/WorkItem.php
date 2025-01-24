<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkItem extends Model
{
    use SoftDeletes;
    protected $fillable = ['type', 'diamond', 'price', 'work_id', 'is_active'];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
