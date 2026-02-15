<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSchedule extends Model
{

    protected $fillable = [
        'label',
        'scheduled_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Human-readable display names for each label
     */
    public static function displayNames(): array
    {
        return [
            'appointment-reminder'  => 'Appointment Reminder Notification',
            'overdue-notifications' => 'Overdue Appointment Notification',
            'staff-daily-schedule'  => 'Staff Daily Schedule Notification',
        ];
    }

    /**
     * Get display name for this schedule's label
     */
    public function getDisplayNameAttribute(): string
    {
        return self::displayNames()[$this->label] ?? $this->label;
    }

    /**
     * Check if this schedule should run right now (matches current H:i)
     */
    public function shouldRunNow(): bool
    {
        return $this->is_active &&
            now()->format('H:i') === substr($this->scheduled_time, 0, 5);
    }

    /**
     * Convenience: fetch a schedule and check if it should fire now
     */
    public static function shouldRun(string $label): bool
    {
        $schedule = self::where('label', $label)->first();

        if (!$schedule) return false;

        return $schedule->shouldRunNow();
    }
}
