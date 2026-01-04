<!-- resources/views/immunization/card.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immunization Card</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }


        .card-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .patient-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .info-field {
            display: flex;
            gap: 10px;
        }

        .info-field label {
            font-weight: bold;
            min-width: 150px;
        }

        .info-field span {
            flex: 1;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        .vaccine-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .vaccine-table th {
            background: #f57c00;
            color: black;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .vaccine-table td {
            padding: 10px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        td {
            font-size: 13px;
        }

        .vaccine-table tr:hover td {
            background: #f0f0f0;
        }

        .vaccine-name {
            font-weight: 500;
        }

        .dose-cell {
            text-align: center;
            min-width: 80px;
            position: relative;
        }

        .dose-cell.filled {
            background: #e8f5e9;
            font-weight: bold;
            color: #2e7d32;
        }

        .dose-cell.empty {
            background: #fff;
        }

        .category-header {
            background: #ff9800 !important;
            color: black;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .remarks-cell {
            max-width: 200px;
            word-wrap: break-word;
        }

        .action-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2196F3;
            color: white;
        }

        .btn-success {
            background: #4CAF50;
            color: white;
        }

        .btn-print {
            background: #607D8B;
            color: white;
        }

        @media print {
            .action-buttons {
                display: none;
            }

            body {
                padding: 0;
                background: white;
            }

            .card-container {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    @vite([
    'resources/css/app.css'])
    <div class="card-container">

        <div class="header">
            <h1 class="mb-0 text-white">IMMUNIZATION CARD</h1>
        </div>

        <div class="patient-info-con d-flex justify-content-between">
            <div class="w-50">
                <div class="info-field">
                    <label>NAME:</label>
                    <span>{{ $caseRecord->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>DATE OF BIRTH:</label>
                    <span>{{ $caseRecord->date_of_birth ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>PLACE OF BIRTH:</label>
                    <span>{{$caseRecord->place_of_birth ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>Address:</label>
                    <span>{{ $fullAddress?? 'N/A' }}</span>
                </div>
            </div>
            <div class="w-50">
                <div class="info-field">
                    <label>MOTHER'S NAME:</label>
                    <span>{{ $medicalRecord->vaccination_medical_record->mother_name?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>FATHER'S NAME:</label>
                    <span>{{ $medicalRecord->vaccination_medical_record->father_name?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label>BIRTH HEIGHT:</label>
                    <span>{{$medicalRecord->vaccination_medical_record->birth_height?? 'N/A'}} cm</span>
                </div>
                <div class="info-field">
                    <label>BIRTH WEIGHT:</label>
                    <span>{{ $medicalRecord->vaccination_medical_record->birth_weight?? 'N/A' }} kg</span>
                </div>
                <div class="info-field">
                    <label>SEX:</label>
                    <span>{{$caseRecord->sex ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>CONTACT NO.</label>
                    <span>{{$caseRecord->contact_number ?? 'N/A' }}</span>
                </div>
                
            </div>

        </div>
        <table class="vaccine-table">
            <thead>
                <tr>
                    <th style="width: 30%;" class="text-center">BAKUNA</th>
                    <th style="width: 20%;" class="text-center">DOSES</th>
                    <th colspan="3" style="text-align: center;" class="text-center">PETSA NG BAKUNA</th>
                    <th style="width: 20%;" class="text-center">REMARKS</th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th class="dose-cell">1</th>
                    <th class="dose-cell">2</th>
                    <th class="dose-cell">3</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                // Group vaccines by category based on vaccine_acronym or type
                $infantVaccines = collect([
                ['id' => 1, 'name' => 'BCG Vaccine', 'acronym' => 'BCG'],
                ['id' => 2, 'name' => 'Hepatitis B Vaccine', 'acronym' => 'Hepatitis B'],
                ['id' => 3, 'name' => 'Pentavalent Vaccine (DPT-HEP B-HIB)', 'acronym' => 'PENTA'],
                ['id' => 4, 'name' => 'Oral Polio Vaccine (OPV)', 'acronym' => 'OPV'],
                ['id' => 5, 'name' => 'Inactivated Polio Vaccine (IPV)', 'acronym' => 'IPV'],
                ['id' => 6, 'name' => 'Pneumococcal Conjugate Vaccine (PCV)', 'acronym' => 'PCV'],
                ['id' => 7, 'name' => 'Measles, Mumps, Rubella Vaccine (MMR)', 'acronym' => 'MMR'],
                ]);

                $schoolVaccines = collect([
                ['id' => 8, 'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 1)', 'acronym' => 'MCV'],
                ['id' => 9, 'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 7)', 'acronym' => 'MCV'],
                ['id' => 10, 'name' => 'Tetanus Diphtheria (TD)', 'acronym' => 'TD'],
                ['id' => 11, 'name' => 'Human Papillomavirus Vaccine', 'acronym' => 'Human Papillomavirus'],
                ]);

                $seniorVaccines = collect([
                ['id' => 12, 'name' => 'Influenza Vaccine', 'acronym' => 'Influenza Vaccine'],
                ['id' => 13, 'name' => 'Pneumococcal Vaccine', 'acronym' => 'Pneumococcal Vaccine'],
                ]);
                @endphp

                {{-- INFANT VACCINES --}}
                @foreach($infantVaccines as $vaccine)
                <tr>
                    <td class="vaccine-name">{{ $vaccine['name'] }}</td>
                    <td>
                        @switch($vaccine['id'])
                        @case(1)
                        @case(2)
                        At Birth
                        @break
                        @case(3)
                        @case(4)
                        @case(6)
                        1 1/2, 2 1/2, 3 1/2 Months
                        @break
                        @case(5)
                        3 1/2 & 9 months
                        @break
                        @case(7)
                        9 months & 1 Year
                        @break
                        @endswitch
                    </td>

                    @for($dose = 1; $dose <= 3; $dose++)
                        @php
                        // Find vaccination record for this vaccine and dose
                        $record=$vaccineAdministered->first(function($item) use ($vaccine, $dose) {
                        return $item->vaccine_id == $vaccine['id'] && $item->dose_number == $dose;
                        });
                        @endphp
                        <td class="dose-cell {{ $record ? 'filled' : 'empty' }}">
                            @if($record)
                            {{ \Carbon\Carbon::parse($record->vaccination_case_record->date_of_vaccination)->format('m/d/Y') }}
                            @endif
                        </td>
                        @endfor

                        <td class="remarks-cell">
                            @php
                            // Collect all remarks for this vaccine
                            $remarks = $vaccineAdministered
                            ->where('vaccine_id', $vaccine['id'])
                            ->map(function($item) {
                            return $item->vaccination_case_record->remarks ?? null;
                            })
                            ->filter()
                            ->unique()
                            ->implode(', ');
                            @endphp
                            {{ $remarks }}
                        </td>
                </tr>
                @endforeach

                {{-- SCHOOL AGED CHILDREN --}}
                <tr>
                    <td colspan="6" class="category-header">SCHOOL AGED CHILDREN</td>
                </tr>

                @foreach($schoolVaccines as $vaccine)
                <tr>
                    <td class="vaccine-name">{{ $vaccine['name'] }}</td>
                    <td>
                        @switch($vaccine['id'])
                        @case(8)
                        (Grade 1)
                        @break
                        @case(9)
                        (Grade 7)
                        @break
                        @case(10)
                        (Grade 1 & 7)
                        @break
                        @case(11)
                        (Grade 4 FEMALE 9-14 Years Old)
                        @break
                        @endswitch
                    </td>

                    @for($dose = 1; $dose <= 3; $dose++)
                        @php
                        $record=$vaccineAdministered->first(function($item) use ($vaccine, $dose) {
                        return $item->vaccine_id == $vaccine['id'] && $item->dose_number == $dose;
                        });
                        @endphp
                        <td class="dose-cell {{ $record ? 'filled' : 'empty' }}">
                            @if($record)
                            {{ \Carbon\Carbon::parse($record->vaccination_case_record->date_of_vaccination)->format('m/d/Y') }}
                            @endif
                        </td>
                        @endfor

                        <td class="remarks-cell">
                            @php
                            $remarks = $vaccineAdministered
                            ->where('vaccine_id', $vaccine['id'])
                            ->map(function($item) {
                            return $item->vaccination_case_record->remarks ?? null;
                            })
                            ->filter()
                            ->unique()
                            ->implode(', ');
                            @endphp
                            {{ $remarks }}
                        </td>
                </tr>
                @endforeach

                {{-- SENIOR CITIZEN --}}
                <tr>
                    <td colspan="6" class="category-header">SENIOR CITIZEN</td>
                </tr>

                @foreach($seniorVaccines as $vaccine)
                <tr>
                    <td class="vaccine-name">{{ $vaccine['name'] }}</td>
                    <td></td>

                    @for($dose = 1; $dose <= 3; $dose++)
                        @php
                        $record=$vaccineAdministered->first(function($item) use ($vaccine, $dose) {
                        return $item->vaccine_id == $vaccine['id'] && $item->dose_number == $dose;
                        });
                        @endphp
                        <td class="dose-cell {{ $record ? 'filled' : 'empty' }}">
                            @if($record)
                            {{ \Carbon\Carbon::parse($record->vaccination_case_record->date_of_vaccination)->format('m/d/Y') }}
                            @endif
                        </td>
                        @endfor

                        <td class="remarks-cell">
                            @php
                            $remarks = $vaccineAdministered
                            ->where('vaccine_id', $vaccine['id'])
                            ->map(function($item) {
                            return $item->vaccination_case_record->remarks ?? null;
                            })
                            ->filter()
                            ->unique()
                            ->implode(', ');
                            @endphp
                            {{ $remarks }}
                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>