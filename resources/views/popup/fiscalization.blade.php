
@extends('popup.index')

@section('content')

    <script>

        window.addEventListener("message", function(event) {
            var receivedMessage = event.data;
            logReceivedMessage(receivedMessage);

            if (receivedMessage.name === 'ShowPopupResponse' && receivedMessage.popupName === 'fiscalizationPopup') {
                logReceivedMessage(receivedMessage);

                var oReq = new XMLHttpRequest();
                oReq.addEventListener("load", function() {

                });
                oReq.open("GET", "");
                oReq.send();
            }

        });

        function logReceivedMessage(msg) {
            var messageAsString = JSON.stringify(msg);
            console.log("← Sending" + " message: " + messageAsString);
        }

        //Доделать потом обновление кнопка
    </script>



    <div class="row gradient rounded p-2">
        <div class="col-11">
            <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                <span class="text-white"> РеКасса 3.0 </span>
            </div>
        </div>
        <div class="col-1 ">
            <button type="submit" onclick="" class="myButton btn "> <i class="fa-solid fa-arrow-rotate-right"></i> </button>
        </div>
    </div>
    <div class=" rounded bg-white">
        <div class="row p-3">
            <div class="col-12">
                <div class="row">
                    <div class="col-1 text-success">№</div>
                    <div class="col-6 text-success">Наименование</div>
                    <div class="col-1 text-success">Кол-во</div>
                    <div class="col-1 text-success">Цена</div>
                    <div class="col-1 text-success">НДС</div>
                    <div class="col-1 text-success">Скидка</div>
                    <div class="col-1 text-success">Сумма</div>
                    <hr class="mt-1 text-success" style="background-color: #0c7d70; height: 3px; border: 0;">
                </div>


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

