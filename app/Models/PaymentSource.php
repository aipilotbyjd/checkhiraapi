<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSource extends Model
{
    protected $fillable = ['name', 'icon', 'is_active'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', '1');
    }
}
