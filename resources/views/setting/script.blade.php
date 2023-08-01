<script>
    let status_arr_meta = @json($arr_meta);

    let activateAutomation = "{{$activateAutomation}}";
    let statusAutomation = "{{$statusAutomation}}";
    let paymentAutomation = "{{$payment_type}}";
    let projectAutomation = "{{$projectAutomation}}";
    let saleschannelAutomation = "{{$saleschannelAutomation}}";
    let documentAutomation = "{{$documentAutomation}}";

    let automationDocument = "{{$automationDocument}}";
    let add_automationStore = "{{$add_automationStore}}";
    let add_automationPaymentDocument = "{{$add_automationPaymentDocument}}";

    window.document.getElementById('activateAutomation').value = activateAutomation


    if (projectAutomation != "") window.document.getElementById('projectAutomation').value = projectAutomation
    if (saleschannelAutomation != "") window.document.getElementById('saleschannelAutomation').value = saleschannelAutomation
    if (paymentAutomation != "") window.document.getElementById('paymentAutomation').value = paymentAutomation

    window.document.getElementById('automationDocument').value = automationDocument
    if (add_automationStore != "") window.document.getElementById('add_automationStore').value = add_automationStore
    if (add_automationPaymentDocument != "") window.document.getElementById('add_automationPaymentDocument').value = add_automationPaymentDocument

    if (documentAutomation != "") {
        window.document.getElementById('documentAutomation').value = documentAutomation
        if (documentAutomation == 1 || documentAutomation == "1") {
            FU_statusAutomation('demand')
        } else {
            FU_statusAutomation('customerorder')
        }
    } else {
        FU_statusAutomation('customerorder')
    }

    if (statusAutomation !== "" && statusAutomation != 0) {
        window.document.getElementById('statusAutomation').value = statusAutomation;
    }


    FU_activateAutomation(activateAutomation)
    FU_automationDocument(automationDocument)

    function FU_activateAutomation(params) {
        let view = window.document.getElementById('T1View')
        if (params === 1 || params === '1') {
            view.style.display = 'block'
            if (window.document.getElementById('T2').style.display === 'none') toggleClick(2)
        } else {
            view.style.display = 'none'
            if (window.document.getElementById('T2').style.display === 'block') toggleClick(2)
        }
    }

    function FU_automationDocument(params) {
        let view = window.document.getElementById('T2View')
        if (params != 1 || params != '1') {
            view.style.display = 'block'
        } else {
            view.style.display = 'none'
        }
    }

    function FU_statusAutomation(params) {
        let selectElement = document.getElementById("statusAutomation")
        while (selectElement.firstChild) {
            selectElement.removeChild(selectElement.firstChild);
        }
        if (params == 'customerorder') {
            for (let index = 0; index < (status_arr_meta.customerorder).length; index++) {
                let option1 = document.createElement("option")
                option1.text = status_arr_meta.customerorder[index].name
                option1.value = status_arr_meta.customerorder[index].name
                selectElement.appendChild(option1);
            }
        } else {
            if (params == 'demand') {
                for (let index = 0; index < (status_arr_meta.demand).length; index++) {
                    let option1 = document.createElement("option")
                    option1.text = status_arr_meta.demand[index].name
                    option1.value = status_arr_meta.demand[index].name
                    selectElement.appendChild(option1);
                }
            }
        }
    }


    function documentChangeDemand(params) {
        if (params === "1") {
            window.document.getElementById('ChangeDemand').style.display = "none"
            window.document.getElementById('ChangeDemand_children').style.display = "none"
            window.document.getElementById('T2View').style.display = "block"
            FU_statusAutomation('demand')
        } else {
            window.document.getElementById('ChangeDemand').style.display = "flex"
            window.document.getElementById('ChangeDemand_children').style.display = "flex"
            window.document.getElementById('T2View').style.display = "none"
            FU_statusAutomation('customerorder')
        }
    }

    function toggleClick(id) {

        if (id === 1) {
            let toggle_off = window.document.getElementById('toggle_off')
            let toggle_on = window.document.getElementById('toggle_on')

            let T1 = window.document.getElementById('T1')

            if (toggle_off.style.display == "none") {
                toggle_on.style.display = "none"
                toggle_off.style.display = "block"

                T1.style.display = 'block'
            } else {
                toggle_on.style.display = "block"
                toggle_off.style.display = "none"

                T1.style.display = 'none'
            }
        }

        if (id === 2) {
            let toggle_off = window.document.getElementById('toggle_off_2')
            let toggle_on = window.document.getElementById('toggle_on_2')

            let T1 = window.document.getElementById('T2')

            if (toggle_off.style.display == "none") {
                toggle_on.style.display = "none"
                toggle_off.style.display = "block"

                T1.style.display = 'block'
            } else {
                toggle_on.style.display = "block"
                toggle_off.style.display = "none"

                T1.style.display = 'none'
            }
        }

    }
</script>
