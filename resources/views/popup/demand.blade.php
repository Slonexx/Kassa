
@extends('popup.index')

@section('content')

    <script>


        //const url = 'https://rekassa/Popup/demand/show'
        const url = 'https://smartrekassa.kz/Popup/demand/show'

        let object_Id = ''
        let accountId = ''
        let entity_type = ''
        let id_ticket = ''

        /* let receivedMessage = {
             "name":"OpenPopup","messageId":1,"popupName":"fiscalizationPopup","popupParameters":
                 {"object_Id":"4037cab5-7496-11ed-0a80-0fcb003d9e39","entity_type":"demand","accountId":"1dd5bd55-d141-11ec-0a80-055600047495"}
         };*/


        window.addEventListener("message", function(event) {
            openDown()
            newPopup()
            let receivedMessage = event.data
            if (receivedMessage.name === 'OpenPopup') {

                object_Id = receivedMessage.popupParameters.object_Id;
                accountId = receivedMessage.popupParameters.accountId;
                entity_type = receivedMessage.popupParameters.entity_type;
                let data = { object_Id: object_Id, accountId: accountId, };

                let settings = ajax_settings(url, "GET", data);
                console.log(url + ' settings ↓ ')
                console.log(settings)

                $.ajax(settings).done(function (json) {
                    console.log(url + ' response ↓ ')
                    console.log(json)
                    $('#lDown').modal('hide')

                    id_ticket = json.attributes.ticket_id
                    window.document.getElementById("numberOrder").innerHTML = json.name;
                    payment_type = json.application.payment_type - 1
                    console.log('payment_type = ' + payment_type)
                    let products = json.products;

                    for (let i = 0; i < products.length; i++) {
                        if (products[i].propety === true) {

                            if ( products[i].propety_code == false ){
                                window.document.getElementById("messageAlert").innerText = "Позиции, у которых не системные единицы измерения не могут быть добавлены";
                                window.document.getElementById("message").style.display = "block";
                            } else {
                                window.document.getElementById('productId_' + i).innerHTML = products[i].position;
                                window.document.getElementById('productName_' + i).innerHTML = products[i].name;
                                window.document.getElementById('productQuantity_' + i).innerHTML = products[i].quantity;
                                if (products[i].trackingCodes == true) {
                                    window.document.getElementById('trackingCodes_minus_' + i).innerText = ''
                                    window.document.getElementById('trackingCodes_plus_' + i).innerText = ''
                                }
                                window.document.getElementById('productUOM_' + i).innerHTML = products[i].uom['name']
                                window.document.getElementById('productIDUOM_' + i).innerHTML = products[i].uom['id'];
                                window.document.getElementById('productPrice_' + i).innerHTML = products[i].price;
                                if (products[i].vat === 0)  window.document.getElementById('productVat_' + i).innerHTML = "без НДС";
                                else window.document.getElementById('productVat_' + i).innerHTML = products[i].vat + '%';
                                window.document.getElementById('productDiscount_' + i).innerHTML = products[i].discount + '%';
                                window.document.getElementById('productFinal_' + i).innerHTML = products[i].final;

                                let sum = window.document.getElementById("sum").innerHTML;
                                if (!sum) sum = 0;
                                window.document.getElementById("sum").innerHTML = roundToTwo(parseFloat(sum) + parseFloat(products[i].final));
                                window.document.getElementById(i).style.display = "block";
                            }

                        } else {
                            window.document.getElementById("messageAlert").innerText = "Позиции у которых нет ед. изм. не добавились ";
                            window.document.getElementById("message").style.display = "block";
                        }
                    }
                    window.document.getElementById('cash').value = window.document.getElementById("sum").innerHTML
                    if (json.attributes.ticket_id != null){
                        window.document.getElementById("ShowCheck").style.display = "block";
                        window.document.getElementById("refundCheck").style.display = "block";
                    } else {
                        window.document.getElementById("getKKM").style.display = "block";
                    }

                    window.document.getElementById("closeButtonId").style.display = "block";
                })

            }
        });

        function sendKKM(pay_type){
            let url = 'https://smartrekassa.kz/Popup/demand/send';

            let button_hide = ''
            if (pay_type === 'return') button_hide = 'refundCheck'
            if (pay_type === 'sell') button_hide = 'getKKM'

            window.document.getElementById(button_hide).style.display = "none";
            let modalShowHide = 'show';

            let total = window.document.getElementById('sum').innerText
            let money_card = window.document.getElementById('card').value;
            let money_cash = window.document.getElementById('cash').value;
            let money_mobile = window.document.getElementById('mobile').value;
            let SelectorInfo = document.getElementById('valueSelector');
            let option = SelectorInfo.options[SelectorInfo.selectedIndex];

            let error_what = option_value_error_fu(option.value, money_cash, money_card, money_mobile)
            if (error_what === true){
                modalShowHide = 'hide'
            }


            if (total-0.01 <= money_card+money_cash+money_mobile) {
                if (modalShowHide === 'show'){

                    $('#downL').modal('toggle')
                    let products = []
                    for (let i = 0; i < 99; i++) {
                        if ( window.document.getElementById(i).style.display === 'block' ) {
                            products[i] = {
                                id:window.document.getElementById('productId_'+i).innerText,
                                name:window.document.getElementById('productName_'+i).innerText,
                                quantity:window.document.getElementById('productQuantity_'+i).innerText,
                                UOM:window.document.getElementById('productIDUOM_'+i).innerText,
                                price:window.document.getElementById('productPrice_'+i).innerText,
                                is_nds:window.document.getElementById('productVat_'+i).innerText,
                                discount:window.document.getElementById('productDiscount_'+i).innerText
                            }
                        }
                    }

                    let data =  {
                        "accountId": accountId,
                        "object_Id": object_Id,
                        "entity_type": entity_type,

                        "money_card": money_card,
                        "money_cash": money_cash,
                        "money_mobile": money_mobile,

                        "pay_type": pay_type,
                        "total": total,

                        "position": JSON.stringify(products),
                    }
                    console.log(url + ' data ↓ ')
                    console.log(data)

                    $.ajax({
                        url: url,
                        method: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response){
                            $('#downL').modal('hide')
                            console.log(url + ' response ↓ ')
                            console.log(response)
                            if (response.code === 200){
                                window.document.getElementById("messageGoodAlert").innerText = "Чек создан";
                                window.document.getElementById("messageGood").style.display = "block";
                                window.document.getElementById("ShowCheck").style.display = "block";
                                window.document.getElementById("closeShift").style.display = "block";
                                modalShowHide = 'hide';
                                id_ticket = response.res.response.id;
                            } else {
                                if (response.res.hasOwnProperty('error')) {
                                    if (response.res.error.code === 'CASH_REGISTER_SHIFT_PERIOD_EXPIRED') {
                                        window.document.getElementById('messageAlert').innerText = "Предыдущая смена не закрыта !";
                                        window.document.getElementById('message').style.display = "block";
                                        window.document.getElementById(button_hide).style.display = "block";
                                        modalShowHide = 'hide';
                                    } else  {
                                        window.document.getElementById('messageAlert').innerText = JSON.stringify(response.res.error);
                                        window.document.getElementById('message').style.display = "block";
                                        window.document.getElementById(button_hide).style.display = "block";
                                        modalShowHide = 'hide';
                                    }
                                } else {
                                    window.document.getElementById('messageAlert').innerText = "Ошибка 400";
                                    window.document.getElementById('message').style.display = "block";
                                    window.document.getElementById(button_hide).style.display = "block";
                                    modalShowHide = 'hide';
                                }
                            }
                        }
                    });
                    modalShowHide = 'hide';
                }
                else window.document.getElementById(button_hide).style.display = "block";
            } else {
                window.document.getElementById('messageAlert').innerText = 'Сумма некорректна, введите больше';
                window.document.getElementById('message').style.display = "block";
                window.document.getElementById(button_hide).style.display = "block";
                modalShowHide = 'hide'
            }
        }

        function ShowCheck(){
            let urlrekassa = 'https://app.rekassa.kz/'
            //let url = 'http://rekassa/Popup/customerorder/closeShift';
            let url = 'https://smartrekassa.kz/api/ticket';
            let data = {
                accountId: accountId,
                id_ticket: id_ticket,
            };

            let settings = ajax_settings(url, "GET", data);
            console.log(url + ' settings ↓ ')
            console.log(settings)

            $.ajax(settings).done(function (response) {
                console.log(url + ' response ↓ ')
                console.log(response)
                window.open(urlrekassa + response);
            })
        }

        function SelectorSum(Selector){
            window.document.getElementById("cash").value = ''
            window.document.getElementById("card").value = ''
            window.document.getElementById("mobile").value = ''
            let option = Selector.options[Selector.selectedIndex];
            if (option.value === "0") {
                document.getElementById('Visibility_Cash').style.display = 'block';
                document.getElementById('Visibility_Card').style.display = 'none';
                document.getElementById('Visibility_Mobile').style.display = 'none';
            }
            if (option.value === "1") {
                document.getElementById('Visibility_Card').style.display = 'block';
                document.getElementById('Visibility_Cash').style.display = 'none';
                document.getElementById('Visibility_Mobile').style.display = 'none';
                let card =  window.document.getElementById("card");
                card.value = window.document.getElementById("sum").innerText
                window.document.getElementById("card").disabled = true
            }
            if (option.value === "2") {
                document.getElementById('Visibility_Cash').style.display = 'none';
                document.getElementById('Visibility_Card').style.display = 'none';
                document.getElementById('Visibility_Mobile').style.display = 'block';
                let mobile =  window.document.getElementById("mobile");
                mobile.value = window.document.getElementById("sum").innerText
                window.document.getElementById("mobile").disabled = true
            }
            if (option.value === "3") {
                document.getElementById('Visibility_Cash').style.display = 'block';
                document.getElementById('Visibility_Card').style.display = 'block';
                document.getElementById('Visibility_Mobile').style.display = 'block';
                window.document.getElementById("card").disabled = false
                window.document.getElementById("mobile").disabled = false
            }

        }

        function updateQuantity(id, params){
            let object_Quantity = window.document.getElementById('productQuantity_'+id);
            let Quantity = parseInt(object_Quantity.innerText)

            if (Quantity >= 0 ){

                let object_price = window.document.getElementById('productPrice_'+id).innerText;
                let object_Final = window.document.getElementById('productFinal_'+id);

                let object_sum = window.document.getElementById('sum');
                let sum = parseFloat(object_sum.innerText - object_Final.innerText)

                if (params === 'plus'){
                    object_Quantity.innerText = Quantity + 1
                    object_Final.innerText = object_Quantity.innerText * object_price
                    object_sum.innerText = parseFloat(sum + parseFloat(object_Final.innerText))
                }
                if (params === 'minus'){
                    object_Quantity.innerText = Quantity - 1
                    object_Final.innerText = object_Quantity.innerText * object_price
                    object_sum.innerText = parseFloat(sum + parseFloat(object_Final.innerText))
                    if (parseInt(object_Quantity.innerText) === 0){
                        deleteBTNClick( id )
                    }
                }
            } else deleteBTNClick( id )





        }
    </script>


    <div class="main-container">
        <div class="row gradient rounded p-2">
            <div class="col-9">
                <div class="mx-2"> <img src="https://static.tildacdn.pro/tild6231-6435-4138-a431-346337363461/Layer_1.svg" width="45" height="45"  alt="">
                    <span class="text-white"> reKassa </span>
                    <span class="mx-5 text-white">Отгрузка №</span>
                    <span id="numberOrder" class="text-white"></span>
                </div>
            </div>
            <div class="col-3">
                <div class="row"> <div class="col-6"></div>
                    <div class="col-6">
                        <button id="closeButtonId" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal" >Закрыть смену</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="message" class="mt-2 row" style="display:none;" >
            <div class="col-12">
                <div id="messageAlert" class=" mx-3 p-2 alert alert-danger text-center ">
                </div>
            </div>
        </div>
        <div id="messageGood" class="mt-2 row" style="display:none;" >
            <div class="col-12">
                <div id="messageGoodAlert" class=" mx-3 p-2 alert alert-success text-center ">
                </div>
            </div>
        </div>
        <div class="content-container">
            <div class=" rounded bg-white">
                <div id="main" class="row p-3">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-1 text-black">№</div>
                            <div class="col-4 text-black">Наименование</div>
                            <div class="col-1 text-black">Кол-во</div>
                            <div class="col-1 text-black">Ед. Изм.</div>
                            <div class="col-1 text-black">Цена</div>
                            <div class="col-1 text-black">НДС</div>
                            <div class="col-1 text-black">Скидка</div>
                            <div class="col-1 text-black">Сумма</div>
                            <div class="col-1 text-black">Учитывать </div>
                            <div class="buttons-container-head mt-1"></div>
                        </div>
                    </div>
                    <div id="products" class="col-12 text-black">
                        @for( $i=0; $i<99; $i++)
                            <div id="{{ $i }}" class="mt-2" style="display:block;">
                                <div class="row">
                                    <div class="col-1">{{ $i + 1 }}</div> <div id="{{'productId_'.$i}}" style="display:none;"></div>
                                    <div id="{{ 'productName_'.$i }}"  class="col-4" ></div>
                                    <div class="col-1 text-center row">
                                        <div id="{{ 'trackingCodes_minus_'.$i }}" class="col-4"><i onclick="updateQuantity( '{{ $i }}', 'minus')" class="fa-solid fa-circle-minus text-danger" style="cursor: pointer"></i></div>
                                        <div id="{{ 'productQuantity_'.$i }}" class="col-4"></div>
                                        <div id="{{ 'trackingCodes_plus_'.$i }}" class="col-4"><i onclick="updateQuantity( '{{ $i }}', 'plus')" class="fa-solid fa-circle-plus text-success" style="cursor: pointer"></i></div>
                                    </div>
                                    <div id="{{ 'productUOM_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productIDUOM_'.$i }}"  class="col-1 text-center" style="display: none"></div>
                                    <div id="{{ 'productPrice_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productVat_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productDiscount_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productFinal_'.$i }}"  class="col-1 text-center"></div>
                                    <div class="col-1 d-flex justify-content-end">
                                        <button onclick="deleteBTNClick( {{ $i }} )" class="btn btn-danger">Убрать</button>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons-container-head"></div>
        <div class="buttons-container">
            <div class="row">

                <div class="col-7 row">
                    <div class="row">
                        <div class="col-12 mx-2 ">
                            <div class="col-5 bg-success text-white p-1 rounded">
                                <span> Итого: </span>
                                <span id="sum"></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-1">

                </div>
                <div class="col-2">

                </div>
                <div class="col-2 d-flex justify-content-end">
                    <button onclick="sendKKM('return')" id="refundCheck" class="btn btn-danger">возврат</button>
                    <button onclick="sendKKM('sell')" id="getKKM" class="btn btn-success">Отправить в ККМ</button>
                </div>

                <div class="row mt-2">
                    <div class="col-3">
                        <div class="row">
                            <div class="col-5">
                                <div class="mx-1 mt-1 bg-warning p-1 rounded text-center">Тип оплаты</div>
                            </div>
                            <div class="col-7">
                                <select onchange="SelectorSum(valueSelector)" id="valueSelector" class="form-select">
                                    <option selected value="0">Наличными</option>
                                    <option value="1">Картой</option>
                                    <option value="2">Мобильная</option>
                                    <option value="3">Смешанная</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4"> <div id="Visibility_Cash" class="mx-2" style="display: none">
                                    <input id="cash" type="number" step="0.1" placeholder="Сумма наличных"  onkeypress="return isNumberKeyCash(event)"
                                           class="form-control float" required maxlength="255" value="">
                                </div> </div>
                            <div class="col-4"> <div id="Visibility_Card" class="mx-2" style="display: none">
                                    <input id="card" type="number" step="0.1"  placeholder="Сумма картой" onkeypress="return isNumberKeyCard(event)"
                                           class="form-control float" required maxlength="255" value="">
                                </div> </div>
                            <div class="col-4"> <div id="Visibility_Mobile" class="mx-2" style="display: none">
                                    <input id="mobile" type="number" step="0.1"  placeholder="Сумма мобильных" onkeypress="return isNumberKeyMobile(event)"
                                           class="form-control float" required maxlength="255" value="">
                                </div> </div>
                        </div>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-2 d-flex justify-content-end">
                        <button onclick="ShowCheck()" id="ShowCheck" class="btn btn-success">Показать чек</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Закрытие смены
                        <i class="fa-solid fa-circle-question text-danger"></i>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <label> Введите пин код для закрытия смены</label>
                            <input id="pin_code" type="number" placeholder="PIN code"
                                   class="form-control float" required maxlength="10" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button  onclick="closeShift()" id="closeShift"
                             data-bs-dismiss="modal" class="btn btn-danger">Закрыть смену</button>
                </div>
            </div>
        </div>
    </div>
    <div id="downL" class="modal fade bd-example-modal-sm" data-bs-keyboard="false" data-bs-backdrop="static"
         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <i class="fa-solid fa-circle-exclamation text-danger"></i>
                        Отправка
                    </h5>
                </div>
                <div class="modal-body text-center" style="background-color: #e5eff1">
                    <div class="row">
                        <img style="width: 100%" src="https://smartrekassa.kz/Config/download.gif" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="lDown" class="modal fade bd-example-modal-sm" data-bs-keyboard="false" data-bs-backdrop="static"
         tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <i class="fa-solid fa-circle-exclamation text-danger"></i>
                        Загрузка
                    </h5>
                </div>
                <div class="modal-body text-center" style="background-color: #e5eff1">
                    <div class="row">
                        <img style="width: 100%" src="https://smartrekassa.kz/Config/download.gif" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<script>

    function ajax_settings(url, method, data){
        return {
            "url": url,
            "method": method,
            "timeout": 0,
            "headers": {"Content-Type": "application/json",},
            "data": data,
        }
    }

    function newPopup(){
        window.document.getElementById("sum").innerHTML = ''

        window.document.getElementById("message").style.display = "none"
        window.document.getElementById("messageGood").style.display = "none"
        window.document.getElementById("closeButtonId").style.display = "none"

        window.document.getElementById("refundCheck").style.display = "none"
        window.document.getElementById("getKKM").style.display = "none"
        window.document.getElementById("ShowCheck").style.display = "none"

        window.document.getElementById("cash").value = ''
        window.document.getElementById("card").value = ''
        window.document.getElementById("mobile").value = ''

        window.document.getElementById("cash").style.display = "block"
        let thisSelectorSum = window.document.getElementById("valueSelector")
        thisSelectorSum.value = 0;
        SelectorSum(thisSelectorSum)

        for (let i = 0; i < 99; i++) {
            window.document.getElementById(i).style.display = "none"
            window.document.getElementById('productName_' + i).innerHTML = ''
            window.document.getElementById('productQuantity_' + i).innerHTML = ''
            window.document.getElementById('productPrice_' + i).innerHTML = ''
            window.document.getElementById('productVat_' + i).innerHTML = ''
            window.document.getElementById('productDiscount_' + i).innerHTML = ''
            window.document.getElementById('productFinal_' + i).innerHTML = ''
        }
    }

    function openDown(){
        $('#lDown').modal('show');
    }
    function closeDown(){
        $('#lDown').modal('hide');
        $('#downL').modal('hide');
    }

    function formatParams(params) {
        return "?" + Object
            .keys(params)
            .map(function (key) {
                return key + "=" + encodeURIComponent(params[key])
            })
            .join("&")
    }

    function option_value_error_fu(index_option, money_card, money_cash, money_mobile){
        console.log(index_option)
        let params = false
        switch (index_option) {
            case 0 && "0": {
                if (!money_cash) {
                    window.document.getElementById('messageAlert').innerText = 'Вы не ввели сумму наличных'
                    window.document.getElementById('message').style.display = "block"
                    params = true
                }
                break
            }
            case 1 && "1": {
                if (!money_cash) {
                    window.document.getElementById('messageAlert').innerText = 'Вы не ввели сумму карты'
                    window.document.getElementById('message').style.display = "block"
                    params = true
                }
                break
            }
            case 2 && "2": {
                if (!money_mobile){
                    window.document.getElementById('messageAlert').innerText = 'Вы не ввели сумму мобильных'
                    window.document.getElementById('message').style.display = "block"
                    params = true
                }
                break
            }
            case 3 && "3": {
                if (!money_card && !money_cash && !money_mobile){
                    window.document.getElementById('messageAlert').innerText = 'Вы не ввели сумму'
                    window.document.getElementById('message').style.display = "block"
                    params = true
                }
                break
            }
            default: {

            }

        }
        return params
    }

    function roundToTwo(num) { return +(Math.round(num + "e+2")  + "e-2"); }

    function isNumberKeyCash(evt){
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode == 46){
            let inputValue = $("#cash").val();
            let count = (inputValue.match(/'.'/g) || []).length;
            if(count<1){
                return inputValue.indexOf('.') < 1;

            }else{
                return false;
            }
        }
        return !(charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57));

    }
    function isNumberKeyCard(evt){
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode == 46){
            let inputValue = $("#card").val();
            let count = (inputValue.match(/'.'/g) || []).length;
            if(count<1){
                return inputValue.indexOf('.') < 1;

            }else{
                return false;
            }
        }
        return !(charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57));

    }
    function isNumberKeyMobile(evt){
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode == 46){
            let inputValue = $("#mobile").val();
            let count = (inputValue.match(/'.'/g) || []).length;
            if(count<1){
                return inputValue.indexOf('.') < 1;

            }else{
                return false;
            }
        }
        return !(charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57));

    }

    function deleteBTNClick(Object){
        let sum = document.getElementById("sum").innerHTML;
        let final = document.getElementById('productFinal_' + Object).innerHTML;
        window.document.getElementById("sum").innerHTML = sum-final;

        window.document.getElementById('productName_' + Object).innerHTML = '';
        window.document.getElementById('productQuantity_' + Object).innerHTML = '';
        window.document.getElementById('productPrice_' + Object).innerHTML = '';
        window.document.getElementById('productVat_' + Object).innerHTML = '';
        window.document.getElementById('productDiscount_' + Object).innerHTML = '';
        window.document.getElementById('productFinal_' + Object).innerHTML = '';
        window.document.getElementById(Object).style.display = "none";
    }

    function closeShift(){

        let pinCode = window.document.getElementById('pin_code').value;

        let params = {
            accountId: accountId,
            pincode: pinCode,
        };
        let url = 'https://smartrekassa.kz/Popup/customerorder/closeShift';
        let settings = ajax_settings(url, "GET", params);
        console.log(url + ' settings ↓ ')
        console.log(settings)

        $.ajax(settings).done(function (json) {
            console.log(url + ' response ↓ ')
            console.log(json)

            if (json.statusCode === 200){
                window.document.getElementById('messageAlert').innerText = json.message;
                window.document.getElementById('message').style.display = "block";
            } else {
                console.log(' Error = ' + url + ' message = ' + JSON.stringify(json.message))
                window.document.getElementById('messageAlert').innerText = "ошибка";
                window.document.getElementById('message').style.display = "block";
            }
        })
    }
</script>
