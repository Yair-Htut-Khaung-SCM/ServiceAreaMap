<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ServiceAreaDetail</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <?php
    if (getenv('APP_ENV') === 'local') {
        $_GOOGLE_MAP_API_KEY = 'AIzaSyCLWMCSlaM3ZNyC4lswtoqfdGDnTZ5wv2Q';
    } else {
        $_GOOGLE_MAP_API_KEY = 'AIzaSyAjft-0tqg2e_oHJaQO-0C31PguYRF4V9g';
    }
?>
<script
  src="https://maps.googleapis.com/maps/api/js?callback=initMap&key=<?php echo $_GOOGLE_MAP_API_KEY; ?>&v=weekly"
  defer
></script>

</head>
<body>
    <div class="m_sec">

        <div class="l_container l_container_areamap">

            <h1 class="m_ttl">Detailed Simulation Map</h1>

            <hr>

            <div class="ServiceArea_Head">
                <div class="ServiceArea_Section" id="Selected_Area">Current Selected Area：</div>
            </div>

            <div class="ServiceArea_slect" style="margin-top: 20px;margin-bottom: 20px;">
                <ul class="ServiceArea_Menu">
                    <li><a class="ServiceArea_Region">Kachin<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Puta-O</a></li>
                            <li><a class="ServiceArea_Prefecture">Myitkyina</a></li>
                            <li><a class="ServiceArea_Prefecture">Danai</a></li>
                        </ul>

                    </li>

                    <li><a class="ServiceArea_Region">Shan<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Keng-Tung</a></li>
                            <li><a class="ServiceArea_Prefecture">Lashio</a></li>
                            <li><a class="ServiceArea_Prefecture">Taunggyi</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Kayah<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Loikaw</a></li>
                            <li><a class="ServiceArea_Prefecture">Mawchi</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Kayin<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Kawkareik</a></li>
                            <li><a class="ServiceArea_Prefecture">Kyainseikgyi</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Bago<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Bago</a></li>
                            <li><a class="ServiceArea_Prefecture">Taungoo</a></li>
                            <li><a class="ServiceArea_Prefecture">Pyay</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Mon<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Mawlamyaing</a></li>
                            <li><a class="ServiceArea_Prefecture">Myawaddy</a></li>
                            <li><a class="ServiceArea_Prefecture">Mudon</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Tanintharyi<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Tanintharyi</a></li>
                            <li><a class="ServiceArea_Prefecture">Dawei</a></li>
                            <li><a class="ServiceArea_Prefecture">Myeik</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Yangon<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Kyimyindine</a></li>
                            <li><a class="ServiceArea_Prefecture">Botahtaung</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Ayeyarwady<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Pathein</a></li>
                            <li><a class="ServiceArea_Prefecture">Labutta</a></li>
                            <li><a class="ServiceArea_Prefecture">Pyapon</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Rakhine<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Sittwe</a></li>
                            <li><a class="ServiceArea_Prefecture">Gwa</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Mandalay<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Mandalay</a></li>
                            <li><a class="ServiceArea_Prefecture">Meiktila</a></li>
                            <li><a class="ServiceArea_Prefecture">Naypyidaw</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Magway<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Minhla</a></li>
                            <li><a class="ServiceArea_Prefecture">Pauk</a></li>
                            <li><a class="ServiceArea_Prefecture">Minbu</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Chin<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Hakha</a></li>
                            <li><a class="ServiceArea_Prefecture">Mindat</a></li>
                            <li><a class="ServiceArea_Prefecture">Matupi</a></li>
                        </ul>
                    </li>

                    <li><a class="ServiceArea_Region">Sagaing<span class="arrow"></span></a>
                        <ul class="ServiceArea_Sub_Menu">
                            <li><a class="ServiceArea_Prefecture">Sagaing</a></li>
                            <li><a class="ServiceArea_Prefecture">Chaung-U</a></li>
                            <li><a class="ServiceArea_Prefecture">Homalin</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div style="clear: both;"></div>

            <div class="AreaMap">
                <div id="ZMap"></div>
                <div id="map-loading-overlay" style="display: none;">
                    <div id="map-loading-spinner">
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/serviceareamap.js') }}"></script>
</body>
</html>