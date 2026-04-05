<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminderMail;
use App\Models\notifications;
use App\Models\NotificationSchedule;
use App\Models\User;
use App\Models\vaccines;  // ✅ ADD THIS

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send email and in-app reminders to patients with appointments tomorrow (Runs at 8:00 PM)';

    // ✅ REMOVE the hardcoded array - we'll load it dynamically
    private $vaccineDoseConfig = [];

    public function handle()
    {
        // ✅ LOAD vaccine config from database at the start
        $this->loadVaccineDoseConfig();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $remindersSent = 0;

        $this->info('===========================================');
        $this->info('Checking for appointments tomorrow: ' . $tomorrow);
        $this->info('Current time: ' . now()->format('Y-m-d H:i:s'));
        $this->info('===========================================');

        // 1. VACCINATION REMINDERS
        $vaccinationReminders = $this->getVaccinationReminders($tomorrow);
        foreach ($vaccinationReminders as $reminder) {
            $this->processReminder($reminder, 'vaccination');
            $remindersSent++;
        }

        // 2. PRENATAL REMINDERS
        $prenatalReminders = $this->getPrenatalReminders($tomorrow);
        foreach ($prenatalReminders as $reminder) {
            $this->processReminder($reminder, 'prenatal');
            $remindersSent++;
        }

        // 3. SENIOR CITIZEN REMINDERS
        $seniorCitizenReminders = $this->getSeniorCitizenReminders($tomorrow);
        foreach ($seniorCitizenReminders as $reminder) {
            $this->processReminder($reminder, 'senior_citizen');
            $remindersSent++;
        }

        // 4. TB DOTS REMINDERS
        $tbDotsReminders = $this->getTbDotsReminders($tomorrow);
        foreach ($tbDotsReminders as $reminder) {
            $this->processReminder($reminder, 'tb_dots');
            $remindersSent++;
        }

        // 5. FAMILY PLANNING REMINDERS
        $familyPlanningReminders = $this->getFamilyPlanningReminders($tomorrow);
        foreach ($familyPlanningReminders as $reminder) {
            $this->processReminder($reminder, 'family_planning');
            $remindersSent++;
        }

        $this->info('===========================================');
        $this->info("✓ Total reminders sent: {$remindersSent}");
        $this->info('===========================================');

        return 0;
    }

    // =========================================================================
    // ✅ NEW: Load vaccine config from database
    // =========================================================================
    private function loadVaccineDoseConfig()
    {
        try {
            $vaccines = vaccines::all();

            foreach ($vaccines as $vaccine) {
                // Use vaccine_acronym as the key (matches how it's stored in vaccine_type field)
                $this->vaccineDoseConfig[$vaccine->vaccine_acronym] = [
                    'maxDoses' => $vaccine->max_doses,
                    'name'     => $vaccine->type_of_vaccine,
                ];
            }

            $this->info("✓ Loaded " . count($this->vaccineDoseConfig) . " vaccine configurations from database");
        } catch (\Exception $e) {
            $this->error("✗ Failed to load vaccine config: " . $e->getMessage());
            // Fallback to empty array - no reminders will be sent if vaccines can't be loaded
            $this->vaccineDoseConfig = [];
        }
    }

    // =========================================================================
    // PROCESS REMINDER
    // =========================================================================
    private function processReminder($reminder, $type)
    {
        if (!NotificationSchedule::shouldRun('appointment-reminder')) {
            return;
        }

        $appointmentType = $this->getAppointmentTypeName($type);

        if ($type === 'vaccination') {
            if ($reminder->is_all_complete) {
                $this->sendCompletionCheckupReminder($reminder);
                $this->createNotificationsForAll($reminder, function ($userId, $patientId) use ($reminder) {
                    $this->createCompletionCheckupNotificationForUser($reminder, $userId, $patientId);
                });
            } else {
                $this->sendEmail($reminder, $type, $appointmentType);
                $this->createNotificationsForAll($reminder, function ($userId, $patientId) use ($reminder, $type, $appointmentType) {
                    $this->createInAppNotificationForUser($reminder, $type, $appointmentType, $userId, $patientId);
                });
            }
        } else {
            $this->sendEmail($reminder, $type, $appointmentType);
            $this->createNotificationsForAll($reminder, function ($userId, $patientId) use ($reminder, $type, $appointmentType) {
                $this->createInAppNotificationForUser($reminder, $type, $appointmentType, $userId, $patientId);
            });
        }
    }

    // =========================================================================
    // createNotificationsForAll
    // =========================================================================
    private function createNotificationsForAll($reminder, callable $callback)
    {
        if (!empty($reminder->user_id)) {
            $callback($reminder->user_id, $reminder->patient_id);
        }

        if (!empty($reminder->guardian_user_id)) {
            $callback($reminder->guardian_user_id, $reminder->patient_id);
        }
    }

    // =========================================================================
    // SEND EMAIL
    // =========================================================================
    private function sendEmail($reminder, $type, $appointmentType)
    {
        try {
            $emailData = [
                'patient_name'     => $reminder->full_name,
                'appointment_type' => $appointmentType,
                'appointment_date' => Carbon::parse($reminder->appointment_date)->format('F j, Y (l)'),
                'type'             => $type,
                'is_guardian'      => false,
            ];

            if ($type === 'vaccination') {
                $emailData['vaccine_type'] = $reminder->vaccine_type;
                $emailData['dose_number']  = ($reminder->dose_number ?? 0) + 1;
            }

            if (!empty($reminder->email)) {
                Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));
                $this->info("✓ Email sent to patient: {$reminder->full_name} ({$reminder->email}) - {$appointmentType}");
            }

            if (!empty($reminder->guardian_user_id)) {
                $guardian = User::find($reminder->guardian_user_id);
                if ($guardian && $guardian->email) {
                    $guardianEmailData = array_merge($emailData, ['is_guardian' => true]);
                    Mail::to($guardian->email)->send(new AppointmentReminderMail($guardianEmailData));
                    $this->info("✓ Email sent to guardian: {$guardian->email} (for patient: {$reminder->full_name}) - {$appointmentType}");
                }
            }
        } catch (\Exception $e) {
            $this->error("✗ Email failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }

    // =========================================================================
    // createInAppNotificationForUser
    // =========================================================================
    private function createInAppNotificationForUser($reminder, $type, $appointmentType, $userId, $patientId)
    {
        try {
            $message = $this->buildNotificationMessage($reminder, $type, $appointmentType);

            notifications::create([
                'user_id'                => $userId,
                'patient_id'             => $patientId,
                'type'                   => 'appointment_reminder',
                'title'                  => 'Appointment Reminder - Tomorrow',
                'message'                => $message,
                'appointment_type'       => $type,
                'appointment_date'       => $reminder->appointment_date,
                'medical_record_case_id' => $reminder->medical_record_case_id,
                'is_read'                => false,
                'created_at'             => now(),
            ]);

            $this->info("✓ In-app notification created for user_id: {$userId} (patient: {$reminder->full_name}) - {$appointmentType}");
        } catch (\Exception $e) {
            $this->error("✗ In-app notification failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }

    // =========================================================================
    // createCompletionCheckupNotificationForUser
    // =========================================================================
    private function createCompletionCheckupNotificationForUser($reminder, $userId, $patientId)
    {
        try {
            $completedVaccinesList = implode(', ', $reminder->completed_vaccines);
            $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');
            $patientName = "<strong>{$reminder->full_name}</strong>";
            $message = "This is a reminder for {$patientName}'s vaccination completion checkup scheduled for tomorrow, {$date}. "
                . "Completed vaccines: {$completedVaccinesList}. "
                . "This is for final verification and to discuss any additional vaccinations if needed.";

            notifications::create([
                'user_id'                => $userId,
                'patient_id'             => $patientId,
                'type'                   => 'appointment_reminder',
                'title'                  => 'Vaccination Completion Checkup - Tomorrow',
                'message'                => $message,
                'appointment_type'       => 'vaccination_completion',
                'appointment_date'       => $reminder->appointment_date,
                'medical_record_case_id' => $reminder->medical_record_case_id,
                'is_read'                => false,
                'created_at'             => now(),
            ]);

            $this->info("✓ Completion checkup notification created for user_id: {$userId} (patient: {$reminder->full_name})");
        } catch (\Exception $e) {
            $this->error("✗ Completion checkup notification failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }

    // =========================================================================
    // BUILD NOTIFICATION MESSAGE
    // =========================================================================
    private function buildNotificationMessage($reminder, $type, $appointmentType)
    {
        $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');
        $patientName = "<strong>{$reminder->full_name}</strong>";
        $baseMessage = "This is a reminder for {$patientName}'s {$appointmentType} scheduled for tomorrow, {$date}.";

        if ($type === 'vaccination') {
            $nextDose = ($reminder->dose_number ?? 0) + 1;
            return $baseMessage . " Vaccine: {$reminder->vaccine_type} - Dose {$nextDose}. Please bring your vaccination card.";
        }
        if ($type === 'prenatal') {
            return $baseMessage . " Please bring your prenatal booklet and don't forget to take your vitamins.";
        }
        if ($type === 'senior_citizen') {
            return $baseMessage . " Please bring your senior citizen ID and health records.";
        }
        if ($type === 'tb_dots') {
            return $baseMessage . " Please continue taking your TB medication as prescribed.";
        }
        if ($type === 'family_planning') {
            return $baseMessage . " Please bring your family planning records.";
        }

        return $baseMessage . " Please arrive 10-15 minutes early.";
    }

    // =========================================================================
    // GET APPOINTMENT TYPE NAME
    // =========================================================================
    private function getAppointmentTypeName($type)
    {
        return [
            'vaccination'     => 'Vaccination',
            'prenatal'        => 'Prenatal Checkup',
            'senior_citizen'  => 'Senior Citizen Checkup',
            'tb_dots'         => 'TB DOTS Checkup',
            'family_planning' => 'Family Planning Follow-up',
        ][$type];
    }

    // =========================================================================
    // GET VACCINATION REMINDERS
    // ✅ NOW USES $this->vaccineDoseConfig loaded from database
    // =========================================================================
    private function getVaccinationReminders($tomorrow)
    {
        $rawReminders = DB::table('vaccination_case_records as vcr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('vcr.status', '!=', 'Archived')
            ->where('vcr.vaccination_status', 'completed')
            ->whereDate('vcr.date_of_comeback', $tomorrow)
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',
                'vcr.date_of_comeback as appointment_date',
                'vcr.vaccine_type',
                'vcr.dose_number',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();

        return $rawReminders->map(function ($reminder) {
            $vaccines = explode(',', $reminder->vaccine_type ?? '');
            $currentDose = $reminder->dose_number;
            $hasIncompleteVaccine = false;
            $completedVaccines = [];
            $incompleteVaccines = [];

            foreach ($vaccines as $vaccine) {
                $vaccineAcronym = trim($vaccine);
                // ✅ Use the dynamically loaded config
                if (isset($this->vaccineDoseConfig[$vaccineAcronym])) {
                    $maxDoses    = $this->vaccineDoseConfig[$vaccineAcronym]['maxDoses'];
                    $vaccineName = $this->vaccineDoseConfig[$vaccineAcronym]['name'];

                    if ($currentDose < $maxDoses) {
                        $hasIncompleteVaccine = true;
                        $incompleteVaccines[] = $vaccineName;
                    } else {
                        $completedVaccines[] = $vaccineName;
                    }
                }
            }

            $reminder->is_all_complete     = !$hasIncompleteVaccine;
            $reminder->completed_vaccines  = $completedVaccines;
            $reminder->incomplete_vaccines = $incompleteVaccines;

            return $reminder;
        });
    }

    // =========================================================================
    // GET PRENATAL REMINDERS
    // =========================================================================
    private function getPrenatalReminders($tomorrow)
    {
        return DB::table('pregnancy_checkups as pc')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('pc.status', '!=', 'Archived')
            ->whereDate('pc.date_of_comeback', $tomorrow)
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',
                'pc.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET SENIOR CITIZEN REMINDERS
    // =========================================================================
    private function getSeniorCitizenReminders($tomorrow)
    {
        return DB::table('senior_citizen_case_records as sccr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('sccr.status', '!=', 'Archived')
            ->whereDate('sccr.date_of_comeback', $tomorrow)
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',
                'sccr.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET TB DOTS REMINDERS
    // =========================================================================
    private function getTbDotsReminders($tomorrow)
    {
        return DB::table('tb_dots_check_ups as tdcu')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('tdcu.status', '!=', 'Archived')
            ->whereDate('tdcu.date_of_comeback', $tomorrow)
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',
                'tdcu.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET FAMILY PLANNING REMINDERS
    // =========================================================================
    private function getFamilyPlanningReminders($tomorrow)
    {
        return DB::table('family_planning_side_b_records as fpsbr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('fpsbr.status', '!=', 'Archived')
            ->whereDate('fpsbr.date_of_follow_up_visit', $tomorrow)
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',
                'fpsbr.date_of_follow_up_visit as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // SEND COMPLETION CHECKUP REMINDER
    // =========================================================================
    private function sendCompletionCheckupReminder($reminder)
    {
        try {
            $completedVaccinesList = implode(', ', $reminder->completed_vaccines);

            $emailData = [
                'patient_name'       => $reminder->full_name,
                'appointment_type'   => 'Vaccination Completion Checkup',
                'appointment_date'   => Carbon::parse($reminder->appointment_date)->format('F j, Y (l)'),
                'type'               => 'vaccination_completion',
                'completed_vaccines' => $completedVaccinesList,
                'is_guardian'        => false,
            ];

            if (!empty($reminder->email)) {
                Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));
                $this->info("✓ Completion checkup email sent to patient: {$reminder->full_name} ({$reminder->email})");
            }

            if (!empty($reminder->guardian_user_id)) {
                $guardian = User::find($reminder->guardian_user_id);
                if ($guardian && $guardian->email) {
                    $guardianEmailData = array_merge($emailData, ['is_guardian' => true]);
                    Mail::to($guardian->email)->send(new AppointmentReminderMail($guardianEmailData));
                    $this->info("✓ Completion checkup email sent to guardian: {$guardian->email} (for patient: {$reminder->full_name})");
                }
            }
        } catch (\Exception $e) {
            $this->error("✗ Completion checkup email failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }
}
