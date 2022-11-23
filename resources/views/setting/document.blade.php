
@extends('layout')
@section('item', 'link_3')
@section('content')

    <div class="content p-4 mt-2 bg-white text-Black rounded">

        <div class="row gradient rounded p-2 pb-2" style="margin-top: -1rem">
            <div class="col-10" style="margin-top: 1.2rem"> <span class="text-white" style="font-size: 20px"> Настройки &#8594; Документ </span> </div>
            <div class="col-2 text-center">
                <img src="https://smarttis.kz/Config/logo.png" width="40%"  alt="">
                <div class="text-white" style="font-size: 11px; margin-top: 8px"> Топ партнёр сервиса МойСклад </div>
            </div>
        </div>


        @isset($message)

            <div class="mt-2 {{$message['alert']}}"> {{ $message['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        @endisset

        <form action="/Setting/Document/{{ $accountId }}?isAdmin={{ $isAdmin }}" method="post" class="mt-3">
        @csrf <!-- {{ csrf_field() }} -->

            <div id="DOCUMENT " >
                <div class="row mb-2">
                    <div class="col-10">
                        <div class="mx-3"> <h5>Документ</h5></div>
                    </div>
                </div>

                <div class="mb-3 row mx-2">
                    <div class="col-6">
                        <label class="mt-1 mx-4"> Выберите какой тип платежного документа создавать: </label>
                    </div>
                    <div class="col-6 row">
                        <div class="col-12">
                            <select id="paymentDocument" name="paymentDocument" class="form-select text-black" >
                                @if ($paymentDocument == "0")
                                    <option selected value="0">Не создавать</option>
                                    <option value="1">Приходной ордер</option>
                                    <option value="2">Входящий платёж </option>
                                @endif
                                @if ($paymentDocument == "1")
                                    <option value="0">Не создавать</option>
                                    <option selected value="1">Приходной ордер</option>
                                    <option value="2">Входящий платёж </option>
                                @endif
                                @if ($paymentDocument == "2")
                                    <option value="0">Не создавать</option>
                                    <option value="1">Приходной ордер</option>
                                    <option selected value="2">Входящий платёж </option>
                                @endif
                            </select>
                        </div>


                    </div>
                </div>
            </div>
            <hr class="href_padding">

            <button class="btn btn-outline-dark " data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa-solid fa-arrow-down-to-arc"></i> Сохранить </button>
        </form>
    </div>


@endsection



