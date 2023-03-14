
@extends('layout')
@section('item', 'link_10')
@section('content')

    <div class="content p-4 mt-2 bg-white text-Black rounded">
        @include('div.TopServicePartner')
        <script>NAME_HEADER_TOP_SERVICE("Информация")</script>
            <div class="mt-2 alert alert-danger text-center"> <i class="fa-solid fa-screwdriver-wrench"></i>
              Сначала нужно подключить кассовый аппарат
            </div>
    </div>


@endsection



