(function(){

    var SearchType = {
        zipcode:  1,
        address:  2,
        freeword: 3
    }
    
    var prefecture = $('.ServiceArea_Prefectures').children();
    var imgDir = "/img/serviceareamap/";
    var linkPath = "./serviceareatop/serviceareadetail";

    //mouserover aka hover areaName and division map will light up
    prefecture.mouseover(function() {
        var areaName = $(this).parent().parent().attr('id');

        var img = imgDir + areaName + ".png";
        $("#ServiceArea_Myanmar").attr("src", img);
    })

    //go to serviceareadetail page after click a city
    prefecture.click(function() {
        var prefecture = $(this).text();
        transition(prefecture, SearchType.address);
    })

    /**
     * Include the entered string and search type in the query and perform a screen transition
     * @param {Entered keyword text} inputText
     * @param {Search type} searchType
     */
    function transition(inputText, searchType) {
        var hasInputText = searchType == SearchType.zipcode || searchType == SearchType.freeword;
        
        setItem('eltres-servicearea-inputtext', hasInputText ? inputText : "");
        setItem('eltres-servicearea-keyword', inputText);
        setItem('eltres-servicearea-type', searchType);

        var inputItem = getItem('eltres-servicearea-inputtext');
        var keywordItem = getItem('eltres-servicearea-keyword');
        var typeItem = getItem('eltres-servicearea-type');

        if(inputItem != undefined && keywordItem && typeItem) {
            window.location.href = linkPath
        } else {
            window.location.href = linkPath + "?target=" + inputText + "&type=" + searchType;
        }
    }

    function setItem(key, value) {
        try {
            sessionStorage.setItem(key, value);
        } catch(e) {
            return false;
        }
    }

    function getItem(key) {
        try {
            return sessionStorage.getItem(key);
        } catch(e) {
            return  false;
        }
    }

}());