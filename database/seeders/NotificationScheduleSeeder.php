<?php

namespace Database\Seeders;

use App\Models\NotificationSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'label'          => 'appointment-reminder',
                'scheduled_time' => '22:00:00',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'label'          => 'overdue-notifications',
                'scheduled_time' => '09:00:00',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'label'          => 'staff-daily-schedule',
                'scheduled_time' => '07:00:00',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        foreach ($schedules as $schedule) {
            NotificationSchedule::updateOrInsert(
                ['label' => $schedule['label']],
                $schedule
            );
        }
    }
}
