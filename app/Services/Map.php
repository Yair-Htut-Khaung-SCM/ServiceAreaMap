<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;


class Map
{

    private function degreesToRadians($degrees)
    {
        return ($degrees * pi()) / 180;
    }

    private function latLonToOffsets($latitude, $longitude, $mapWidth, $mapHeight)
    {
        /*
        false easting 
        for add +180 from equator it will arrive to minus point of the map 
        which is in south places
        */
        $FE = 180;

        // from center of the map to the outeredge of the map by pixel unit
        $radius = $mapWidth / (2 * pi());

        /* 
        convert to radian value ( math caculatable spere arc length ) by 
        latitude point 0 degree from prime meridian line
        to latitude degree point
        */
        $latRad = $this->degreesToRadians($latitude);

        /* 
        convert to radian value ( math caculatable spere arc length ) by 
        longitude point 0 degree from equator line
        to longitude degree point and FE is make sure for caculatable positive value
        */
        $lonRad = $this->degreesToRadians($longitude + $FE);

        /* 
        multipling $lonRad and $radius mean 
        form prime merdian line to map outeredge of x-axis in pixel unit
        */
        $x = $lonRad * $radius;


        /* 
        need to find value of yFromEquator because y-axis is not simple as x-axis
        x-axis is simply from prime meridian line 0 to 360 around the earth
        but for y-axis .. the standard value -90 for south poe and 90 to north poe
        from base of equator line .. so we need to make more step 
        the reason for $latRad is have to divide by /2 is becase of tan() 
        tan(45) is equal to 1 .. which use here is tan(pi() / 4) 
        which can has math advantage and more simple to caculate trigonometry map length
        the current latRad range is -90 to 90 which is standard so we have to adjust 45 degree 
        to able to caculate map trigonometry easily so to get 45 degree .. -90 to 90 is divide by 2 make -45 degree to 45 degree
        so finally we got $yFromEquator value by * with center map to outeredge pixel value which is $radius
        so $mapHeight / 2 is half of the map which is center .. so - $yFromEquator mean from equator line to center point of the map
        */
        $yFromEquator = $radius * log(tan(pi() / 4 + $latRad / 2));
        $y = $mapHeight / 2 - $yFromEquator;


        return array($x, $y);
    }

    private function addPointsByZoomLevel($levelData, $zoom_level, $tmpTileX, $tmpTileY, $tmpPoint)
    {
        if (array_key_exists($zoom_level, $levelData)) {
            if (array_key_exists($tmpTileX, $levelData[$zoom_level])) {
                if (array_key_exists($tmpTileY, $levelData[$zoom_level][$tmpTileX])) {
                    array_push($levelData[$zoom_level][$tmpTileX][$tmpTileY], $tmpPoint);
                } else {
                    $levelData[$zoom_level][$tmpTileX][$tmpTileY] = [
                        $tmpPoint
                    ];
                }
            } else {
                $levelData[$zoom_level][$tmpTileX] = [
                    $tmpTileY => [
                        $tmpPoint
                    ]
                ];
            }
        } else {
            $levelData[$zoom_level] = [
                $tmpTileX => [
                    $tmpTileY => [
                        $tmpPoint
                    ]
                ]
            ];
        }

        return $levelData;
    }

