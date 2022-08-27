
@extends('popup.index')

@section('content')

    <script>

        //const url = 'http://rekassa/Popup/customerorder/show';
        const url = 'https://smartrekassa.kz/Popup/customerorder/show';



        window.addEventListener("message", function(event) {
            //var receivedMessage = {"name":"OpenPopup","messageId":10,"popupName":"fiscalizationPopup","popupParameters":{"object_Id":"75035b22-243a-11ed-0a80-07600015e5d3","accountId":"1dd5bd55-d141-11ec-0a80-055600047495"}}; /*event.data;*/
            var receivedMessage = event.data;
           /* document.getElementById("products").remove();

            let child = document.createElement('div');
            child.setAttribute('id', 'products');
            child.setAttribute('class', 'col-12 text-black');
            document.getElementById('main').appendChild(child);*/

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



    </script>



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
    <div class=" rounded bg-white">
        <div id="main" class="row p-3">
            <div class="col-12">
                <div class="row">
                    <div class="col-1 text-success">№</div>
                    <div class="col-4 text-success">Наименование</div>
                    <div class="col-1 text-success">Кол-во</div>
                    <div class="col-1 text-success">Цена</div>
                    <div class="col-1 text-success">НДС</div>
                    <div class="col-1 text-success">Скидка</div>
                    <div class="col-1 text-success">Сумма</div>
                    <div class="col-2 text-success">Фискализировать </div>
                    <hr class="mt-1 text-success" style="background-color: #0c7d70; height: 3px; border: 0;">
                </div>
            </div>
            <div id="products" class="col-12 text-black">
                @for( $i=0; $i<20; $i++)
                    <div id="{{ $i }}" class="row mt-2" style="display:block;">
                        <div class="row">
                            <div class="col-1">{{ $i + 1 }}</div>
                            <div id="{{ 'productName_'.$i }}"  class="col-4"></div>
                            <div id="{{ 'productQuantity_'.$i }}"  class="col-1"></div>
                            <div id="{{ 'productPrice_'.$i }}"  class="col-1"></div>
                            <div id="{{ 'productVat_'.$i }}"  class="col-1 text-center"></div>
                            <div id="{{ 'productDiscount_'.$i }}"  class="col-1 text-center"></div>
                            <div id="{{ 'productFinal_'.$i }}"  class="col-1 text-center"></div>
                            <div class="col-2 ">
                                <button onclick="deleteBTNClick( {{ $i }} )" class="btn btn-danger">Убрать</button>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>






@endsection

<style>
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
