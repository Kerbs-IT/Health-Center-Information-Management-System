<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="vaccinationModalLabel">Client Record Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="whole-table w-100 border-dark border-2">
            <div class="boxes">
                <div class="box w-100">
                    @include('records.familyPlanning.viewCaseComponent.step1')
                </div>
            </div>
            <div class="boxes d-flex w-100 ">
                <div class="col  w-50 border-r-2 border-dark">
                    <div class="box w-100 ">
                        @include('records.familyPlanning.viewCaseComponent.step2')
                    </div>
                    <div class="box w-100 ">
                        @include('records.familyPlanning.viewCaseComponent.step3')
                    </div>
                    <div class="box w-100">
                        @include('records.familyPlanning.viewCaseComponent.step4')
                    </div>
                    <div class="box w-100 p-3 ">
                        <p class="text-center">Implant=Progestin subdermal Implant,IUD= Intrauterine device, BTL= Bilateral tubal ligation, Nsy=No sceptal vasedomy,
                            COC= Combined ora; contraceptives, POP= Progestin only pills, LAM=Lactational amenorhes method, SOM= Standard days method,
                            ABT=Based body temperature, BOM= Billage ovulation method, CMMI= Cervical mucus method, STM= Symptothermal method
                        </p>
                    </div>
                </div>
                <div class="col w-50 ">
                    <div class="box w-100">
                        @include('records.familyPlanning.viewCaseComponent.step5')
                    </div>
                    <div class="box w-100">
                        @include('records.familyPlanning.viewCaseComponent.step6')
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>