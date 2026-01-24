<!DOCTYPE html>
<html lang="tl">

<head>
    <meta charset="UTF-8">
    <title>Plano sa Oras ng Panganganak at Kagipitan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: 0;
            background: white;
            font-size: 9pt;
            line-height: 1.8;
        }

        .header {
            width: 100%;
            margin-bottom: 0px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: middle;
        }

        .header img {
            width: 100px;
            height: 100px;
        }

        .doh_image img {
            width: 120px !important;
        }

        .header .text-content {
            text-align: center;
            line-height: 25px;
        }

        .header h5 {
            margin: 0 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            margin: 0px 0 0px 0;
            text-align: center;
        }

        .subtitle {
            font-size: 11pt;
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.5;
        }

        .form-line {
            margin: 10px 0;
            font-size: 11pt;
        }

        .form-line table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-line td {
            padding: 0;
            vertical-align: bottom;
        }

        .label-cell {
            white-space: nowrap;
        }

        .underline-cell {
            width: 100%;
            border-bottom: 1px solid #000;
            font-weight: bold;
            padding-left: 10px !important;
        }

        .small-text {
            font-size: 10pt;
            font-style: italic;
            text-align: center;
            display: block;
            margin-top: 5px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .section {
            margin: 10px 0;
        }

        .section.donor-names {
            margin: 0px 0 !important;
        }

        .signature-section {
            margin-top: 10px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 45%;
            margin: 5px 2%;
            min-height: 20px;
        }

        .checkbox-table {
            display: inline;
        }

        .checkbox-table table {
            display: inline-table;
            border-collapse: collapse;
        }

        .checkbox-cell {
            border-bottom: 1px solid #000;
            text-align: center;
            padding: 0 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 15%; text-align: center;">
                    <img src="{{$treceLogo}}" alt="Trece Logo">
                </td>
                <td style="width: 70%;">
                    <div class="text-content">
                        <h5>Republic of the Philippines</h5>
                        <h5>Province of Cavite</h5>
                        <h5>CITH HEALTH OFFICE</h5>
                        <h5>Trece Martires City</h5>
                    </div>
                </td>
                <td style="width: 15% !important; text-align: center;object-fit:fill;" class="doh_image">
                    <img src="{{$DOHlogo}}" alt="DOH Logo">
                </td>
            </tr>
        </table>
    </div>

    <div class="title">PLANO SA ORAS NG PANGANGANAK AT KAGIPITAN</div>

    <div class="subtitle">
        Alarn kong maaring magkaroon ng kumplikasyon sa oras ng panganganak. Alarn kong<br>
        dapat akong magantak sa isang ospital o pansakan.
    </div>

    <div class="form-line" style="margin:0 0;">
        <table>
            <tr>
                <td class="label-cell">Ako ay paamkain ni</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->midwife_name}}</td>
            </tr>
        </table>
        <span class="small-text">(pangalan ng doctor / nars / midwife, atbp.)</span>
    </div>

    <div class="form-line" style="margin:0 0;">
        <table>
            <tr>
                <td class="label-cell">Plano kong manganak sa</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->place_of_pregnancy}}</td>
            </tr>
        </table>
        <span class="small-text">(pangalan ng ospital / lying-in center / maternity clinic)</span>
    </div>

    <div class="form-line" style="margin:0 0;">
        Ito ay pisilidad na otortado ng Philhealth Oo <span style="display: inline-block; border-bottom: 1px solid #000; width: 80px; text-align: center; font-weight: bold;">@if($pregnancyPlan->authorized_by_philhealth == 'yes')✓@else &nbsp; @endif</span> Hindi <span style="display: inline-block; border-bottom: 1px solid #000; width: 120px; text-align: center; font-weight: bold;">@if($pregnancyPlan->authorized_by_philhealth != 'yes')✓@else &nbsp; @endif</span>
        <span class="small-text">(lagyan ng tsek)</span>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Ang tinatayong gastusin ng pangangangak sa pasilidad ay P</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->cost_of_pregnancy ??''}}</td>
            </tr>
        </table>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Ang paraan ng pagbabayad ay</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->payment_method ??''}}</td>
            </tr>
        </table>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Ang maaring magamit na paraan ng pagbyrahe patungo na pasilidad ay</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->transportation_mode ??''}}</td>
            </tr>
        </table>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Kinansap ko na si</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->accompany_person_to_hospital ??''}}</td>
                <td class="label-cell">upang ako'y dalhin sa ospital / Klinikang panganganganak.</td>
            </tr>
        </table>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Ako ay tanunahan ni</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->accompany_through_pregnancy??''}}</td>
            </tr>
        </table>
    </div>

    <div class="form-line">
        <table>
            <tr>
                <td class="label-cell">Si</td>
                <td class="underline-cell fw-bold">{{$pregnancyPlan->care_person ??''}}</td>
                <td class="label-cell">ang mangungalaga sa aking anak / bahay habang ako ay nasa ospital / pansakan.</td>
            </tr>
        </table>
    </div>

    <div class="section donor-names">
        <div class="form-line">
            Kung sakaling mangailangan ng pagsalie ng dugo, ang maaring makagabigay ay sinu:
        </div>
        @for($i = 0; $i < 6; $i++)
            @if(isset($pregnancyPlan->donor_name[$i]))
            <div class="signature-line">{{$pregnancyPlan->donor_name[$i]->donor_name}}</div>
            @else
            <div class="signature-line">&nbsp;</div>
            @endif
            @endfor
    </div>

    <div class="section">
        <div class="form-line">
            Kung magkakaroon ng kumplikasyon, kailangan salihan kaagad si:
        </div>
        <div class="form-line">
            <table>
                <tr>
                    <td class="label-cell">Pangalan:</td>
                    <td class="underline-cell fw-bold">{{$pregnancyPlan->emergency_person_name??''}}</td>
                </tr>
            </table>
        </div>
        <div class="form-line">
            <table>
                <tr>
                    <td class="label-cell">Tirahan:</td>
                    <td class="underline-cell fw-bold">{{$pregnancyPlan->emergency_person_residency??''}}</td>
                </tr>
            </table>
        </div>
        <div class="form-line">
            <table>
                <tr>
                    <td class="label-cell">Telepono:</td>
                    <td class="underline-cell fw-bold">{{$pregnancyPlan->emergency_person_contact_number??''}}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="signature-section" style="padding-left:30px;">
        <div class="form-line">
            <table>
                <tr>
                    <td class="label-cell">Pangalan ng panyente:</td>
                    <td class="underline-cell fw-bold">{{$pregnancyPlan->patient_name??''}}</td>
                </tr>
            </table>
        </div>
        <div class="form-line">
            Lagda:
            @if($pregnancyPlan->signature != null)
            <img src="{{ storage_path('app/public/' . $pregnancyPlan->signature) }}" alt="Signature" style="max-width:400px; height:40px;">
            <div style="width: 100%;border-bottom:1px solid black; margin-top: 1px;"></div>
            @else
            <div style="width: 100%;border-bottom:1px solid black; min-height: 40px;">&nbsp;</div>
            @endif
        </div>
    </div>
</body>

</html>