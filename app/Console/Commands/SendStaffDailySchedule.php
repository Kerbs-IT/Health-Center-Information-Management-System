<?php
namespace App\Console\Commands;

use App\Mail\StaffDailyScheduleMail;
use App\Models\notifications;
use App\Models\staff;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendStaffDailySchedule extends Command
{
    protected $signature = 'staff:send-daily-schedule';
    protected $description = 'Send daily schedule notification to nurses and health workers at 5:00 AM';
    
    public function handle(){
        $today = Carbon::today()->format('Y-m-d');

        $this->info("==================================================");
        $this->info("Sending Daily Schedule to Staff");
        $this->info("Date: ". $today);
        $this->info("Current Time: " . now()->format("Y-m-d H:i:s"));
        $this->info("==================================================");

        // get all member and staff (health worker)
        $healthWorker = User::whereIn("role",['nurse','staff'])
                ->where("status",'active')
                ->whereNotNull('email')
                ->get();

        if($healthWorker->isEmpty()){
            $this->warn("No staff member found with email addresses.");
            return 0;
        }

        foreach ($healthWorker as $staff) {
            $this->processStaffNotification($staff, $today);
        }

        $this->info('===========================================');
        $this->info("✓ Daily schedules sent to {$healthWorker->count()} staff members");
        $this->info('===========================================');
    }

    // create a private function for handling the information that will be sent to the emails
    private function processStaffNotification($staff,$today){
        $scheduleData = $this->getStaffScheduleForToday($staff,$today);

        $totalAppointments = array_sum(array_column($scheduleData,'count'));

        if($totalAppointments === 0 ){
            $this->info("✓ {$staff->username} - No appointments today");
            return;
        }
        
        if (date('H') == '5') {
            // send consolidated email with all appointment types
            $this->sendDailyScheduleEmail($staff, $scheduleData, $totalAppointments, $today);

            // create in app notification for individual sections 
            $this->createInAppNotifications($staff, $scheduleData, $today);
        }

        $this->info("✓ {$staff->username} - {$totalAppointments} appointments scheduled");

    
    }

    // get today schedule for a specific staff member 
    private function getStaffScheduleForToday($staff,$today){
        $scheduleData = [];
        $isNurse = $staff->role === 'nurse';
        $isStaff = $staff->role === 'staff';
        // 1. vaccination appointment
        $vaccinationQuery = DB::table('vaccination_case_records as vcr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->where('vcr.status', '!=', 'Archived')
            ->where('vcr.vaccination_status', 'completed')
            ->whereDate('vcr.date_of_comeback', $today)
            ->where('p.status', '!=', 'Archived');

        // Health workers only see their assigned patients
        if ($isStaff) {
            $vaccinationQuery->join('vaccination_medical_records as vmr', 'vmr.medical_record_case_id', '=', 'mrc.id')
                ->where('vmr.health_worker_id', $staff->id);
        }

        // count the schedule patient today
        $vaccinationCount = $vaccinationQuery->count();

        $scheduleData[] = [
            'type' => 'vaccination',
            'label' => 'Vaccination',
            'count' => $vaccinationCount,
            'icon' => '💉',
            'route' => '/patient-record/vaccination',
            'color' => '#2196F3'
        ];


        // 2. PRENATAL APPOINTMENTS
        $prenatalQuery = DB::table('pregnancy_checkups as pc')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->where('pc.status', '!=', 'Archived')
            ->whereDate('pc.date_of_comeback', $today)
            ->where('p.status', '!=', 'Archived');

        if ($staff->role === 'staff') {
            $prenatalQuery->join('prenatal_medical_records as pmr', 'pmr.medical_record_case_id', '=', 'mrc.id')
                ->where('pmr.health_worker_id', $staff->id);
        }

        $prenatalCount = $prenatalQuery->count();

        $scheduleData[] = [
            'type' => 'prenatal',
            'label' => 'Prenatal Checkup',
            'count' => $prenatalCount,
            'icon' => '🤰',
            'route' => '/patient-record/prenatal/view-records',
            'color' => '#9C27B0'
        ];

        // 3. SENIOR CITIZEN APPOINTMENTS
        $seniorCitizenQuery = DB::table('senior_citizen_case_records as sccr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->where('sccr.status', '!=', 'Archived')
            ->whereDate('sccr.date_of_comeback', $today)
            ->where('p.status', '!=', 'Archived');

        if ($staff->role === 'staff') {
            $seniorCitizenQuery->join('senior_citizen_medical_records as scmr', 'scmr.medical_record_case_id', '=', 'mrc.id')
                ->where('scmr.health_worker_id', $staff->id);
        }

        $seniorCitizenCount = $seniorCitizenQuery->count();

        $scheduleData[] = [
            'type' => 'senior_citizen',
            'label' => 'Senior Citizen Checkup',
            'count' => $seniorCitizenCount,
            'icon' => '👴',
            'route' => '/patient-record/senior-citizen/view-records',
            'color' => '#FF9800'
        ];

        // 4. TB DOTS APPOINTMENTS
        $tbDotsQuery = DB::table('tb_dots_check_ups as tdcu')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->where('tdcu.status', '!=', 'Archived')
            ->whereDate('tdcu.date_of_comeback', $today)
            ->where('p.status', '!=', 'Archived');

        if ($staff->role === 'staff') {
            $tbDotsQuery->join('tb_dots_medical_records as tdmr', 'tdmr.medical_record_case_id', '=', 'mrc.id')
                ->where('tdmr.health_worker_id', $staff->id);
        }

        $tbDotsCount = $tbDotsQuery->count();

        $scheduleData[] = [
            'type' => 'tb_dots',
            'label' => 'TB DOTS Checkup',
            'count' => $tbDotsCount,
            'icon' => '🏥',
            'route' => '/patient-record/tb-dots/view-records',
            'color' => '#4CAF50'
        ];

        // 5. FAMILY PLANNING APPOINTMENTS
        $familyPlanningQuery = DB::table('family_planning_side_b_records as fpsbr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->where('fpsbr.status', '!=', 'Archived')
            ->whereDate('fpsbr.date_of_follow_up_visit', $today)
            ->where('p.status', '!=', 'Archived');

        if ($staff->role === 'staff') {
            $familyPlanningQuery->join('family_planning_medical_records as fpmr', 'fpmr.medical_record_case_id', '=', 'mrc.id')
                ->where('fpmr.health_worker_id', $staff->id);
        }

        $familyPlanningCount = $familyPlanningQuery->count();

        $scheduleData[] = [
            'type' => 'family_planning',
            'label' => 'Family Planning Follow-up',
            'count' => $familyPlanningCount,
            'icon' => '👨‍👩‍👧‍👦',
            'route' => '/patient-record/family-planning/view-records',
            'color' => '#E91E63'
        ];

        return $scheduleData;

    }

    // create a function to send the daily schedule email

    private function sendDailyScheduleEmail($staff,$scheduleData,$totalAppointments,$today){
        try{
            $emailData = [
                'staff_name'=> $this->getStaffName($staff),
                'date' => Carbon::parse($today)->format('l, F j, Y'),
                'schedule_data'=>$scheduleData,
                'total_appointments' => $totalAppointments
            ];

            Mail::to($staff->email)->send(new StaffDailyScheduleMail($emailData));

            $this->info("  ✓ Email sent to: {$staff->email}");
        }catch(\Exception $e){
            $this->error("✗ Email failed for {$staff->email}: {$e->getMessage()}");
        }
    }

    // create in app notification
    private function createInAppNotifications($staff, $scheduleData, $today)
    {
        foreach ($scheduleData as $schedule) {
            if ($schedule['count'] > 0) {
                try {
                    $message = "{$schedule['count']} {$schedule['label']} " .
                        ($schedule['count'] === 1 ? 'patient is' : 'patients are') .
                        " scheduled for today.";

                    notifications::create([
                        'user_id' => $staff->id,
                        'patient_id' => null,
                        'type' => 'daily_schedule',
                        'title' => "Today's Schedule - {$schedule['label']}",
                        'message' => $message,
                        'appointment_type' => $schedule['type'],
                        'appointment_date' => $today,
                        'medical_record_case_id' => null,
                        'link_url' => $schedule['route'],
                        'is_read' => false,
                        'created_at' => now(),
                    ]);

                    $this->info("  ✓ In-app notification created: {$schedule['label']} ({$schedule['count']})");
                } catch (\Exception $e) {
                    $this->error("  ✗ In-app notification failed for {$schedule['label']}: {$e->getMessage()}");
                }
            }
        }
    }

    // get the staff name
    private function getStaffName($staff)
    {
        if ($staff->nurses) {
            return $staff->nurses->full_name;
        }

        if ($staff->staff) {
            return $staff->staff->full_name;
        }

        $middle = $staff->middle_initial ? strtoupper(substr($staff->middle_initial, 0, 1)) . '.' : '';
        return ucwords(trim(implode(' ', array_filter([$staff->first_name, $middle, $staff->last_name]))));
    }
}