    public function meshListToTileImages(string $file)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "-1");

        $mesh_code_size = 1;
        $base_tile_size = 256;
        $min_zoom_level = 12;
        $max_zoom_level = 15;

        $mesh_lists_csv = [];
        foreach (preg_split('/[\r\n]+/', $file) as $line) {
            $line = rtrim($line);
            if ($line == '') continue;
            $d = preg_split('/\,/', $line);
            $meshcode = $d[0];
            $class = $d[1];

            $mesh_lists_csv[substr($meshcode, 0, 2)][$meshcode] = $class;
        }

        $groupPixelDotSizeList = [];

        foreach ($mesh_lists_csv as $group_name => $group) {
            $pixelDotSizeList = [];

            /*
            basically group name is all of the pixel from center to outerborders square of the map
            from bottom to the top
            for 454205 mean the point cell of group '45'4205 to map more detail .. like row 4 and column 5 
            the the result cell of '45' cell to divide again '42' to the map more detail .. like row 4 and colum 2
            and so on
            */
            $constantMashCodeList = [$group_name . "450001", $group_name . "450002", $group_name . "450010"];

            for ($zoom_level = $min_zoom_level; $zoom_level <= $max_zoom_level; $zoom_level++) {

                $tmpConstantXYList = [];

                foreach ($constantMashCodeList as $meshcode) {

                    $mesh_image_width = 1024 * pow(2, $zoom_level - 2);
                    $mesh_image_height = 1024 * pow(2, $zoom_level - 2);
                    $mesh1 = substr($meshcode, 0, 4);
                    $mesh2 = substr($meshcode, 4, 2);
                    $mesh3 = substr($meshcode, 6, 2);
                    $lat = (intval(substr($mesh1, 0, 2)) * 80 +
                        intval(substr($mesh2, 0, 1)) * 10 + intval(substr($mesh3, 0, 1))) * (45 / 3600);
                    $lng = (intval(substr($mesh1, 2, 4)) * 80 +
                    intval(substr($mesh2, 1, 2)) * 10 + intval(substr($mesh3, 1, 2))) * (45 / 3600) + 0.0015;

                    $coor = $this->latLonToOffsets($lat, $lng, $mesh_image_width, $mesh_image_height);

                    array_push($tmpConstantXYList, $coor);

                }
                // custom pixel width and height
                $width = ($tmpConstantXYList[1][0] - $tmpConstantXYList[0][0]);
                $height = ($tmpConstantXYList[0][1] - $tmpConstantXYList[2][1]);

                // each zoom level's pixel width and height
                $pixelDotSizeList[$zoom_level] = [$width <= 0 ? 0.5 : $width, $height <= 0 ? 0.5 : $height];

            }

            $groupPixelDotSizeList[$group_name] = $pixelDotSizeList;

        }

        // basically from westest border to the eastest outer border
        $levelData = [];
        foreach ($mesh_lists_csv as $group_name => $mesh_group) {
            foreach ($mesh_group as $meshcode => $class) {

                for ($zoom_level = $min_zoom_level; $zoom_level <= $max_zoom_level; $zoom_level++) {
                    $mesh_image_width = (1024 * pow(2, $zoom_level - 2));
                    $mesh_image_height = (1024 * pow(2, $zoom_level - 2));

                    $mesh1 = substr($meshcode, 0, 4);
                    $mesh2 = substr($meshcode, 4, 2);
                    $mesh3 = substr($meshcode, 6, 2);


                    $lat = (intval(substr($mesh1, 0, 2)) * 80 +
                    intval(substr($mesh2, 0, 1)) * 10 + intval(substr($mesh3, 0, 1))) * (45 / 3600);
                    $lng = (intval(substr($mesh1, 2, 4)) * 80 +
                    intval(substr($mesh2, 1, 2)) * 10 + intval(substr($mesh3, 1, 2))) * (45 / 3600);

                    $coor = $this->latLonToOffsets($lat, $lng, ($mesh_image_width), ($mesh_image_height));

                    $x = $coor[0];
                    // since it been convert to 2D .. so have to change y-axis of each pixel point
                    $y = $coor[1] - $groupPixelDotSizeList[$group_name][$zoom_level][1];

                    // to convert pixel coordinates to tile coordinates
                    $tmpTileX = intval($x / $base_tile_size);
                    $tmpTileY = intval($y / $base_tile_size);


                    $tmpPoint = [
                        "x" => ($x - ($base_tile_size * $tmpTileX)),
                        "y" => ($y - ($base_tile_size * $tmpTileY)),
                        "x_end" => (($x - ($base_tile_size * $tmpTileX)) + $groupPixelDotSizeList[$group_name][$zoom_level][0]),
                        "y_end" => (($y - ($base_tile_size * $tmpTileY)) + $groupPixelDotSizeList[$group_name][$zoom_level][1]),
                        "color" => $class
                    ];

                    $totalWidth = $groupPixelDotSizeList[$group_name][$zoom_level][0];
                    $totalHeight = $groupPixelDotSizeList[$group_name][$zoom_level][1];

                    $maxXTileNo = ceil(($tmpPoint["x"] + $totalWidth) / $base_tile_size);
                    $maxYTileNo = ceil(($tmpPoint["y"] + $totalHeight) / $base_tile_size);

                    if ($maxXTileNo == 1 && $maxYTileNo == 1) {
                        $levelData = $this->addPointsByZoomLevel($levelData, $zoom_level, $tmpTileX, $tmpTileY, $tmpPoint);
                    } else if ($maxXTileNo == 2 && $maxYTileNo == 1) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"],
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"] - $base_tile_size,
                                "y_end" => $tmpPoint["y_end"],
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 3 && $maxYTileNo == 1) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"],
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"],
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $tmpPoint["y_end"],
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 1 && $maxYTileNo == 2) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"],
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 1,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"],
                                "y_end" => $tmpPoint["y_end"] - $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 1 && $maxYTileNo == 3) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"],
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 1,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"],
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 2,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"],
                                "y_end" => $tmpPoint["y_end"] - ($base_tile_size * 2),
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 2 && $maxYTileNo == 2) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        // var_dump($tmpPoint["x"], $tmpPoint["y"]);
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"] - $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        // var_dump($tmpPoint["x"], $tmpPoint["y"]);
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 1,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY + 1,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"] - $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 3 && $maxYTileNo == 2) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 1,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY + 1,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY + 1,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $tmpPoint["y_end"] - ($base_tile_size * 1),
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    } else if ($maxXTileNo == 3 && $maxYTileNo == 3) {
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );
                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY,
                            [
                                "x" => 0,
                                "y" => $tmpPoint["y"],
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 1,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY + 1,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY + 1,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $base_tile_size,
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX,
                            $tmpTileY + 2,
                            [
                                "x" => $tmpPoint["x"],
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - ($base_tile_size * 2),
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 1,
                            $tmpTileY + 2,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $base_tile_size,
                                "y_end" => $tmpPoint["y_end"] - ($base_tile_size * 2),
                                "color" => $tmpPoint["color"]
                            ]
                        );

                        $levelData = $this->addPointsByZoomLevel(
                            $levelData,
                            $zoom_level,
                            $tmpTileX + 2,
                            $tmpTileY + 2,
                            [
                                "x" => 0,
                                "y" => 0,
                                "x_end" => $tmpPoint["x_end"] - ($base_tile_size * 2),
                                "y_end" => $tmpPoint["y_end"] - ($base_tile_size * 2),
                                "color" => $tmpPoint["color"]
                            ]
                        );
                    }
                }

            }
        }

        $im = imagecreatetruecolor($mesh_code_size, $mesh_code_size);
        // land's color
        $class_colors = [
            1 => imagecolorallocatealpha($im, 255, 0, 0, 0),
            2 => imagecolorallocatealpha($im, 255, 187, 61, 0),
            3 => imagecolorallocatealpha($im, 133, 43, 230, 0),
            4 => imagecolorallocatealpha($im, 100, 112, 255, 0),
        ];
        // sea color
        $class_colors_sea = [
            3 => imagecolorallocatealpha($im, 118, 74, 241, 0),
            4 => imagecolorallocatealpha($im, 118, 74, 241, 0),
        ];


        for ($zoom_level = $min_zoom_level; $zoom_level <= $max_zoom_level; $zoom_level++) {
            $opacity = 0.379;
            foreach ($levelData[$zoom_level] as $lat => $tileX) {

                foreach ($tileX as $lng => $tileY) {

                    $tile = imagecreatetruecolor($base_tile_size, $base_tile_size);
                    $tile_back = imagecolorallocatealpha($tile, 255, 255, 255, 127);
                    imagefill($tile, 0, 0, $tile_back);

                    // sea
                    $sea_tile = imagecreatetruecolor($base_tile_size, $base_tile_size);
                    $sea_tile_back = imagecolorallocatealpha($sea_tile, 255, 255, 255, 127);
                    imagefill($sea_tile, 0, 0, $sea_tile_back);
                    foreach ($tileY as $index => $point) {
                        // land rectangle png
                        if($point["color"] == 1 || $point["color"] == 2 || $point["color"] == 3 || $point["color"] == 4){

                            imagefilledrectangle(
                                $tile,
                                $point["x"],
                                $point["y"],
                                $point["x_end"],
                                $point["y_end"],
                                $class_colors[$point["color"]]
                            );

                        }
                        // sea rectangle png
                        if($point["color"] == 3 || $point["color"] == 4){
                            imagefilledrectangle(
                                $sea_tile,
                                $point["x"],
                                $point["y"],
                                $point["x_end"],
                                $point["y_end"],
                                $class_colors_sea[$point["color"]]
                            );
                        }
                    }
                    imagealphablending($tile, false);
                    imagesavealpha($tile, true);
                    $transparency = 1 - $opacity;
                    imagefilter($tile, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);

                    ob_start();
                    imagepng($tile);
                    $tile_image_data = ob_get_contents();
                    ob_get_clean();

                    // land png upload to s3 bucket
                    Storage::disk('public')->put(config('application.map.tile_images_path') .
                        "${zoom_level}/${lat}/${lng}.png", $tile_image_data, 'public');


                    imagealphablending($sea_tile, false);
                    imagesavealpha($sea_tile, true);
                    imagefilter($sea_tile, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);
                    ob_start();
                    imagepng($sea_tile);
                    $tile_image_data_sea = ob_get_contents();
                    ob_get_clean();

                    // sea png upload to s3 bucket
                    Storage::disk('public')->put(config('application.map.sea_tile_images_path') .
                        "${zoom_level}/${lat}/${lng}.png", $tile_image_data_sea, 'public');


                }
            }
        }
    }
}
