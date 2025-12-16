<!DOCTYPE html>
<html lang="tl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano sa Oras ng Panganganak at Kagipitan</title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            .container {
                page-break-inside: avoid;
            }
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .header-text {
            padding-top: 10px;
        }

        .header-text h3 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-align: center;
        }

        .subtitle {
            font-size: 11pt;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-line {
            margin: 20px 0;
            font-size: 11pt;
            line-height: 1.8;
            display: flex;
            /* ADDED */
            flex-direction: column;
            /* ADDED */
        }

        .line-with-input {
            display: flex;
            align-items: baseline;
            margin-bottom: 5px;
        }

        .underline {
            flex: 1;
            /* CHANGED from width: calc(100% - 20px) */
            border-bottom: 1px solid #000;
            margin: 0 5px;
            min-height: 1.2em;
            /* ADDED */
        }

        .underline-full {
            display: block;
            border-bottom: 1px solid #000;
            width: 100%;
            margin: 10px 0;
        }

        .small-text {
            font-size: 10pt;
            font-style: italic;
        }

        .section {
            margin: 25px 0;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 45%;
            margin: 10px 2%;
        }
    </style>
</head>

<body>
    @vite([
    'resources/css/app.css',
    'resources/css/pdfTableTemplate.css'])
    <div class="container">
        <div class="content d-flex justify-content-between w-100 align-items-center mb-3">
            <img src="{{$treceLogo}}" class="pdf-logo">
            <div class="text-content">
                <h5 class="text-center fw-bold">CITH HEALTH OFFICE</h5>
                <h5 class="text-center fw-bold">Trece Martires Cavite</h5>
                <h5 class="text-center fw-bold">PRE-NATAL</h5>
            </div>
            <img src="{{ $DOHlogo }}" alt="logo" class="pdf-logo">
        </div>

        <div class="title">PLANO SA ORAS NG PANGANGANAK AT KAGIPITAN</div>
        <div class="subtitle">
            Alarn kong maaring magkaroon ng kumplikasyon sa oras ng panganganak. Alarn kong<br>
            dapat akong magantak sa isang ospital o pansakan.
        </div>

        <div class="form-line w-100">
            <div class="line-with-input">
                Ako ay paamkain ni <span class="underline fw-bold">{{$pregnancyPlan->midwife_name}}</span>
            </div>
            <span class="small-text text-center">(pangalan ng doctor / nars / midwife, atbp.)</span>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Plano kong manganak sa <span class="underline fw-bold">{{$pregnancyPlan->place_of_pregnancy}}</span>
            </div>
            <span class="small-text text-center">(pangalan ng ospital / lying-in center / maternity clinic)</span>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Ito ay pisilidad na otortado ng Philhealth Oo <span class="underline fw-bold" style="flex: 0 0 100px;">{{$pregnancyPlan->authorized_by_philhealth == 'yes'?'✓':''}}</span> Hindi <span class="underline fw-bold" style="flex: 0 0 100px;">{{$pregnancyPlan->authorized_by_philhealth != 'yes'?'✓':''}}</span>
            </div>
            <span class="small-text">(lagyan ng tsek)</span>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Ang tinatayong gastusin ng pangangangak sa pasilidad ay P <span class="underline fw-bold">{{$pregnancyPlan->cost_of_pregnancy ??''}}</span>
            </div>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Ang paraan ng pagbabayad ay <span class="underline fw-bold">{{$pregnancyPlan->payment_mode ??''}}</span>
            </div>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Ang maaring magamit na paraan ng pagbyrahe patungo na pasilidad ay <span class="underline fw-bold fw-bold">{{$pregnancyPlan->transportation_mode ??''}}</span>
            </div>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Kinansap ko na si <span class="underline fw-bold">{{$pregnancyPlan->accompany_person_to_hospital ??''}}</span> upang ako'y dalhin sa ospital /
            </div>
            <div>Klinikang panganganganak.</div>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Ako ay tanunahan ni <span class="underline fw-bold">{{$pregnancyPlan->accompany_through_pregnancy??''}}</span>
            </div>
        </div>

        <div class="form-line">
            <div class="line-with-input">
                Si <span class="underline fw-bold">{{$pregnancyPlan->care_person ??''}}</span> ang mangungalaga sa aking anak / bahay habang
            </div>
            <div>ako ay nasa ospital / pansakan.</div>
        </div>

        <div class="section">
            <div class="form-line">
                Kung sakaling mangailangan ng pagsalie ng dugo, ang maaring makagabigay ay sinu:
            </div>
            @forelse($pregnancyPlan ->donor_name as $name)
            <div class="signature-line">{{$name->donor_name}}</div>
            @empty
            <div class="signature-line"></div>
            <div class="signature-line"></div>
            <div class="signature-line"></div>
            <div class="signature-line"></div>
            @endforelse
        </div>

        <div class="section">
            <div class="form-line">
                Kung magkakaroon ng kumplikasyon, kailangan salihan kaagad si:
            </div>
            <div class="form-line">
                Pangalan: <span class="underline-full fw-bold">{{$pregnancyPlan->emergency_person_name??''}}</span>
            </div>
            <div class="form-line">
                Tirahan: <span class="underline-full fw-bold">{{$pregnancyPlan->emergency_person_residency??''}}</span>
            </div>
            <div class="form-line">
                Telepono: <span class="underline-full fw-bold">{{$pregnancyPlan->emergency_person_contact_number??''}}</span>
            </div>
        </div>

        <div class="signature-section">
            <div class="form-line">
                Pangalan ng panyente: <span class="underline fw-bold">{{$pregnancyPlan->patient_name??''}}</span>
            </div>
            <div class="form-line">
                Lagda: <span class="underline fw-bold"></span>
            </div>
        </div>
    </div>
</body>

</html>