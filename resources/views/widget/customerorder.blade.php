
@extends('widget.widget')

@section('content')


    <div class="p-1">
        <div class="row gradient rounded p-2">
            <div class="col-11">
                <div class="mx-2"> <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                    <span class="text-white"> РеКасса 3.0 </span>
                </div>
            </div>
            <div class="col-1">
                <button type="submit" onclick="update()" class="myButton btn "> <i class="fa-solid fa-arrow-rotate-right"></i> </button>
            </div>
        </div>
        <div class="row mt-4 rounded bg-white">
            <div class="col-1"></div>
            <button onclick="" class="col-10 btn btn-warning text-black rounded-pill"> Фискализация </button>
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

