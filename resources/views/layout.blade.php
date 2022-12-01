<!doctype html>
<html lang="en">
@include('head')
<body style="background-color:#dcdcdc;">

<div class="page headfull">
        <div class="sidenav">

            <div class="p-2 gradient ">
                <div class="row text-white">
                    <div class="col-2">
                        <img src="https://app.rekassa.kz/static/logo.png" width="35" height="35"  alt="">
                    </div>
                    <div class="mt-1 col-10">
                        <label class="s-min-16"> reKassa </label>
                    </div>

                </div>
            </div>
            <br>
                <div class="toc-list-h1">
                    <a id="link_1" class="mt-2 mb-2" href="/{{$accountId}}?isAdmin={{ request()->isAdmin }}">Главная </a>
                    <div>
                        @if ( request()->isAdmin == null )
                        @else
                            @if( request()->isAdmin == 'ALL')
                                    <button id="btn_1" class="dropdown-btn">Настройки <i class="fa fa-caret-down"></i> </button>
                                    <div class="dropdown-container">
                                        <a id="link_2" class="mt-1" href="/Setting/Device/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Кассовый аппарат </a>
                                        <a id="link_3" class="mt-1" href="/Setting/Document/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Документ </a>
                                        <a id="link_4" class="mt-1" href="/Setting/Worker/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Сотрудники </a>
                                    </div>
                            @endif
                        @endif
                    </div>
                    <a id="link_6" class="mt-1" href="/kassa/change/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Смена </a>
                </div>

            <div class="mt-2 mb-2" >
                <button class="dropdown-btn">Помощь <i class="fa fa-caret-down"></i> </button>
                    <div class="dropdown-container">
                        <a target="_blank" href="https://smartrekassa.bitrix24.site/contact/">
                            <i class="fa-solid fa-address-book"></i>
                            Контакты </a>
                        <a target="_blank" href="https://api.whatsapp.com/send/?phone=77232400545&text=" >
                            <i class="fa-brands fa-whatsapp"></i>
                            Написать на WhatsApp </a>
                        <a target="_blank" href="https://smartrekassa.bitrix24.site/instruktsiiponastroyke" >
                            <i class="fa-solid fa-chalkboard-user"></i>
                             Инструкция </a>
                    </div>
            </div>

        </div>

        <div class="main head-full">
                @yield('content')
        </div>
    </div>

</body>
</html>


<script>
    var dropdown = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }

    let item = '@yield('item')'

    window.document.getElementById(item).classList.add('active_sprint')
    if (item.replace(/[^+\d]/g, '') > 1 && item.replace(/[^+\d]/g, '') <= 5){
        this_click(window.document.getElementById('btn_1'))
    }

    function this_click(btn){
        btn.classList.toggle("active");
        let dropdownContent = btn.nextElementSibling;
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    }

</script>
@include('style')
