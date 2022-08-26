<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>reKassa</title>
    <script src="{{ asset("Config/js/uikit.js") }}"></script>
    <link rel="stylesheet" href="{{ asset("Config/css/uikit.css") }}">


</head>


<body class="bg-white">

@yield('content')

<style>
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 12pt;
    }

    .gradient{
        /* background: rgb(145,0,253);
         background: linear-gradient(34deg, rgba(145,0,253,1) 0%, rgba(232,0,141,1) 100%);*/
        background-image: radial-gradient( circle farthest-corner at 10% 20%,  rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90% );
    }

</style>

</body>
</html>

