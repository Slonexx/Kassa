
@extends('popup.index')

@section('content')

    <script>

        //const url = 'http://rekassa/Popup/customerorder/show';
        import {forEach} from "lodash";

        const url = 'https://smartrekassa.kz/Popup/customerorder/show';


        window.addEventListener("message", function(event) {
            //var receivedMessage = {"name":"OpenPopup","messageId":10,"popupName":"fiscalizationPopup","popupParameters":{"object_Id":"75035b22-243a-11ed-0a80-07600015e5d3","accountId":"1dd5bd55-d141-11ec-0a80-055600047495"}}; /*event.data;*/
            var receivedMessage = event.data;
            window.document.getElementById("sum").innerHTML = '';
            window.document.getElementById("vat").innerHTML = "";

            for (var i = 0; i < 20; i++) {
                window.document.getElementById(i).style.display = "none";
                window.document.getElementById('productName_' + i).innerHTML = '';
                window.document.getElementById('productQuantity_' + i).innerHTML = '';
                window.document.getElementById('productPrice_' + i).innerHTML = '';
                window.document.getElementById('productVat_' + i).innerHTML = '';
                window.document.getElementById('productDiscount_' + i).innerHTML = '';
                window.document.getElementById('productFinal_' + i).innerHTML = '';
            }

            if (receivedMessage.name === 'OpenPopup') {
                let params = {
                    object_Id: receivedMessage.popupParameters.object_Id,
                    accountId: receivedMessage.popupParameters.accountId,
                };
                let final = url + formatParams(params);

                console.log(final);//dwawdawwa

                let xmlHttpRequest = new XMLHttpRequest();
                xmlHttpRequest.addEventListener("load", function () {

                    let json = JSON.parse(this.responseText);
                    let products = json.products;

                    logReceivedMessage(products);

                    for (var i = 0; i < products.length; i++) {
                        window.document.getElementById('productName_' + i).innerHTML = products[i].name;
                        window.document.getElementById('productQuantity_' + i).innerHTML = products[i].quantity;
                        window.document.getElementById('productPrice_' + i).innerHTML = products[i].price;
                        if (products[i].vat === 0)  window.document.getElementById('productVat_' + i).innerHTML = "без НДС";
                        else window.document.getElementById('productVat_' + i).innerHTML = products[i].vat + '%';
                        window.document.getElementById('productDiscount_' + i).innerHTML = products[i].discount + '%';
                        window.document.getElementById('productFinal_' + i).innerHTML = products[i].final;



                        window.document.getElementById(i).style.display = "block";
                    }



                    window.document.getElementById("numberOrder").innerHTML = json.name;
                    window.document.getElementById("cash").innerHTML = json.sum;
                    window.document.getElementById("sum").innerHTML = json.sum;
                    if (json.vat == null) window.document.getElementById("vat").innerHTML = "0";
                    else window.document.getElementById("vat").innerHTML = json.vat.vatSum;

                    if (json.attributes != null){
                        json.attributes.forEach(showandhide);
                    } else {
                        window.document.getElementById("ShowCheck").style.display = "none";
                        window.document.getElementById("getKKM").style.display = "block";
                    }

                });
                xmlHttpRequest.open("GET", final);
                xmlHttpRequest.send();
            }

             });

            function logReceivedMessage(msg) {
                var messageAsString = JSON.stringify(msg);
                console.log("→ Received" + " message: " + messageAsString);
            }

            function formatParams(params) {
                return "?" + Object
                    .keys(params)
                    .map(function (key) {
                        return key + "=" + encodeURIComponent(params[key])
                    })
                    .join("&")
            }

            function deleteBTNClick(Object){
                //Object.remove();
                document.getElementById(Object).remove();
            }

            function showandhide(item, index, arr){
                if (index === 'ticket_id'){
                    if (arr[index] != null ){

                    }
                }
            }

        function isNumberKeyCash(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode == 46){
                var inputValue = $("#cash").val();
                var count = (inputValue.match(/'.'/g) || []).length;
                if(count<1){
                    if (inputValue.indexOf('.') < 1){
                        return true;
                    }
                    return false;
                }else{
                    return false;
                }
            }
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
        function isNumberKeyCard(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode == 46){
                var inputValue = $("#card").val();
                var count = (inputValue.match(/'.'/g) || []).length;
                if(count<1){
                    if (inputValue.indexOf('.') < 1){
                        return true;
                    }
                    return false;
                }else{
                    return false;
                }
            }
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>


    <div class="main-container">
        <div class="row gradient rounded p-2">
            <div class="col-11">
                <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                    <span class="text-white"> re:Kassa </span>
                    <span class="mx-5 text-white">Заказ покупателя №</span>
                    <span id="numberOrder" class="text-white"></span>
                </div>
            </div>
            <div class="col-1 ">
                <button type="submit" onclick="" class="myButton btn "> <i class="fa-solid fa-arrow-rotate-right"></i> </button>
            </div>
        </div>
        <div class="content-container">
            <div class=" rounded bg-white">
                <div id="main" class="row p-3">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-1 text-success">№</div>
                            <div class="col-5 text-success">Наименование</div>
                            <div class="col-1 text-success">Кол-во</div>
                            <div class="col-1 text-success">Цена</div>
                            <div class="col-1 text-success">НДС</div>
                            <div class="col-1 text-success">Скидка</div>
                            <div class="col-1 text-success">Сумма</div>
                            <div class="col-1 text-success">Учитывать </div>
                            <hr class="mt-1 text-success" style="background-color: #0c7d70; height: 3px; border: 0;">
                        </div>
                    </div>
                    <div id="products" class="col-12 text-black">
                        @for( $i=0; $i<20; $i++)
                            <div id="{{ $i }}" class="row mt-2" style="display:block;">
                                <div class="row">
                                    <div class="col-1">{{ $i + 1 }}</div>
                                    <div id="{{ 'productName_'.$i }}"  class="col-5"></div>
                                    <div id="{{ 'productQuantity_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productPrice_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productVat_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productDiscount_'.$i }}"  class="col-1 text-center"></div>
                                    <div id="{{ 'productFinal_'.$i }}"  class="col-1 text-center"></div>
                                    <div class="col-1 ">
                                        <button onclick="deleteBTNClick( {{ $i }} )" class="btn btn-danger">Убрать</button>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <div class="col-12 mt-5">
                        <div class="row">
                          <div class="col-8"></div>
                          <div class="col-2">
                              <h4>Итого: </h4>
                              <h6>НДС: </h6>
                          </div>
                            <div class="col-2 float-right">
                                <h4 id="sum"></h4>
                                <h6 id="vat"></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons-container">
            <div class="row">
                <div class="col-2">
                    <div class="mx-2">
                        <input id="cash" type="number" step="0.1" placeholder="Сумма наличных"  onkeypress="return isNumberKeyCash(event)"
                               class="form-control float" required maxlength="255" value="">
                    </div>
                </div>
                <div class="col-2">
                    <input id="card" type="number" step="0.1"  placeholder="Сумма картой" onkeypress="return isNumberKeyCard(event)"
                           class="form-control float" required maxlength="255" value="">
                </div>
                <div class="col-2">

                </div>
                <div class="col-2">

                </div>
                <div class="col-2">
                    <button id="ShowCheck" class="mx-3 btn btn-success">Показать чек</button>
                </div>
                <div class="col-2">
                    <button id="getKKM" class="mx-3 btn btn-success">Отправить в ККМ</button>
                </div>
            </div>

        </div>
    </div>


