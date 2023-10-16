const domain = window.location.hostname;

let API_PROTOCOL = 'http:';
let API_HOST = domain + ':' + window.location.port;

let secretBaseUrl = '';
secretBaseUrl = API_PROTOCOL + '//' + API_HOST;
const CSV_PATH = '/map/mesh-list.json';
const GET_CSV_URL = secretBaseUrl + CSV_PATH;
const GOOGLE_MAP_API_KEY = 'AIzaSyCLWMCSlaM3ZNyC4lswtoqfdGDnTZ5wv2Q';
var paramMinZoom = 12;

const SearchType = {
  zipcode: 1,
  address: 2,
  freeword: 3
};

const ZoomLevel = {
  tod: 11,
  shk: 11,
  other: 13
};

/**
 *  the geographical coordinates (latitude and longitude)
 * @type {number[][]}
 */
const prefectureCenter = [
  [0, 0],
  [25.3953968, 97.3034361],
  [27.3155681, 97.3668899],
  [26.3587902, 96.7038394],
  [21.2807559, 99.5684256],
  [22.9547210, 97.7347725],
  [20.7870062, 97.0088189],
  [19.6750686, 97.1738071],
  [18.8184246, 97.1502327],
  [16.5639688, 98.2018069],
  [16.0436684, 98.1100773],
  [17.2963671, 96.3906372],
  [18.9249300, 96.4893547],
  [18.8239972, 95.2185265],
  [16.4538132, 97.6179874],
  [16.6952069, 98.4766385],
  [16.2543111, 97.7073594],
  [12.0854429, 99.0030383],
  [14.0818384, 98.1554592],
  [12.4520959, 98.4654277],
  [16.8035640, 96.0928032],
  [16.7702333, 96.1646027],
  [16.7866582, 94.7065676],
  [16.1475344, 94.7470163],
  [16.2859728, 95.6627223],
  [20.1675607, 92.6988292],
  [17.5579521, 94.5403159],
  [21.9404986, 95.9934221],
  [20.8787514, 95.816809],
  [19.7470758, 96.0472093],
  [17.9774670, 95.6999944],
  [21.4519455, 94.4402341],
  [20.1686687, 94.8461129],
  [22.6450641, 93.5822722],
  [21.6138160, 93.404001],
  [21.3711857, 93.8899679],
  [21.9178883, 95.7880905],
  [21.9595831, 95.2636957],
  [24.8861930, 94.8759759]
];

/**
 * prefecture code
 * @type {{Minbu: string, Bago: string, Meiktila: string, Pyay: string, Tanintharyi: string, Pauk: string, Keng-Tung: string, Mawchi: string, Gwa: string, Myitkyina: string, Labutta: string, Naypyidaw: string, Homalin: string, Pyapon: string, Mindat: string, Danai: string, Mudon: string, Chaung-U: string, Sittwe: string, Pathein: string, Botahtaung: string, Taungoo: string, Lashio: string, Loikaw: string, Kawkareik: string, Dawei: string, Puta-O: string, Kyainseikgyi: string, Myawaddy: string, Kyimyindine: string, Minhla: string, Mindat: string, Mandalay: string, Hakha: string, Sagaing: string, Taunggyi: string, Myeik: string, Mawlamyaing: string}}
 */
const prefectureCode = {
  'Myitkyina': '01',
  'Puta-O': '02',
  'Danai': '03',
  'Keng-Tung': '04',
  'Lashio': '05',
  'Taunggyi': '06',
  'Loikaw': '07',
  'Mawchi': '08',
  'Kawkareik': '09',
  'Kyainseikgyi': '10',
  'Bago': '11',
  'Taungoo': '12',
  'Pyay': '13',
  'Mawlamyaing': '14',
  'Myawaddy': '15',
  'Mudon': '16',
  'Tanintharyi': '17',
  'Dawei': '18',
  'Myeik': '19',
  'Kyimyindine': '20',
  'Botahtaung': '21',
  'Pathein': '22',
  'Labutta': '23',
  'Pyapon': '24',
  'Sittwe': '25',
  'Gwa': '26',
  'Mandalay': '27',
  'Meiktila': '28',
  'Naypyidaw': '29',
  'Minhla': '30',
  'Pauk': '31',
  'Minbu': '32',
  'Hakha': '33',
  'Matupi': '34',
  'Mindat': '35',
  'Sagaing': '36',
  'Chaung-U': '37',
  'Homalin': '38'
};


// prefecture name
let searchKeyword = '';
// SearchType[zipcode, address, freeword]
let searchType;
let inputText = '';
const keywordInput = $('#ServiceArea_Keyword_Input');
const searchButton = $('#ServiceArea_Search_Button');
const prefecture = $('.ServiceArea_Prefecture');


let myMap = null;
let meshAreaData = [];

//check first time for clicked page-serviceareamaptop to page-serviceareamap
if (!getKeywordAndType()) {
  const args = new Object;
  const urlParams = decodeURIComponent(location.search.substring(1)).split('&');

  if (urlParams !== '') {
    for (let i = 0; i < urlParams.length; i++) {
      const kv = urlParams[i].split('=');
      args[kv[0]] = kv[1];
    }
  }

  searchKeyword = args['target'];
  searchType = args['type'];
}
if (searchKeyword && searchType) {
  $('.switch_input').prop('checked', true);
}

prefecture.click(function () {
  let prefecture = $(this).text();
  transition(prefecture, SearchType.address);
});

$('.ServiceArea_Menu').children('li').click(function () {
  if (window.innerWidth > 736) return;

  $(this).children('a').attr('style', 'background-color: #b3e2dd');
  $(this).children('ul').slideToggle();
  $('.ServiceArea_Menu > li').not($(this)).children('ul').slideUp();
  $('.ServiceArea_Menu > li ').not($(this)).children('a').attr('style', 'background-color: #ffffff');
});

