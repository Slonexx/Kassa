<script>

    function createScript(){
        let i = 0
        if (gridChild.some((element) => element === false)){
            i = gridChild.findIndex((element) => element === false)
            let entity = "entity_"+i
            let status = "status_"+i
            let project = "project_"+i
            let saleschannel = "saleschannel_"+i


            $('#mainCreate').append('<div id="child_'+ i +'" class="mt-2 row">' +
                '<div class="col-2"> <select onchange="FU_statusAutomation('+ status +', '+ entity +', '+ project +', '+ saleschannel +')" id="entity_'+ i +'" name="entity_'+ i +'" class="form-select text-black"> <option value="0">Заказ покупателя</option> <option value="1">Отгрузки</option> <option value="2">Возврат покупателя</option> </select> </div>' +
                '<div class="col-2"> <select id="status_'+ i +'" name="status_'+ i +'" class="form-select text-black"> </select> </div>' +
                '<div class="col-2"> <select id="payment_'+ i +'" name="payment_'+ i +'" class="form-select text-black"> <option value="0">Наличные</option> <option value="1">Карта</option> <option value="2">Мобильная</option> <option value="3">От выбора справочника</option> </select> </div>' +
                '<div class="col-2"> <select onchange="FU_saleschannelAutomation('+ saleschannel +', '+ entity +')" id="saleschannel_'+ i +'" name="saleschannel_'+ i +'" class="form-select text-black "> <option value="0"> Не выбирать</option> </select> </div>' +
                '<div class="col-2"> <select onchange="FU_projectAutomation('+ project +', '+ entity +')" id="project_'+ i +'" name="project_'+ i +'" class="form-select text-black "> <option value="0"> Не выбирать</option> </select> </div>' +
                '<div class="col-2 text-center"> <span onclick="deleteScript('+ i +')" class="fa-solid fa-rectangle-xmark" style="font-size: 30px; cursor: pointer"> </span> </div>' +
                '</div>')
            gridChild[i] = true
            FU_statusAutomation( window.document.getElementById('status_'+i), window.document.getElementById('entity_'+i),  window.document.getElementById('project_'+i), window.document.getElementById('saleschannel_'+i) )
        }
    }


    if (Saved.length > 0){
        for (let i = 0; i < Saved.length; i++){
            if (gridChild.some((element) => element === false)){
                i = gridChild.findIndex((element) => element === false)
                let entity = "entity_"+i
                let status = "status_"+i
                let payment = "payment_"+i
                let project = "project_"+i
                let saleschannel = "saleschannel_"+i


                $('#mainCreate').append('<div id="child_'+ i +'" class="mt-2 row">' +
                    '<div class="col-2"> <select onchange="FU_statusAutomation('+ status +', '+ entity +', '+ project +', '+ saleschannel +')" id="entity_'+ i +'" name="entity_'+ i +'" class="form-select text-black"> <option value="0">Заказ покупателя</option> <option value="1">Отгрузки</option> <option value="2">Возврат покупателя</option> </select> </div>' +
                    '<div class="col-2"> <select id="status_'+ i +'" name="status_'+ i +'" class="form-select text-black"> </select> </div>' +
                    '<div class="col-2"> <select id="payment_'+ i +'" name="payment_'+ i +'" class="form-select text-black"> <option value="0">Наличные</option> <option value="1">Карта</option> <option value="2">Мобильная</option> <option value="3">От выбора справочника</option> </select> </div>' +
                    '<div class="col-2"> <select onchange="FU_saleschannelAutomation('+ saleschannel +', '+ entity +')" id="saleschannel_'+ i +'" name="saleschannel_'+ i +'" class="form-select text-black "> <option value="0"> Не выбирать</option> </select> </div>' +
                    '<div class="col-2"> <select onchange="FU_projectAutomation('+ project +', '+ entity +')" id="project_'+ i +'" name="project_'+ i +'" class="form-select text-black "> <option value="0"> Не выбирать</option> </select> </div>' +
                    '<div class="col-2 text-center"> <span onclick="deleteScript('+ i +')" class="fa-solid fa-rectangle-xmark" style="font-size: 30px; cursor: pointer"> </span> </div>' +
                    '</div>')
                gridChild[i] = true
                window.document.getElementById(entity).value = Saved[i].entity
                FU_statusAutomation( window.document.getElementById('status_'+i), window.document.getElementById('entity_'+i),  window.document.getElementById('project_'+i), window.document.getElementById('saleschannel_'+i) )
                window.document.getElementById(status).value = Saved[i].status
                window.document.getElementById(payment).value = Saved[i].payment
                window.document.getElementById(project).value = Saved[i].project
                window.document.getElementById(saleschannel).value = Saved[i].saleschannel

            }
        }
    }




    function FU_statusAutomation(selectElement, entityName, selectProject, selectSalesChannel) {
        function createOptions(data, targetElement) {
            data.forEach((item) => {
                let option1 = document.createElement("option")
                option1.text = item.name
                option1.value = item.id
                targetElement.appendChild(option1)
            });
        }

        let value = entityName.value
        let params = entityName.options[value].text

        while (selectElement.firstChild) { selectElement.removeChild(selectElement.firstChild);}
        while (selectProject.firstChild) { selectProject.removeChild(selectProject.firstChild) }
        while (selectSalesChannel.firstChild) { selectSalesChannel.removeChild(selectSalesChannel.firstChild) }

        switch (params) {
            case 'Заказ покупателя':
                createOptions(status_arr_meta.customerorder, selectElement)
                createOptions(project_arr_meta.customerorder, selectProject)
                createOptions(saleschannel_arr_meta.customerorder, selectSalesChannel)
                break;

            case 'Отгрузки':
                createOptions(status_arr_meta.demand, selectElement)
                createOptions(project_arr_meta.demand, selectProject)
                createOptions(saleschannel_arr_meta.demand, selectSalesChannel)
                break;

            case 'Возврат покупателя':
                createOptions(status_arr_meta.salesreturn, selectElement)
                createOptions(project_arr_meta.salesreturn, selectProject)
                createOptions(saleschannel_arr_meta.salesreturn, selectSalesChannel)
                break;

            default:
                break;
        }
    }
    function FU_saleschannelAutomation(selectElement, entityName){
        let value = entityName.value
        let params = entityName.options[value].text
        switch (params) {
            case 'Заказ покупателя':
                saleschannel_arr_meta.customerorder = (saleschannel_arr_meta.customerorder).filter((obj) => obj.id !== selectElement.value);
                break;

            case 'Отгрузки':
                saleschannel_arr_meta.demand = (saleschannel_arr_meta.demand).filter((obj) => obj.id !== selectElement.value);
                break;

            case 'Возврат покупателя':
                saleschannel_arr_meta.salesreturn = (saleschannel_arr_meta.salesreturn).filter((obj) => obj.id !== selectElement.value);
                break;

            default:
                break;
        }
    }
    function FU_projectAutomation(selectElement, entityName){
        let value = entityName.value
        let params = entityName.options[value].text
        switch (params) {
            case 'Заказ покупателя':
                project_arr_meta.customerorder = (project_arr_meta.customerorder).filter((obj) => obj.id !== selectElement.value);
                break;

            case 'Отгрузки':
                project_arr_meta.demand = (project_arr_meta.demand).filter((obj) => obj.id !== selectElement.value);
                break;

            case 'Возврат покупателя':
                project_arr_meta.salesreturn = (project_arr_meta.salesreturn).filter((obj) => obj.id !== selectElement.value);
                break;

            default:
                break;
        }
    }




    function deleteScript(id){
        window.document.getElementById('child_'+id).remove();
        gridChild[id] = false
    }



    function showAddingOff() {
        isMouseDown = true;
        addingOff.style.display = "none";
        addingOn.style.display = "block";
    }
    function showAddingOn() {
        isMouseDown = false;
        addingOff.style.display = "block";
        addingOn.style.display = "none";
    }

    document.addEventListener("mouseup", function() { if (isMouseDown) { showAddingOn(); } });
</script>
