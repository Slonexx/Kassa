
@extends('layout')

@section('content')

    <script>
        var Global_num_click = 0;
        var Global_Max_device = 0;
        var Global_device_1 = 0;
        var Global_device_2 = 0;

        function add_device(){
            for (var i = 0; i<2; i++) {
                Global_num_click++;
                if ( Global_device_1 === 0 ) {
                    Global_device_1 = 1;
                    document.getElementById("device_1").style.display = "block";
                    break;
                }
                if ( Global_device_2 === 0 ) {
                    Global_device_2 = 1;
                    document.getElementById("device_2").style.display = "block";
                    break;
                }
                if ( Global_num_click > 2 ){
                    Global_Max_device = 1;
                    document.getElementById("Max_device").style.display = "block";
                    break;
                }

            }

        }

        function delete_device_1(){
            if ( Global_device_1 === 1 ) {
                Global_device_1 = 0;
                document.getElementById("device_1").style.display = "none";

                var ZHM_1 = document.getElementById('ZHM_1');
                var PASSWORD_1 = document.getElementById('PASSWORD_1');
                ZHM_1.value = '';
                PASSWORD_1.value = '';

                if (Global_Max_device === 1) {
                    Global_Max_device = 0;
                    document.getElementById("Max_device").style.display = "none";
                }

            }
        }

        function delete_device_2(){
            if ( Global_device_2 === 1 ) {
                Global_device_2 = 0;
                document.getElementById("device_2").style.display = "none";

                var ZHM_2 = document.getElementById('ZHM_2');
                var PASSWORD_2 = document.getElementById('PASSWORD_2');
                ZHM_2.value = '';
                PASSWORD_2.value = '';

                if (Global_Max_device === 1) {
                    Global_Max_device = 0;
                    document.getElementById("Max_device").style.display = "none";
                }

            }
        }

    </script>

    <div class="content p-4 mt-2 bg-white text-Black rounded">

            <div class="row gradient rounded p-2">
                <div class="col-10">
                    <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                        <span class="text-white"> Данные для интеграции с ReKassa </span>
                    </div>
                </div>
                <div class="col-2">
                    <button onclick="add_device()" type="button" class=" btn transparent btn-outline-warning"> <i class="fa-solid fa-circle-plus"></i> Добавить </button>
                </div>
            </div>


        @isset($message)

            <div class="{{$message['alert']}}"> {{ $message['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        @endisset

        <div id="Max_device" class="alert alert-danger alert-dismissible fade show in text-center mt-2"
             role="alert" style="display: none"> Уже максимальное количество кассовых аппаратов </div>


        <form action="/Setting/Device/{{$accountId}}" method="post" class="mt-3">
        @csrf <!-- {{ csrf_field() }} -->
            <div id="device_1" style="display: none;" >
                <div class="row mb-2">
                    <div class="col-10">
                        <div class="mx-3"> <h5>Кассовый аппарат №1</h5></div>
                    </div>
                    <div class="col-2">
                        <button onclick="delete_device_1()" type="button" class="mt-1 btn btn-danger "> <i class="fa-solid fa-circle-plus"></i> удалить </button>
                    </div>
                </div>

                <div class="mb-3 row mx-4">
                    <div class="col-4">
                        1. <label class="mt-1"> Заводской номер (ЗНМ) </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="ZHM_1" id="ZHM_1" placeholder="Необходимо ввести значение"
                               class="form-control" maxlength="255" value="">
                    </div>
                    <div class="mt-2"></div>
                    <div class="col-4">
                        2. <label class="mt-1"> Пароль </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="PASSWORD_1" id="PASSWORD_1" placeholder="Необходимо ввести значение"
                               class="form-control" maxlength="255" value="">
                    </div>
                </div>
            </div>
            <div id="device_2" style="display: none;" >
                <div class="row mb-2">
                    <div class="col-10">
                        <div class="mx-3"> <h5>Кассовый аппарат №2</h5></div>
                    </div>
                    <div class="col-2">
                        <button onclick="delete_device_2()" type="button" class="mt-1 btn btn-danger "> <i class="fa-solid fa-circle-plus"></i> удалить </button>
                    </div>
                </div>

                <div class="mb-3 row mx-4">
                    <div class="col-4">
                        1. <label class="mt-1"> Заводской номер (ЗНМ) </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="ZHM_2" id="ZHM_2" placeholder="Необходимо ввести значение"
                               class="form-control" maxlength="255" value="">
                    </div>
                    <div class="mt-2"></div>
                    <div class="col-4">
                        2. <label class="mt-1"> Пароль </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="PASSWORD_2" id="PASSWORD_2" placeholder="Необходимо ввести значение"
                               class="form-control" maxlength="255" value="">
                    </div>
                </div>
            </div>

            <hr class="href_padding">



            <button class="btn btn-outline-dark textHover" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa-solid fa-arrow-down-to-arc"></i> Сохранить </button>


        </form>
    </div>


@endsection