{{--
<div id="object">
    <form id="popup-form">
        <h2>Формирование чека</h2>
        <table class="ui-table">
            <thead>
            <tr>
                <th>Наименование</th>
                <th>Кол-во</th>
                <th>Цена</th>
                <th>НДС</th>
                <th>Скидка</th>
                <th>Сумма</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Зелье единорога</td>
                <td>1</td>
                <td>1300</td>
                <td>без НДС</td>
                <td>0</td>
                <td>1300</td>
            </tr>
            <tr>
                <td>Бумажная-тонкая веревка</td>
                <td>1</td>
                <td>220</td>
                <td>без НДС</td>
                <td>0</td>
                <td>220</td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td style="font-size: 200%;">1520</td>
            </tr>
            </tbody>
            <tbody>

            </tbody>
        </table>
        <div style="display: inline-block; width: 200px;vertical-align: top;">
            Тип оплаты:
            <br>
            <label>
                <input type="radio" name="pay-type" value="cash" checked="">
                Наличными</label>
            <label>
                <input type="radio" name="pay-type" value="card">
                Безналичными</label>
            <br>
        </div>
        <div style="display: inline-block; width: 200px;vertical-align: top;">
            Тип чека:
            <br>
            <select name="check-type">
                <option value="4" selected="">Полный расчет</option>
                <option value="-1">Полный возврат</option>
            </select>
        </div>
        <div style="display: inline-block; width: 200px;vertical-align: top;">
            <button class="button button--success" onclick="RegisterCheck();return false;" id="button-register-check">
                Напечатать чек</button>
        </div>
        <div style="display: inline-block; width: 300px;vertical-align: top;">
            <button class="button" onclick="ExecuteKkm({Command: 'OpenShift'});return false;">Открыть смену</button>
            <button class="button" onclick="ExecuteKkm({Command: 'CloseShift'});return false;">Закрыть смену</button>
            <button class="button" onclick="ExecuteKkm({Command: 'XReport'});return false;">X отчет</button>
        </div>
        <script>
            var ObjectData = atob("eyJDb21tYW5kIjoiUmVnaXN0ZXJDaGVjayIsIklzRmlzY2FsQ2hlY2siOnRydWUsIlR5cGVDaGVjayI6MCwiQ2hlY2tTdHJpbmdzIjpbeyJSZWdpc3RlciI6eyJOYW1lIjoiXHUwNDE3XHUwNDM1XHUwNDNiXHUwNDRjXHUwNDM1IFx1MDQzNVx1MDQzNFx1MDQzOFx1MDQzZFx1MDQzZVx1MDQ0MFx1MDQzZVx1MDQzM1x1MDQzMCIsIlF1YW50aXR5IjoxLCJQcmljZSI6MTMwMCwiQW1vdW50IjoxMzAwLCJUYXgiOi0xLCJTaWduTWV0aG9kQ2FsY3VsYXRpb24iOjQsIlNpZ25DYWxjdWxhdGlvbk9iamVjdCI6MX19LHsiUmVnaXN0ZXIiOnsiTmFtZSI6Ilx1MDQxMVx1MDQ0M1x1MDQzY1x1MDQzMFx1MDQzNlx1MDQzZFx1MDQzMFx1MDQ0Zi1cdTA0NDJcdTA0M2VcdTA0M2RcdTA0M2FcdTA0MzBcdTA0NGYgXHUwNDMyXHUwNDM1XHUwNDQwXHUwNDM1XHUwNDMyXHUwNDNhXHUwNDMwIiwiUXVhbnRpdHkiOjEsIlByaWNlIjoyMjAsIkFtb3VudCI6MjIwLCJUYXgiOi0xLCJTaWduTWV0aG9kQ2FsY3VsYXRpb24iOjQsIlNpZ25DYWxjdWxhdGlvbk9iamVjdCI6MX19XSwiTVNfc3VtIjoxNTIwfQ==");</script>
        <script>
            var Data = {};

            function finishExecution(Result) {
                let message = "";
                const serviceCommandsArray = ["OpenShift", "CloseShift", "XReport"];
                if (Result.Command == "RegisterCheck" && typeof(Result.CheckNumber) == "number" && Result.Error === "") {
                    message = "Чек успешно напечатан! Смена " + Result.SessionNumber + ", чек " + Result.CheckNumber + ".";
                    message += "<br><a href='" + Result.URL + "'>Ссылка на чек на сайте вашего ОФД</a>."
                } else if (serviceCommandsArray.includes(Result.Command) && Result.Error === "") {
                    switch (Result.Command) {
                        case "OpenShift":
                            message += "Смена открыта. Номер смены: " + Result.SessionNumber + ".";
                            break;
                        case "CloseShift":
                            message += "Смена закрыта. Номер смены: " + Result.SessionNumber + ".";
                            break;
                        case "XReport":
                            message += "X отчет напечатан.";
                            break;
                    }
                } else {
                    if (Result.Error === "Unknown error.") {
                        message = "Произошла ошибка! <br>ККТ не подключена, не настроено расширение KkmServer.<br>Обратитесь к руководству пользователя для настройки.";
                    } else {
                        message = "Произошла ошибка! <br>Текст ошибки: " + Result.Error;
                    }
                }
                document.getElementById("result").innerHTML = message;
            }

            window.addEventListener("message", function(event) {
                var receivedMessage = event.data;
                if (receivedMessage.name === 'KkmExecutionResult') {
                    finishExecution(receivedMessage.Result);
                }
            });

            function ExecuteKkm(DataParametr) {
                window.Data = DataParametr;
                window.open('../kassa/KkmExecute.php','_blank');
            }
            function RegisterCheck() {
                // block button
                $("#button-register-check").prop('disabled', true).removeClass("button--success");
                // unpack ObjectData if necessary
                if (typeof (ObjectData) == "string") {
                    ObjectData = JSON.parse(ObjectData);
                }
                // apply pay type
                let payType = $("[name=pay-type]:checked").val();
                if (payType == 'cash') {
                    ObjectData.Cash = ObjectData.MS_sum;
                } else {
                    ObjectData.ElectronicPayment = ObjectData.MS_sum;
                }

                // apply check type
                let checkType = $("[name=check-type]").val();
                if (checkType == '-1') {
                    ObjectData.TypeCheck = 1;
                } else {
                    ObjectData.TypeCheck = 0;
                }

                // console.log(ObjectData);

                // send data
                ExecuteKkm(window.ObjectData);
            }

            $(".buttons-container").hide();
        </script>
        <br>
        <div id="result" style="font-size: 200%;">

        </div>
        <div class="buttons-container" style="display: none;">
            <button class="button button--success" onclick="ClosePopup(true);return false;">Отправить</button>
            <button class="button" onclick="ClosePopup();return false;">Отмена</button>
        </div>
    </form>
</div>--}}
