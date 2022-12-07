
@extends('widget.widget')

@section('content')

    <script>
        const hostWindow = window.parent;
        let Global_messageId = 0;
        let Global_accountId = "{{$accountId}}";
        let Global_object_Id;
        let entity_type = 'salesreturn';

        window.addEventListener("message", function(event) {
            const receivedMessage = event.data;
            workerAccess();
            if (receivedMessage.name === 'Open') {

                console.log('Global_object_Id = ' + receivedMessage.objectId );
                Global_object_Id = receivedMessage.objectId;

                var sendingMessage = {
                    name: "OpenFeedback",
                    correlationId: receivedMessage.messageId
                };
                logSendingMessage(sendingMessage);
                hostWindow.postMessage(sendingMessage, '*');

                const xmlHttpRequest = new XMLHttpRequest();
                xmlHttpRequest.addEventListener("load", function() {

                });
                xmlHttpRequest.open("GET", "");
                xmlHttpRequest.send();
            }

        });

        function fiscalization(){

            Global_messageId++;
            var sendingMessage = {
                name: "ShowPopupRequest",
                messageId: Global_messageId,
                popupName: "salesreturnPopup",
                popupParameters: {
                    object_Id:Global_object_Id,
                    accountId:Global_accountId,
                    entity_type:entity_type,
                },
            };
            logSendingMessage(sendingMessage);
            hostWindow.postMessage(sendingMessage, '*');
        }


        function logSendingMessage(msg) {
            var messageAsString = JSON.stringify(msg);
            console.log("← Sending" + " message: " + messageAsString);
        }

        function workerAccess(){
            let worker = {{$worker}};
            if (worker === 1){
                $('#workerAccess_yes').show();
                $('#workerAccess_no').hide();
            } else {
                $('#workerAccess_yes').hide();
                $('#workerAccess_no').show();
            }

        }

    </script>


    <div class="row gradient rounded p-2">
        <div class="col-10">
            <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                <span class="text-white"> re:Kassa </span>
            </div>
        </div>
    </div>
    <div id="workerAccess_yes" class="row mt-2 rounded bg-white" style="display:none;">
        <div class="col-1"></div>
        <button onclick="fiscalization()" class="col-10 btn btn-warning text-black rounded-pill"> Возврат </button>
    </div>
    <div id="workerAccess_no" class="row mt-2 rounded bg-white" style="display: none">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="text-center">
                <div class="p-3 mb-2 bg-danger text-white">
                   <span class="s-min-10">
                        У вас нет доступа к данному виджету, сообщите администратору, чтоб он вам предоставил доступ
                        <i class="fa-solid fa-ban "></i>
                    </span>
                </div>
            </div>
        </div>
    </div>


@endsection

<style>
    .s-min-10 {
        font-size: 12px;
    }
</style>

