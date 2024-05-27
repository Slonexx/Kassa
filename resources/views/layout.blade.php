<!doctype html>
<html lang="en">
@include('head')
<body style="background-color:#dcdcdc;">

<div class="page headfull">
    <div class="sidenav">

        <div class="p-2 gradient ">
            <div class="row text-white">
                <img src="https://static.tildacdn.pro/tild6231-6435-4138-a431-346337363461/Layer_1.svg" width="55" height="55" alt="">
            </div>
        </div>
        <br>
        <div class="toc-list-h1">
            <a id="link_1" class="mt-2 mb-2" href="/{{$accountId}}?isAdmin={{ request()->isAdmin }}">Главная </a>
            @if (request()->isAdmin !== null && request()->isAdmin === 'ALL')
                <div>
                    <button id="btn_1" class="dropdown-btn">Настройки <i class="fa fa-caret-down"></i> </button>
                    <div class="dropdown-container">
                        <a id="link_2" class="mt-1" href="/Setting/Device/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Кассовый аппарат </a>
                        <a id="link_3" class="mt-1" href="/Setting/Document/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Документ </a>
                        <a id="link_4" class="mt-1" href="/Setting/Worker/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Сотрудники </a>
                        <a id="link_5" class="mt-1" href="/Setting/Automation/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Автоматизация </a>
                    </div>
                </div>
            @endif
            <a id="link_6" class="mt-1" href="/kassa/change/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Смена </a>
        </div>

        <div class="mt-2 mb-2">
            <button class="dropdown-btn">Помощь <i class="fa fa-caret-down"></i> </button>
            <div class="dropdown-container">
                <a target="_blank" href="https://smartrekassa.bitrix24.site/contact/">
                    <i class="fa-solid fa-address-book"></i>
                    Контакты </a>
                <a target="_blank" href="https://api.whatsapp.com/send/?phone=77232400545&text=">
                    <i class="fa-brands fa-whatsapp"></i>
                    Написать на WhatsApp </a>
                <a target="_blank" href="https://smartrekassa.bitrix24.site/instruktsiiponastroyke">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    Инструкция </a>
            </div>
        </div>

    </div>

    <div class="main head-full">
        @yield('content')
    </div>
</div>

@include('style')

<script>
    let dropdown = document.getElementsByClassName("dropdown-btn");

    for (let i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function () {
            this.classList.toggle("active");
            let dropdownContent = this.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        });
    }

    let item = '@yield('item')';

    window.document.getElementById(item).classList.add('active_sprint');

    if (item.replace(/[^+\d]/g, '') > 1 && item.replace(/[^+\d]/g, '') <= 5) {
        this_click(window.document.getElementById('btn_1'));
    }

    function this_click(btn) {
        btn.classList.toggle("active");
        let dropdownContent = btn.nextElementSibling;
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    }
</script>

</body>
</html>
