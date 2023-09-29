(function(){

    var prefecture = $('.ServiceArea_Prefectures').children();

    var imgDir = "/img/serviceareamap/";

    //mouserover aka hover areaName and division map will light up
    prefecture.mouseover(function() {
        var areaName = $(this).parent().parent().attr('id');

        var img = imgDir + areaName + ".png";
        $("#ServiceArea_Myanmar").attr("src", img);
    })

}());