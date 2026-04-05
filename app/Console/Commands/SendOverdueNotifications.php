<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OverdueAppointmentsMail;
use App\Models\Notification;
use App\Models\notifications;
use App\Models\NotificationSchedule;
use App\Models\User;

class SendOverdueNotifications extends Command
{
    protected $signature = 'staff:send-overdue-notifications';
    protected $description = 'Send overdue appointments notification to nurses and health workers';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        $this->info('===========================================');
        $this->info('Checking for Overdue Appointments');
        $this->info('Date: ' . $today);
        $this->info('Current time: ' . now()->format('Y-m-d H:i:s'));
        $this->info('===========================================');

        // Get all nurses and staff (health workers)
        $staffMembers = User::whereIn('role', ['nurse', 'staff'])
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get();

        if ($staffMembers->isEmpty()) {
            $this->warn('No staff members found with email addresses.');
            return 0;
        }

        foreach ($staffMembers as $staff) {
            $this->processOverdueNotification($staff, $today);
        }

        $this->info('===========================================');
        $this->info("✓ Overdue notifications processed for {$staffMembers->count()} staff members");
        $this->info('===========================================');

        return 0;
    }

    /**
     * Process overdue notification for a single staff member
     */
    private function processOverdueNotification($staff, $today)
    {
        // Get overdue appointments for this staff member
        $overdueData = $this->getOverdueAppointments($staff, $today);

        // Calculate total overdue appointments
        $totalOverdue = array_sum(array_column($overdueData, 'count'));

        if ($totalOverdue === 0) {
            $this->info("✓ {$staff->username} - No overdue appointments");
            return;
        }

        if (NotificationSchedule::shouldRun('overdue-notifications')) {
            // 1. Send consolidated email with all overdue appointment types
            $this->sendOverdueEmail($staff, $overdueData, $totalOverdue);

            // 2. Create individual in-app notifications for each overdue type
            $this->createOverdueInAppNotifications($staff, $overdueData);
        }

        $this->info("✓ {$staff->username} - {$totalOverdue} overdue appointments");
    }

    /**
     * Get overdue appointments for a specific staff member
     */
    private function getOverdueAppointments($staff, $today)
    {
        $overdueData = [];
        $isStaff = $staff->role === 'staff';

        // =========================================================
        // 1. VACCINATION OVERDUE
        // =========================================================

        // DYNAMIC: Load vaccine config from DB instead of hardcoded array
        // Keys are vaccine_acronym (e.g. 'BCG', 'PENTA'), values are max_doses
        $vaccineDoseConfig = DB::table('vaccines')
            ->where('status', 'Active')
            ->pluck('max_doses', 'vaccine_acronym')
            ->map(fn($doses) => (int) $doses)
            ->toArray();

        $lastVaccinationSubquery = DB::table('vaccination_case_records as vcr')
            ->select(
                'vcr.medical_record_case_id',
                DB::raw('MAX(vcr.id) as last_record_id')
            )
            ->where('vcr.status', '!=', 'Archived')
            ->where('vcr.vaccination_status', 'completed')
            ->groupBy('vcr.medical_record_case_id');

        $vaccinationBaseQuery = DB::table('vaccination_case_records as vcr')
            ->joinSub($lastVaccinationSubquery, 'last_vcr', function ($join) {
                $join->on('vcr.id', '=', 'last_vcr.last_record_id');
            })
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->whereDate('vcr.date_of_comeback', '<', $today)
            ->where('p.status', '!=', 'Archived');

        if ($isStaff) {
            $vaccinationBaseQuery->join('vaccination_medical_records as vmr', 'vmr.medical_record_case_id', '=', 'mrc.id')
                ->where('vmr.health_worker_id', $staff->id);
        }

        $vaccinationCases = $vaccinationBaseQuery
            ->select('vcr.*', 'mrc.id as medical_record_case_id', 'p.id as patient_id')
            ->get();

        $countedPatients = [];

        foreach ($vaccinationCases as $case) {
            if (in_array($case->patient_id, $countedPatients)) {
                continue;
            }

            $vaccines = array_map('trim', explode(',', $case->vaccine_type ?? ''));
            $currentDose = (int) $case->dose_number;
            $nextDosage  = $currentDose + 1;
            $allVaccinesComplete = true;
            $hasPendingDose = false;

            foreach ($vaccines as $vaccineAcronym) {
                if ($vaccineAcronym === '') {
                    continue;
                }

                // DYNAMIC: Look up max doses from DB result; skip if vaccine not found
                // Uses case-insensitive match to handle acronym casing differences
                $matchedKey = collect($vaccineDoseConfig)->keys()->first(
                    fn($key) => strtoupper($key) === strtoupper($vaccineAcronym)
                );

                if ($matchedKey === null) {
                    // Vaccine not in DB (e.g. archived or unknown) — skip it
                    continue;
                }

                $maxDoses = $vaccineDoseConfig[$matchedKey];

                if ($currentDose < $maxDoses) {
                    $allVaccinesComplete = false;

                    $nextDoseExists = DB::table('vaccination_case_records')
                        ->where('medical_record_case_id', $case->medical_record_case_id)
                        ->where('vaccine_type', 'LIKE', '%' . $matchedKey . '%')
                        ->where('status', '!=', 'Archived')
                        ->where('dose_number', $nextDosage)
                        ->exists();

                    if (!$nextDoseExists) {
                        $hasPendingDose = true;
                        break;
                    }
                }
            }

            if (!$allVaccinesComplete && $hasPendingDose) {
                $countedPatients[] = $case->patient_id;
            }
        }

        $vaccinationCount = count($countedPatients);

        // DEBUG block (remove after verifying)
        $this->info("=== VACCINATION DEBUG ===");
        $this->info("Vaccine config loaded from DB: " . count($vaccineDoseConfig) . " active vaccines");
        $this->info("Total cases found: " . $vaccinationCases->count());
        $this->info("Unique patients counted: " . $vaccinationCount);
        foreach ($countedPatients as $patientId) {
            $patient = DB::table('patients')->where('id', $patientId)->first();
            $this->info("  - Patient ID: {$patientId}, Name: {$patient->first_name} {$patient->last_name}");
        }
        $this->info("=========================");

        $overdueData[] = [
            'type'  => 'vaccination',
            'label' => 'Vaccination',
            'count' => $vaccinationCount,
            'icon'  => '💉',
            'route' => '/patient-record/vaccination',
            'color' => '#F44336'
        ];

        // 2. PRENATAL OVERDUE - Check only the last record for each patient
        $lastPrenatalSubquery = DB::table('pregnancy_checkups as pc')
            ->select(
                'pc.medical_record_case_id',
                DB::raw('MAX(pc.id) as last_record_id')
            )
            ->where('pc.status', '!=', 'Archived')
            ->whereNotNull('pc.date_of_comeback')
            ->groupBy('pc.medical_record_case_id');

        $prenatalBaseQuery = DB::table('pregnancy_checkups as pc')
            ->joinSub($lastPrenatalSubquery, 'last_pc', function ($join) {
                $join->on('pc.id', '=', 'last_pc.last_record_id');
            })
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->whereDate('pc.date_of_comeback', '<', $today) // Only past dates (OVERDUE, not today)
            ->where('p.status', '!=', 'Archived');

        if ($isStaff) {
            $prenatalBaseQuery->join('prenatal_medical_records as pmr', 'pmr.medical_record_case_id', '=', 'mrc.id')
                ->where('pmr.health_worker_id', $staff->id);
        }

        // Get all potential overdue cases
        $prenatalCases = $prenatalBaseQuery
            ->select('pc.*', 'mrc.id as medical_record_case_id')
            ->get();

        // Filter cases that don't have a checkup after comeback date
        $prenatalCount = 0;
        foreach ($prenatalCases as $case) {
            $checkupExists = DB::table('pregnancy_checkups')
                ->where('medical_record_case_id', $case->medical_record_case_id)
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $case->date_of_comeback)
                ->where('id', '!=', $case->id)
                ->exists();

            if (!$checkupExists) {
                $prenatalCount++;
            }
        }

        $overdueData[] = [
            'type' => 'prenatal',
            'label' => 'Prenatal Checkup',
            'count' => $prenatalCount,
            'icon' => '🤰',
            'route' => '/patient-record/prenatal/view-records',
            'color' => '#F44336'
        ];

        // 3. SENIOR CITIZEN OVERDUE - Check only the last record for each patient
        $seniorCitizenSubquery = DB::table('senior_citizen_case_records as sccr')
            ->select('sccr.medical_record_case_id', DB::raw('MAX(sccr.id) as last_record_id'))
            ->where('sccr.status', '!=', 'Archived')
            ->whereNotNull('sccr.date_of_comeback')
            ->groupBy('sccr.medical_record_case_id');

        $seniorCitizenQuery = DB::table('senior_citizen_case_records as sccr')
            ->joinSub($seniorCitizenSubquery, 'last_sccr', function ($join) {
                $join->on('sccr.id', '=', 'last_sccr.last_record_id');
            })
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->whereDate('sccr.date_of_comeback', '<', $today) // Only past dates (OVERDUE, not today)
            ->where('p.status', '!=', 'Archived');

        if ($isStaff) {
            $seniorCitizenQuery->join('senior_citizen_medical_records as scmr', 'scmr.medical_record_case_id', '=', 'mrc.id')
                ->where('scmr.health_worker_id', $staff->id);
        }

        $seniorCitizenCount = $seniorCitizenQuery->distinct()->count('p.id');

        $overdueData[] = [
            'type' => 'senior_citizen',
            'label' => 'Senior Citizen Checkup',
            'count' => $seniorCitizenCount,
            'icon' => '👴',
            'route' => '/patient-record/senior-citizen/view-records',
            'color' => '#F44336'
        ];

        // 4. TB DOTS OVERDUE - Check only the last record for each patient
        $tbDotsSubquery = DB::table('tb_dots_check_ups as tdcu')
            ->select('tdcu.medical_record_case_id', DB::raw('MAX(tdcu.id) as last_record_id'))
            ->where('tdcu.status', '!=', 'Archived')
            ->whereNotNull('tdcu.date_of_comeback')
            ->groupBy('tdcu.medical_record_case_id');

        $tbDotsQuery = DB::table('tb_dots_check_ups as tdcu')
            ->joinSub($tbDotsSubquery, 'last_tdcu', function ($join) {
                $join->on('tdcu.id', '=', 'last_tdcu.last_record_id');
            })
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->whereDate('tdcu.date_of_comeback', '<', $today) // Only past dates (OVERDUE, not today)
            ->where('p.status', '!=', 'Archived');

        if ($isStaff) {
            $tbDotsQuery->join('tb_dots_medical_records as tdmr', 'tdmr.medical_record_case_id', '=', 'mrc.id')
                ->where('tdmr.health_worker_id', $staff->id);
        }

        $tbDotsCount = $tbDotsQuery->distinct()->count('p.id');

        $overdueData[] = [
            'type' => 'tb_dots',
            'label' => 'TB DOTS Checkup',
            'count' => $tbDotsCount,
            'icon' => '🏥',
            'route' => '/patient-record/tb-dots/view-records',
            'color' => '#F44336'
        ];

        // 5. FAMILY PLANNING OVERDUE - Check only the last record for each patient
        $familyPlanningSubquery = DB::table('family_planning_side_b_records as fpsbr')
            ->select('fpsbr.medical_record_case_id', DB::raw('MAX(fpsbr.id) as last_record_id'))
            ->where('fpsbr.status', '!=', 'Archived')
            ->whereNotNull('fpsbr.date_of_follow_up_visit')
            ->groupBy('fpsbr.medical_record_case_id');

        $familyPlanningQuery = DB::table('family_planning_side_b_records as fpsbr')
            ->joinSub($familyPlanningSubquery, 'last_fpsbr', function ($join) {
                $join->on('fpsbr.id', '=', 'last_fpsbr.last_record_id');
            })
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->whereDate('fpsbr.date_of_follow_up_visit', '<=', $today) // Not in the future
            ->where('p.status', '!=', 'Archived');

        if ($isStaff) {
            $familyPlanningQuery->join('family_planning_medical_records as fpmr', 'fpmr.medical_record_case_id', '=', 'mrc.id')
                ->where('fpmr.health_worker_id', $staff->id);
        }

        $familyPlanningCount = $familyPlanningQuery->distinct()->count('p.id');

        $overdueData[] = [
            'type' => 'family_planning',
            'label' => 'Family Planning Follow-up',
            'count' => $familyPlanningCount,
            'icon' => '👨‍👩‍👧‍👦',
            'route' => '/patient-record/family-planning/view-records',
            'color' => '#F44336'
        ];

        return $overdueData;
    }

    /**
     * Send consolidated overdue email
     */
    private function sendOverdueEmail($staff, $overdueData, $totalOverdue)
    {
        try {
            $emailData = [
                'staff_name' => $this->getStaffName($staff),
                'overdue_data' => $overdueData,
                'total_overdue' => $totalOverdue,
            ];

            Mail::to($staff->email)->send(new OverdueAppointmentsMail($emailData));

            $this->info("  ✓ Overdue email sent to: {$staff->email}");
        } catch (\Exception $e) {
            $this->error("  ✗ Overdue email failed for {$staff->email}: {$e->getMessage()}");
        }
    }

    /**
     * Create individual in-app notifications for each overdue type
     */
    private function createOverdueInAppNotifications($staff, $overdueData)
    {
        foreach ($overdueData as $overdue) {
            if ($overdue['count'] > 0) {
                try {
                    $message = "⚠️ {$overdue['count']} {$overdue['label']} " .
                        ($overdue['count'] === 1 ? 'patient has' : 'patients have') .
                        " overdue appointments that need attention.";

                    notifications::create([
                        'user_id' => $staff->id,
                        'patient_id' => null,
                        'type' => 'overdue_alert',
                        'title' => "⚠️ Overdue - {$overdue['label']}",
                        'message' => $message,
                        'appointment_type' => $overdue['type'],
                        'appointment_date' => null,
                        'medical_record_case_id' => null,
                        'link_url' => $overdue['route'],
                        'is_read' => false,
                        'created_at' => now(),
                    ]);

                    $this->info("  ✓ Overdue notification created: {$overdue['label']} ({$overdue['count']})");
                } catch (\Exception $e) {
                    $this->error("  ✗ Overdue notification failed for {$overdue['label']}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Get staff member's full name
     */
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
