<?php

namespace App\Services;

use App\Models\patients;
use App\Models\patient_addresses;
use App\Models\users_address;
use App\Models\medical_record_cases;
use App\Models\wra_masterlists;
use App\Models\vaccination_masterlists;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientUpdateService
{
    // ── Main entry point ──────────────────────────────────────────────────
    // Call this from any controller, pass the validated $data and patient $id
    public function updatePatientDetails(array $data, int $patientId): void
    {
        $patient        = patients::findOrFail($patientId);
        $patientAddress = patient_addresses::where('patient_id', $patientId)->firstOrFail();

        // ── Name components ───────────────────────────────────────────────
        $middle     = substr($data['middle_initial'] ?? '', 0, 1);
        $middle     = $middle ? strtoupper($middle) . '.' : null;
        $middleName = !empty($data['middle_initial'])
            ? ucwords(strtolower($data['middle_initial']))
            : '';
        $parts    = array_filter([
            strtolower($data['first_name']),
            $middle,
            strtolower($data['last_name']),
            $data['suffix'] ?? null,
        ]);
        $fullName  = ucwords(trim(implode(' ', $parts)));

        $age       = Carbon::parse($data['date_of_birth'])->age;
        $ageMonths = Carbon::parse($data['date_of_birth'])->diffInMonths(now());

        // ── Update patients table ─────────────────────────────────────────
        $patient->update([
            'first_name'     => ucwords(strtolower($data['first_name'])),
            'middle_initial' => $middleName,
            'last_name'      => ucwords(strtolower($data['last_name'])),
            'full_name'      => $fullName,
            'suffix'         => $data['suffix']         ?? null,
            'age'            => $age,
            'age_in_months'  => $ageMonths,
            'date_of_birth'  => $data['date_of_birth'],
            'place_of_birth' => $data['place_of_birth'] ?? null,
            'sex'            => $data['sex']             ?? null,
            'civil_status'   => $data['civil_status']   ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'nationality'    => $data['nationality']    ?? null,
        ]);

        // ── Update patient_addresses ──────────────────────────────────────
        $blk_n_street = explode(',', $data['street'], 2);
        $house_number = trim($blk_n_street[0] ?? '');
        $street       = trim($blk_n_street[1] ?? '');

        $patientAddress->update([
            'house_number' => $house_number,
            'street'       => $street,
            'purok'        => $data['brgy'] ?? $patientAddress->purok,
        ]);
        $patientAddress->refresh();

        $fullAddress = collect([
            $patientAddress->house_number,
            $patientAddress->street,
            $patientAddress->purok,
            $patientAddress->barangay ?? null,
            $patientAddress->city     ?? null,
            $patientAddress->province ?? null,
        ])->filter()->join(', ');

        // ── Sync linked user account if exists ────────────────────────────
        $this->syncLinkedUser($patient, $fullName, $fullAddress, $age, $data, $house_number, $street);

        // ── Cascade to all medical records ────────────────────────────────
        $this->cascadePatientUpdate($patient, $fullName, $fullAddress, $age, $ageMonths, $data);
    }

    // ── Sync user account ─────────────────────────────────────────────────
    private function syncLinkedUser(
        $patient,
        string $fullName,
        string $fullAddress,
        int $age,
        array $data,
        string $house_number,
        string $street
    ): void {
        // find linked user via user_id on patient or patient_record_id on user
        $linkedUser = null;

        if (!empty($patient->user_id)) {
            $linkedUser = User::find($patient->user_id);
        }

        // fallback: find user by patient_record_id
        if (!$linkedUser) {
            $linkedUser = User::where('patient_record_id', $patient->id)->first();
        }

        if (!$linkedUser) return;

        // make sure both sides are always in sync
        if (empty($patient->user_id)) {
            $patient->update(['user_id' => $linkedUser->id]);
        }
        if (empty($linkedUser->patient_record_id)) {
            $linkedUser->update(['patient_record_id' => $patient->id]);
        }

        $linkedUser->update([
            'first_name'     => ucwords(strtolower($data['first_name'])),
            'middle_initial' => !empty($data['middle_initial'])
                ? ucwords(strtolower($data['middle_initial']))
                : '',
            'last_name'      => ucwords(strtolower($data['last_name'])),
            'full_name'      => $fullName,
            'suffix'         => $data['suffix']         ?? null,
            'date_of_birth'  => $data['date_of_birth'],
            'contact_number' => $data['contact_number'] ?? null,
            'address'        => $fullAddress,
        ]);

        // sync users_address
        $userAddress = users_address::where('user_id', $linkedUser->id)->first();
        if ($userAddress) {
            $userAddress->update([
                'house_number' => $house_number,
                'street'       => $street,
                'purok'        => $data['brgy'],
            ]);
        }
    }

    // ── Cascade to medical records ────────────────────────────────────────
    private function cascadePatientUpdate(
        $patient,
        string $fullName,
        string $fullAddress,
        int $age,
        int $ageMonths,
        array $data
    ): void {
        $cases = medical_record_cases::where('patient_id', $patient->id)->get();

        foreach ($cases as $case) {
            switch ($case->type_of_case) {

                case 'family-planning':
                    if (DB::table('family_planning_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('family_planning_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update([
                                'client_name'           => $fullName,
                                'client_date_of_birth'  => $data['date_of_birth'],
                                'client_age'            => $age,
                                'client_address'        => $fullAddress,
                                'client_contact_number' => $data['contact_number'] ?? null,
                                'client_civil_status'   => $data['civil_status']   ?? null,
                                'client_suffix'         => $data['suffix']         ?? null,
                            ]);
                    }

                    if (DB::table('family_planning_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('family_planning_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (wra_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        wra_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_wra'   => $fullName,
                                'address'       => $fullAddress,
                                'date_of_birth' => $data['date_of_birth'],
                            ]);
                    }
                    break;

                case 'prenatal':
                    if (DB::table('pregnancy_checkups')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('pregnancy_checkups')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('pregnancy_plans')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('pregnancy_plans')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('prenatal_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('prenatal_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (wra_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        wra_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_wra'   => $fullName,
                                'address'       => $fullAddress,
                                'date_of_birth' => $data['date_of_birth'],
                            ]);
                    }
                    break;

                case 'senior-citizen':
                    if (DB::table('senior_citizen_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('senior_citizen_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('senior_citizen_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('senior_citizen_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }
                    break;

                case 'tb-dots':
                    if (DB::table('tb_dots_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('tb_dots_check_ups')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_check_ups')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('tb_dots_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }
                    break;

                case 'vaccination':
                    if (DB::table('vaccination_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('vaccination_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (vaccination_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        vaccination_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_child' => $fullName,
                                'Address'       => $fullAddress,
                                'sex'           => $data['sex']          ?? null,
                                'date_of_birth' => $data['date_of_birth'],
                                'age'           => $age,
                                'age_in_months' => $age === 0 ? $ageMonths : null,
                            ]);
                    }
                    break;
            }
        }
    }
}
