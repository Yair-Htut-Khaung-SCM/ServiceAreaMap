<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSV Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/upload.css') }}">
    <?php
    if (getenv('APP_ENV') === 'local') {
        $_GOOGLE_MAP_API_KEY = 'xxxxxxxxxxxxxxxxxxxxxxxxxx';
    } else {
        $_GOOGLE_MAP_API_KEY = 'xxxxxxxxxxxxxxxxxxxxxxxxxx';
    }
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?callback=initMap&key=<?php echo $_GOOGLE_MAP_API_KEY; ?>&v=weekly" defer ></script>
</head>
<body>
    @include('layouts.navbar')

    @yield ('content')


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/d97b87339f.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/serviceareamap.js') }}"></script>
    <script src="{{ asset('js/serviceareamaptop.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
</body>
</html>