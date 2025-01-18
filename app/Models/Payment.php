<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
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

    public static function getTotalPayments()
    {
        return self::where('is_active', '1')->sum('amount');
    }
}
