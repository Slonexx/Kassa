
@extends('widgets.widget')

@section('content')


    <div class="content p-4 mt-2 bg-white text-Black rounded">

        <div class="row gradient rounded p-2">
            <div class="col-10">
                <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                    <span class="text-white"> Настройки &#8594; кассовый аппарат </span>
                </div>
            </div>
            <div class="col-2">
                <button onclick="add_device()" type="button" class=" btn transparent btn-outline-warning"> <i class="fa-solid fa-circle-plus"></i> Добавить </button>
            </div>
        </div>


    </div>


@endsection



