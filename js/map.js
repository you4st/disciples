var geocoder;
var map;
var directionsDisplay;
var directionsService;
var churchLocation = '4242 Jones Ave NE, Renton, WA 98056';
var markers = [];

function initialize() {
    var mapOptions = {
      zoom: 13,
      center: new google.maps.LatLng(47.529551, -122.196193), //church lat/long
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    geocoder = new google.maps.Geocoder();
    directionsService = new google.maps.DirectionsService();
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);
}


function codeAddress(address, name) {
	deleteMarkers();
	directionsDisplay.setMap(null);
    geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            addMarker(results[0].geometry.location, name, address);
        } else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });    
}

function generateRoute(address) {
	deleteMarkers();
	directionsDisplay.setMap(map);
    var request = {
        origin:churchLocation,
        destination:address,
        travelMode: google.maps.TravelMode.DRIVING
    };
    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        }
    });
}

function addMarker(location, name, address) {
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
    var infowindow = new google.maps.InfoWindow({
        content: '<h5>' + name + '</h5>' + address
    });

    // IF I REMOVE THIS PART -> IT WORKS, BUT WITHOUT INFOWINDOW
    google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map, marker);
    });
    markers.push(marker);
}

function deleteMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}