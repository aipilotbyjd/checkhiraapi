<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'amount', 'category', 'description', 'source_id', 'date', 'is_active', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function source()
    {
        return $this->belongsTo(PaymentSource::class);
    }

    public static function getTotalPayments($filter)
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
            default:
                break;
        }

        return $query->sum('amount');
    }
}