@endsection

<style>

    body {
        overflow: hidden;
    }
    .main-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    .content-container {
        overflow-y: auto;
        overflow-x: hidden;
        flex-grow: 1;
    }
    .buttons-container {
        padding-top: 15px;
        min-height: 55px;
    }

    .myButton {
        box-shadow: 0px 4px 5px 0px #5d5d5d !important;
        background-image: radial-gradient( circle farthest-corner at 10% 20%,  rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90% ) !important;
        color: white !important;
        border-radius:50px !important;
        display:inline-block !important;
        cursor:pointer !important;
        padding:5px 5px !important;
        text-decoration:none !important;
    }
    .myButton:hover {
        filter: invert(1);

        color: #111111 !important;
    }
    .myButton:active {
        position: relative !important;
        top: 1px !important;
    }
</style>

<script>
   /* for (var i = 0; i < products.length; i++) {
        let divRow = document.createElement('div');
        divRow.setAttribute('id', products[i].position);
        divRow.setAttribute('class', 'row');
        document.getElementById('products').appendChild(divRow);

        let productNumber = document.createElement('div');
        productNumber.setAttribute('class', 'col-1');
        productNumber.innerText = i + 1;
        document.getElementById(products[i].position).appendChild(productNumber);

        let productName = document.createElement('div');
        productName.setAttribute('class', 'col-4 mt-1');
        productName.innerText = products[i].name;
        document.getElementById(products[i].position).appendChild(productName);

        let productQuantity = document.createElement('div');
        productQuantity.setAttribute('class', 'col-1');
        productQuantity.innerText = products[i].quantity;
        document.getElementById(products[i].position).appendChild(productQuantity);

        let productPrice = document.createElement('div');
        productPrice.setAttribute('class', 'col-1');
        productPrice.innerText = products[i].price;
        document.getElementById(products[i].position).appendChild(productPrice);

        let productVat = document.createElement('div');
        productVat.setAttribute('class', 'col-1');
        let procent = products[i].vat;
        if (procent === 0) productVat.innerText = 'без НДС';
        else productVat.innerText = products[i].vat + '%';
        document.getElementById(products[i].position).appendChild(productVat);

        let productDiscount = document.createElement('div');
        productDiscount.setAttribute('class', 'col-1');
        productDiscount.innerText = products[i].discount + '%';
        document.getElementById(products[i].position).appendChild(productDiscount);

        let productFinal = document.createElement('div');
        productFinal.setAttribute('class', 'col-1');
        productFinal.innerText = products[i].final;
        document.getElementById(products[i].position).appendChild(productFinal);

        let productCheck = document.createElement('dir');
        productCheck.setAttribute('class', 'col-2');
        productCheck.setAttribute('id', 'btn_'+products[i].position);
        document.getElementById(products[i].position).appendChild(productCheck);

        let DeleteCheck = document.createElement('button');
        DeleteCheck.setAttribute('class', ' btn btn-danger');
        DeleteCheck.setAttribute('onclick', 'deleteBTNClick('+ products[i].position.toString() +')');
        DeleteCheck.innerText = "Убрать";
        document.getElementById('btn_'+products[i].position).appendChild(DeleteCheck);

    }*/
</script>
