<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminderMail;
use App\Models\notifications;

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

    /**
     * Process reminder - Send both email AND create in-app notification
     */
    private function processReminder($reminder, $type)
    {
        $appointmentType = $this->getAppointmentTypeName($type);

        // For vaccination, determine if it's a completion checkup or next dose
        if ($type === 'vaccination') {
            if ($reminder->is_all_complete) {
                // All vaccines complete - send completion checkup reminder
                $this->sendCompletionCheckupReminder($reminder);
                $this->createCompletionCheckupNotification($reminder);
            } else {
                // Still has incomplete vaccines - send regular reminder
                $this->sendEmail($reminder, $type, $appointmentType);
                $this->createInAppNotification($reminder, $type, $appointmentType);
            }
        } else {
            // Other types (prenatal, senior citizen, etc.) - send regular reminder
            $this->sendEmail($reminder, $type, $appointmentType);
            $this->createInAppNotification($reminder, $type, $appointmentType);
        }
    }

    /**
     * Send reminder email
     */
    private function sendEmail($reminder, $type, $appointmentType)
    {
        try {
            $emailData = [
                'patient_name' => $reminder->full_name,
                'appointment_type' => $appointmentType,
                'appointment_date' => Carbon::parse($reminder->appointment_date)->format('F j, Y (l)'),
                'type' => $type,
            ];

            // Add vaccination-specific details
            if ($type === 'vaccination') {
                $emailData['vaccine_type'] = $reminder->vaccine_type;
                $emailData['dose_number'] = ($reminder->dose_number ?? 0) + 1;
            }

            if (date('H') == '22') {
                Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));
            }

            $this->info("✓ Email sent to: {$reminder->full_name} ({$reminder->email}) - {$appointmentType}");
        } catch (\Exception $e) {
            $this->error("✗ Email failed for {$reminder->email}: {$e->getMessage()}");
        }
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification($reminder, $type, $appointmentType)
    {
        try {
            // Prepare notification message
            $message = $this->buildNotificationMessage($reminder, $type, $appointmentType);
            $title = "Appointment Reminder - Tomorrow";

            // Create notification record
            notifications::create([
                'user_id' => $reminder->user_id,
                'patient_id' => $reminder->patient_id,
                'type' => 'appointment_reminder',
                'title' => $title,
                'message' => $message,
                'appointment_type' => $type,
                'appointment_date' => $reminder->appointment_date,
                'medical_record_case_id' => $reminder->medical_record_case_id,
                'is_read' => false,
                'created_at' => now(),
            ]);

            $this->info("✓ In-app notification created for: {$reminder->full_name} - {$appointmentType}");
        } catch (\Exception $e) {
            $this->error("✗ In-app notification failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }

    /**
     * Build notification message based on type
     */
    private function buildNotificationMessage($reminder, $type, $appointmentType)
    {
        $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');

        $baseMessage = "You have a {$appointmentType} scheduled for tomorrow, {$date}.";

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

    /**
     * Get appointment type display name
     */
    private function getAppointmentTypeName($type)
    {
        return [
            'vaccination' => 'Vaccination',
            'prenatal' => 'Prenatal Checkup',
            'senior_citizen' => 'Senior Citizen Checkup',
            'tb_dots' => 'TB DOTS Checkup',
            'family_planning' => 'Family Planning Follow-up',
        ][$type];
    }

    /**
     * Get vaccination reminders for tomorrow
     */
    /**
     * Get vaccination reminders for tomorrow
     */
    private function getVaccinationReminders($tomorrow)
    {
        // Define vaccine dose configurations using acronyms
        $vaccineDoseConfig = [
            'BCG' => ['acronym' => 'BCG', 'maxDoses' => 1, 'description' => 'at birth', 'name' => 'BCG Vaccine'],
            'Hepatitis B' => ['acronym' => 'Hepatitis B', 'maxDoses' => 1, 'description' => 'at birth', 'name' => 'Hepatitis B Vaccine'],
            'PENTA' => ['acronym' => 'PENTA', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pentavalent Vaccine (DPT-HEP B-HIB)'],
            'OPV' => ['acronym' => 'OPV', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Oral Polio Vaccine (OPV)'],
            'IPV' => ['acronym' => 'IPV', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Inactived Polio Vaccine (IPV)'],
            'PCV' => ['acronym' => 'PCV', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pnueumococcal Conjugate Vaccine (PCV)'],
            'MMR' => ['acronym' => 'MMR', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Measles, Mumps, Rubella Vaccine (MMR)'],
            'MCV' => ['acronym' => 'MCV', 'maxDoses' => 1, 'description' => 'dose 1', 'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 1)'],
            'MCV_7' => ['acronym' => 'MCV', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 7)'],
            'TD' => ['acronym' => 'TD', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Tetanus Diphtheria (TD)'],
            'Human Papiliomavirus' => ['acronym' => 'Human Papiliomavirus', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Human Papiliomavirus Vaccine'],
            'Influenza Vaccine' => ['acronym' => 'Influenza Vaccine', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Influenza Vaccine'],
            'Pnuemococcal Vaccine' => ['acronym' => 'Pnuemococcal Vaccine', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pnuemococcal Vaccine'],
        ];

        $rawReminders = DB::table('vaccination_case_records as vcr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('vcr.status', '!=', 'Archived')
            ->where('vcr.vaccination_status', 'completed')
            ->whereDate('vcr.date_of_comeback', $tomorrow)
            ->whereNotNull('p.user_id')
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'vcr.date_of_comeback as appointment_date',
                'vcr.vaccine_type',
                'vcr.dose_number',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();

        // Process reminders and mark completion status
        $processedReminders = $rawReminders->map(function ($reminder) use ($vaccineDoseConfig) {
            $vaccines = explode(',', $reminder->vaccine_type ?? '');
            $currentDose = $reminder->dose_number;
            $hasIncompleteVaccine = false;
            $completedVaccines = [];
            $incompleteVaccines = [];

            foreach ($vaccines as $vaccine) {
                $vaccineAcronym = trim($vaccine);

                if (isset($vaccineDoseConfig[$vaccineAcronym])) {
                    $maxDoses = $vaccineDoseConfig[$vaccineAcronym]['maxDoses'];
                    $vaccineName = $vaccineDoseConfig[$vaccineAcronym]['name'];

                    if ($currentDose < $maxDoses) {
                        $hasIncompleteVaccine = true;
                        $incompleteVaccines[] = $vaccineName;
                    } else {
                        $completedVaccines[] = $vaccineName;
                    }
                }
            }

            // Add flags to the reminder object
            $reminder->is_all_complete = !$hasIncompleteVaccine;
            $reminder->completed_vaccines = $completedVaccines;
            $reminder->incomplete_vaccines = $incompleteVaccines;

            return $reminder;
        });

        return $processedReminders;
    }

    /**
     * Get prenatal checkup reminders for tomorrow
     */
    private function getPrenatalReminders($tomorrow)
    {
        return DB::table('pregnancy_checkups as pc')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('pc.status', '!=', 'Archived')
            ->whereDate('pc.date_of_comeback', $tomorrow)
            ->whereNotNull('p.user_id')
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'pc.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    /**
     * Get senior citizen checkup reminders for tomorrow
     */
    private function getSeniorCitizenReminders($tomorrow)
    {
        return DB::table('senior_citizen_case_records as sccr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('sccr.status', '!=', 'Archived')
            ->whereDate('sccr.date_of_comeback', $tomorrow)
            ->whereNotNull('p.user_id')
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'sccr.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    /**
     * Get TB DOTS checkup reminders for tomorrow
     */
    private function getTbDotsReminders($tomorrow)
    {
        return DB::table('tb_dots_check_ups as tdcu')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('tdcu.status', '!=', 'Archived')
            ->whereDate('tdcu.date_of_comeback', $tomorrow)
            ->whereNotNull('p.user_id')
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'tdcu.date_of_comeback as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }

    /**
     * Get family planning follow-up reminders for tomorrow
     */
    private function getFamilyPlanningReminders($tomorrow)
    {
        return DB::table('family_planning_side_b_records as fpsbr')
            ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
            ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->where('fpsbr.status', '!=', 'Archived')
            ->whereDate('fpsbr.date_of_follow_up_visit', $tomorrow)
            ->whereNotNull('p.user_id')
            ->select(
                'p.id as patient_id',
                'p.full_name',
                'u.id as user_id',
                'u.email',
                'fpsbr.date_of_follow_up_visit as appointment_date',
                'mrc.id as medical_record_case_id'
            )
            ->distinct()
            ->get();
    }
    // for vaccination
    /**
     * Send completion checkup email
     */
    private function sendCompletionCheckupReminder($reminder)
    {
        try {
            $completedVaccinesList = implode(', ', $reminder->completed_vaccines);

            $emailData = [
                'patient_name' => $reminder->full_name,
                'appointment_type' => 'Vaccination Completion Checkup',
                'appointment_date' => Carbon::parse($reminder->appointment_date)->format('F j, Y (l)'),
                'type' => 'vaccination_completion',
                'completed_vaccines' => $completedVaccinesList,
            ];

            Mail::to($reminder->email)->send(new AppointmentReminderMail($emailData));

            $this->info("✓ Completion checkup email sent to: {$reminder->full_name} ({$reminder->email}) - Vaccines completed: {$completedVaccinesList}");
        } catch (\Exception $e) {
            $this->error("✗ Email failed for {$reminder->email}: {$e->getMessage()}");
        }
    }

    /**
     * Create completion checkup in-app notification
     */
    private function createCompletionCheckupNotification($reminder)
    {
        try {
            $completedVaccinesList = implode(', ', $reminder->completed_vaccines);
            $date = Carbon::parse($reminder->appointment_date)->format('F j, Y (l)');

            $message = "You have a vaccination completion checkup scheduled for tomorrow, {$date}. "
                . "Completed vaccines: {$completedVaccinesList}. "
                . "This is for final verification and to discuss any additional vaccinations if needed.";

            notifications::create([
                'user_id' => $reminder->user_id,
                'patient_id' => $reminder->patient_id,
                'type' => 'appointment_reminder',
                'title' => 'Vaccination Completion Checkup - Tomorrow',
                'message' => $message,
                'appointment_type' => 'vaccination_completion',
                'appointment_date' => $reminder->appointment_date,
                'medical_record_case_id' => $reminder->medical_record_case_id,
                'is_read' => false,
                'created_at' => now(),
            ]);

            $this->info("✓ Completion checkup notification created for: {$reminder->full_name}");
        } catch (\Exception $e) {
            $this->error("✗ In-app notification failed for {$reminder->full_name}: {$e->getMessage()}");
        }
    }
}


