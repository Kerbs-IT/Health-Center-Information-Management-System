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

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send email and in-app reminders to patients with appointments tomorrow (Runs at 8:00 PM)';

    public function handle()
    {
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
    // PROCESS REMINDER
    // CHANGE: Now calls createNotificationsForAll() instead of single
    //         createInAppNotification(). This ensures both patient and
    //         guardian receive in-app notifications.
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
    // NEW: createNotificationsForAll
    // Loops through both the patient's own user_id and guardian_user_id,
    // calling the provided $callback for each valid recipient.
    // =========================================================================
    private function createNotificationsForAll($reminder, callable $callback)
    {
        // Notify patient's own account if they have one
        if (!empty($reminder->user_id)) {
            $callback($reminder->user_id, $reminder->patient_id);
        }

        // Notify guardian's account if one is linked
        if (!empty($reminder->guardian_user_id)) {
            $callback($reminder->guardian_user_id, $reminder->patient_id);
        }
    }

    // =========================================================================
    // SEND EMAIL
    // CHANGE: Now also sends email to guardian if guardian_user_id exists.
    //         Guardian email is fetched via User::find().
    //         Added 'is_guardian' flag in emailData for the Mailable to use
    //         if you want a slightly different subject/body for guardians.
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

            // Send to patient's own email
            if (!empty($reminder->email)) {
                Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));
                $this->info("✓ Email sent to patient: {$reminder->full_name} ({$reminder->email}) - {$appointmentType}");
            }

            // Send to guardian's email if linked
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
    // NEW: createInAppNotificationForUser
    // CHANGE: Replaces old createInAppNotification($reminder, $type, $appointmentType).
    //         Now accepts explicit $userId and $patientId so it can be called
    //         for both the patient and the guardian separately.
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
    // NEW: createCompletionCheckupNotificationForUser
    // CHANGE: Replaces old createCompletionCheckupNotification($reminder).
    //         Same reason — accepts explicit $userId/$patientId so guardian
    //         can also receive the vaccination completion notification.
    // =========================================================================
    private function createCompletionCheckupNotificationForUser($reminder, $userId, $patientId)
    {
        try {
            $completedVaccinesList = implode(', ', $reminder->completed_vaccines);
            $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');
            $patientName = "<strong>{$reminder->full_name}</strong>";  // ADDED
            $message = "This is a reminder for {$patientName}'s vaccination completion checkup scheduled for tomorrow, {$date}. "  // CHANGED
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
    // BUILD NOTIFICATION MESSAGE — unchanged
    // =========================================================================
    private function buildNotificationMessage($reminder, $type, $appointmentType)
    {
        $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');
        $patientName = "<strong>{$reminder->full_name}</strong>";  // ADDED
        $baseMessage = "This is a reminder for {$patientName}'s {$appointmentType} scheduled for tomorrow, {$date}.";  // CHANGED

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
    // GET APPOINTMENT TYPE NAME — unchanged
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
    // CHANGE: Removed ->whereNotNull('p.user_id').
    //         Added ->where(fn($q) => ...) to include patients that have
    //         either a user_id OR a guardian_user_id.
    //         Added 'p.guardian_user_id' to select().
    // =========================================================================
    private function getVaccinationReminders($tomorrow)
    {
        $vaccineDoseConfig = [
            'BCG'                   => ['maxDoses' => 1,  'name' => 'BCG Vaccine'],
            'Hepatitis B'           => ['maxDoses' => 1,  'name' => 'Hepatitis B Vaccine'],
            'PENTA'                 => ['maxDoses' => 3,  'name' => 'Pentavalent Vaccine (DPT-HEP B-HIB)'],
            'OPV'                   => ['maxDoses' => 3,  'name' => 'Oral Polio Vaccine (OPV)'],
            'IPV'                   => ['maxDoses' => 2,  'name' => 'Inactived Polio Vaccine (IPV)'],
            'PCV'                   => ['maxDoses' => 3,  'name' => 'Pnueumococcal Conjugate Vaccine (PCV)'],
            'MMR'                   => ['maxDoses' => 2,  'name' => 'Measles, Mumps, Rubella Vaccine (MMR)'],
            'MCV'                   => ['maxDoses' => 1,  'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 1)'],
            'MCV_7'                 => ['maxDoses' => 2,  'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 7)'],
            'TD'                    => ['maxDoses' => 2,  'name' => 'Tetanus Diphtheria (TD)'],
            'Human Papiliomavirus'  => ['maxDoses' => 2,  'name' => 'Human Papiliomavirus Vaccine'],
            'Influenza Vaccine'     => ['maxDoses' => 3,  'name' => 'Influenza Vaccine'],
            'Pnuemococcal Vaccine'  => ['maxDoses' => 3,  'name' => 'Pnuemococcal Vaccine'],
        ];

        $rawReminders = DB::table('vaccination_case_records as vcr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('vcr.status', '!=', 'Archived')
            ->where('vcr.vaccination_status', 'completed')
            ->whereDate('vcr.date_of_comeback', $tomorrow)
            // CHANGED: include patients with user_id OR guardian_user_id
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',   // ADDED
                'vcr.date_of_comeback as appointment_date',
                'vcr.vaccine_type',
                'vcr.dose_number',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();

        return $rawReminders->map(function ($reminder) use ($vaccineDoseConfig) {
            $vaccines = explode(',', $reminder->vaccine_type ?? '');
            $currentDose = $reminder->dose_number;
            $hasIncompleteVaccine = false;
            $completedVaccines = [];
            $incompleteVaccines = [];

            foreach ($vaccines as $vaccine) {
                $vaccineAcronym = trim($vaccine);
                if (isset($vaccineDoseConfig[$vaccineAcronym])) {
                    $maxDoses   = $vaccineDoseConfig[$vaccineAcronym]['maxDoses'];
                    $vaccineName = $vaccineDoseConfig[$vaccineAcronym]['name'];
                    if ($currentDose < $maxDoses) {
                        $hasIncompleteVaccine = true;
                        $incompleteVaccines[] = $vaccineName;
                    } else {
                        $completedVaccines[] = $vaccineName;
                    }
                }
            }

            $reminder->is_all_complete    = !$hasIncompleteVaccine;
            $reminder->completed_vaccines = $completedVaccines;
            $reminder->incomplete_vaccines = $incompleteVaccines;

            return $reminder;
        });
    }

    // =========================================================================
    // GET PRENATAL REMINDERS
    // CHANGE: Removed ->whereNotNull('p.user_id').
    //         Added OR condition and 'p.guardian_user_id' to select().
    // =========================================================================
    private function getPrenatalReminders($tomorrow)
    {
        return DB::table('pregnancy_checkups as pc')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('pc.status', '!=', 'Archived')
            ->whereDate('pc.date_of_comeback', $tomorrow)
            // CHANGED
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',   // ADDED
                'pc.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET SENIOR CITIZEN REMINDERS
    // CHANGE: Removed ->whereNotNull('p.user_id').
    //         Added OR condition and 'p.guardian_user_id' to select().
    // =========================================================================
    private function getSeniorCitizenReminders($tomorrow)
    {
        return DB::table('senior_citizen_case_records as sccr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('sccr.status', '!=', 'Archived')
            ->whereDate('sccr.date_of_comeback', $tomorrow)
            // CHANGED
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',   // ADDED
                'sccr.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET TB DOTS REMINDERS
    // CHANGE: Removed ->whereNotNull('p.user_id').
    //         Added OR condition and 'p.guardian_user_id' to select().
    // =========================================================================
    private function getTbDotsReminders($tomorrow)
    {
        return DB::table('tb_dots_check_ups as tdcu')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('tdcu.status', '!=', 'Archived')
            ->whereDate('tdcu.date_of_comeback', $tomorrow)
            // CHANGED
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',   // ADDED
                'tdcu.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // GET FAMILY PLANNING REMINDERS
    // CHANGE: Removed ->whereNotNull('p.user_id').
    //         Added OR condition and 'p.guardian_user_id' to select().
    // =========================================================================
    private function getFamilyPlanningReminders($tomorrow)
    {
        return DB::table('family_planning_side_b_records as fpsbr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('fpsbr.status', '!=', 'Archived')
            ->whereDate('fpsbr.date_of_follow_up_visit', $tomorrow)
            // CHANGED
            ->where(function ($q) {
                $q->whereNotNull('p.user_id')
                    ->orWhereNotNull('p.guardian_user_id');
            })
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'p.guardian_user_id',   // ADDED
                'fpsbr.date_of_follow_up_visit as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    // =========================================================================
    // SEND COMPLETION CHECKUP REMINDER (vaccination)
    // CHANGE: Now also sends to guardian email if linked.
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

            // Send to patient
            if (!empty($reminder->email)) {
                Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));
                $this->info("✓ Completion checkup email sent to patient: {$reminder->full_name} ({$reminder->email})");
            }

            // Send to guardian
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
