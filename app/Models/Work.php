<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Work extends Model
{
    use SoftDeletes;
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
    public static function getTotalWorks($filter)
    {
        $query = self::where('is_active', '1');

        switch ($filter) {
            case 'today':
                $query->where('date', date('Y-m-d'));
                break;
            case 'week':
                $query->whereBetween('date', [date('Y-m-d', strtotime('last Monday')), date('Y-m-d', strtotime('next Sunday'))]);
                break;
            case 'month':
                $query->whereMonth('date', date('m'));
                break;
        }

        return $query->with('workItems')->get()->sum(function ($work) {
            return $work->workItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
        });
    }
}
