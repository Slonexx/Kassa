@extends('layout')
@section('item', 'link_5')
@section('content')
    <div class="main-container">
        <div class="content-container">
            <div class="content p-4 mt-2 bg-white text-Black rounded">
                @include('div.TopServicePartner')
                <script> NAME_HEADER_TOP_SERVICE("Настройки → Автоматизация") </script>
                @if($message == true) <div class="{{$class}}"> {{$message}}</div> @endif

                <div class="mt-3 alert alert-warning alert-dismissible fade show in text-center" style="font-size: 16px">
                    Данный раздел предлагает автоматизировать фискализацию путем создания собственного сценария для смены статуса в документах.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <form action="/Setting/Automation/{{ $accountId }}?isAdmin={{ $isAdmin }}" method="post">
                    @csrf <!-- {{ csrf_field() }} -->
                    <div class="mt-2 row p-1 gradient_invert rounded text-black">
                        <div class="col-11">
                            <div style="font-size: 20px"> Создать сценарий </div>
                        </div>
                        <div onclick="createScript()" onmousedown="showAddingOff()" onmouseup="showAddingOn()" class="col-1 d-flex justify-content-end " style="font-size: 30px; cursor: pointer">
                            <i id="adding_off" class="fa-regular fa-square-plus"></i>
                            <i id="adding_on" class="fa-solid fa-square-plus" style="display: none"></i>
                        </div>
                    </div>

                    <div class="mt-2 row gradient p-1 rounded text-white">
                        <div class="col-2">
                           Тип документа
                        </div>
                        <div class="col-2 text-center">
                            Статус
                        </div>
                        <div class="col-2 text-center">
                            Тип оплаты
                        </div>
                        <div class="col-2 text-center">
                            Канал продаж
                        </div>
                        <div class="col-2 text-center">
                            Проект
                        </div>
                        <div class="col-2 text-center">
                            Удалить
                        </div>
                    </div>
                    <div id="mainCreate">

                    </div>

                    <button class="mt-2 btn btn-outline-dark gradient_focus"> Сохранить</button>
                </form>
            </div>
        </div>
    </div>

    @include('setting.LetScript')
    @include('setting.function')
   {{-- @include('setting.script')--}}
@endsection