/**
 * Change the style on hover
 */
$('.ServiceArea_Menu > li').mouseenter(function () {
  if (window.innerWidth <= 736) return;

  const region = $(this).children('.ServiceArea_Region');
  region.css({
    'background-color': '#00ada9',
    'color': 'white',
    'opacity': '1'
  });
  region.children().first().css({
    'border-color': 'transparent transparent #ffffff #ffffff',
    'top': '17px',
    'transform': 'rotate(135deg)'
  });
}).mouseleave(function () {
  if (window.innerWidth <= 736) return;

  const region = $(this).children('.ServiceArea_Region');
  region.css({
    'background-color': '#ffffff',
    'color': '#00ada9'
  });
  region.children().first().css({
    'border-color': 'transparent transparent #00ada9 #00ada9',
    'top': '11px',
    'transform': 'rotate(-45deg)'
  });
});


function setItem(key, value) {
  try {
    sessionStorage.setItem(key, value);
  } catch (e) {
    return false;
  }
}

function getItem(key) {
  try {
    return sessionStorage.getItem(key);
  } catch (e) {
    return false;
  }
}

/**
 * Retrieve and assign information from the session
 * @returns {boolean}
 */
function getKeywordAndType() {
  const keywordItem = getItem('eltres-servicearea-keyword');
  const typeItem = getItem('eltres-servicearea-type');
  const inputItem = getItem('eltres-servicearea-inputtext');

  if (inputItem !== undefined && keywordItem && typeItem) {
    searchKeyword = keywordItem;
    searchType = typeItem;
    inputText = inputItem;
    return true;
  } else {
    return false;
  }
}

/**
 * Set information in the session
 * @param keyword
 * @param searchType
 * @param hasInputText
 */
function setKeywordAndType(keyword, searchType, hasInputText) {
  searchKeyword = keyword;
  searchType = searchType;
  inputText = keyword;
  setItem('eltres-servicearea-keyword', keyword);
  setItem('eltres-servicearea-type', searchType);
  setItem('eltres-servicearea-inputtext', hasInputText ? keyword : '');
}


function transition(inputText, searchType) {
  const hasInputText = searchType === SearchType.freeword;
  setKeywordAndType(inputText, searchType, hasInputText);
  setLocation(inputText);

  if (searchType === SearchType.address) {
    searchPrefecture(inputText);
  }
}


function areaMapGetRequest(url, success, failure, loadend) {
  const request = new XMLHttpRequest();

  request.open('GET', url, true);
  request.onload = function () {
    if (this.status === 200) {
      const data = JSON.parse(this.responseText);
      success(data);
    } else {
      failure();
    }
  };
  request.onerror = function () {
    failure();
  };
  request.onloadend = function () {
    loadend();
  };
  request.onabort = function () {
    failure();
  };
  request.send();
}

function setLocation(areaName) {
  const areaLabel = areaName.length > 0 ? areaName : '';
  $('#Selected_Area').text('Current Selected Area：' + areaLabel);
}

/**
 * Set the prefecture name and the current zoom level, and delete the map pins.
 * @param prefectureName prefecture name
 */
function setPrefecture(prefectureName) {
  if (!prefectureName) return;

  const code = Number(prefectureCode[prefectureName]);
  const center = prefectureCenter[code];
  const lat = center[0];
  const lng = center[1];

  myMap.setCenter(new window.google.maps.LatLng(lat, lng));
  myMap.setZoom(ZoomLevel.tod);
  if( prefectureName == 'Kyimyindine' || prefectureName == 'Botahtaung') {
    myMap.setZoom(ZoomLevel.other);
  }
}


function searchPrefecture(prefectureName) {
  if (typeof prefectureCode[prefectureName] === 'undefined') { // if null on prefecture return with alert message
    alert( prefectureName + ' is invalid');
    return;
  }
  setLocation(prefectureName);
  setPrefecture(prefectureName);
}

window.initMap = function () {
  // Basic information about Google Maps.
  myMap = new window.google.maps.Map(document.getElementById('ZMap'), {
    center: {
      lat: 16.8395368, // initial map set to Yangon
      lng: 95.8519061
    },
    zoomControl: true,
    streetViewControl: false,
    mapTypeControl: false,
    clickableIcons: false,
    scaleControl: true,
    mapTypeId: window.google.maps.MapTypeId.TERRAIN,
    zoom: 12,
    maxZoom: 15,
    minZoom: 5
  });

  const imageMapType = new google.maps.ImageMapType({
    getTileUrl: function (coord, zoom) {
      if (zoom < paramMinZoom) return null

      return `http://127.0.0.1:8000/storage/map/tile_images/${zoom}/${coord.x}/${coord.y}.png`;
    },
    tileSize: new google.maps.Size(256, 256),
    maxZoom: 15,
    minZoom: 5
  });

  myMap.overlayMapTypes.push(imageMapType);

  areaMapGetRequest(GET_CSV_URL, function (data) {
      meshAreaData = data;
      $('#map-loading-overlay').hide();
      if (
        typeof searchKeyword !== 'undefined' &&
        searchKeyword !== '' &&
        searchKeyword !== 'undefined'
      ) {
        transition(searchKeyword, searchType);
      }
    },
    function () {
      meshAreaData = [];
      $('#map-loading-overlay').hide();
      if (
        typeof searchKeyword !== 'undefined' &&
        searchKeyword !== '' &&
        searchKeyword !== 'undefined'
      ) {
        transition(searchKeyword, searchType);
      }
    },
    function () {
      setPrefecture(searchKeyword);
      window.google.maps.event.addListener(myMap);
    });
};
