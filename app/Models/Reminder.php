<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'remind_at',
        'is_snoozed',
        'snooze_minutes',
        'user_id'  // Add this
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_snoozed' => 'boolean',
        'snooze_minutes' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue()
    {
        return Carbon::now()->gte($this->remind_at);
    }

    public function getTimeRemainingAttribute()
    {
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        $now = Carbon::now();
        $remindAt = $this->remind_at;
        
        if ($now > $remindAt) {
            return 'Overdue';
        }
        
        $minutes = $now->diffInMinutes($remindAt, false);
        
        if ($minutes < 60) {
            return round($minutes) . ' minutes';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $remainingMinutes > 0 ? "{$hours}h {$remainingMinutes}m" : "{$hours} hours";
    }

    public function scopeActive($query)
    {
        return $query->where('remind_at', '>', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('remind_at', '<=', Carbon::now());
    }
}