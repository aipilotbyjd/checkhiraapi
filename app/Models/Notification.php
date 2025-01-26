<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'image', 'link', 'link_text', 'link_icon', 'link_color', 'is_read', 'status'];

    protected $dates = ['deleted_at'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public static function getUnreadCount()
    {
        return self::unread()->active()->count();
    }
}
