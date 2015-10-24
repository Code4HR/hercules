function initAutocomplete() {

    var map, crimeheatmap, colheatmap, foodSanitationMarker;
    var heatMapData = [];
    var Datas = [];
    var foodSanitationImage = "../imgs/foodSanitationImage.png"

    function initialize() {
        var styleArray = [
            {
                "featureType": "road.arterial",
                "stylers": [
                    { "hue": "#00ccff" }
                ]
            },
            {
                "stylers": [
                    { "visibility": "simplified" },
                    { "hue": "#0099ff" },
                    { "weight": 0.7 }
                ]
            },
            {
                "featureType": "poi.school",
                "stylers": [
                    { "hue": "#cc00ff" }
                ]
            },
        ];
        var mapOptions = {
            zoom: 11,
            center: new google.maps.LatLng(36.9487874, -76.2121092),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: styleArray
        };
        map = new google.maps.Map(document.getElementById('map'),
                                  mapOptions);
        var pointArray = new google.maps.MVCArray(Datas);
        crimeheatmap = new google.maps.visualization.HeatmapLayer({
            data: pointArray,
            maxIntensity: 12,
            dissipate: false
        });
        crimeheatmap.setMap(map);
        colheatmap = new google.maps.visualization.HeatmapLayer({
            data: pointArray,
            maxIntensity: 12,
            dissipate: false,
            radius: 100
        });
        colheatmap.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    $('#crimeSlider').change(function(){
        var crimeHeatMapData = [];
        var data = {};
        data.slidervalue = $(this).text();
        $.ajax({
            method: 'GET',
            url: '/crime',
            data: data,
            dataType: 'json',
            success: function(crimeData) {
                for (var elem = 0, max = crimeData.length; elem < max; elem++) {
                    crimeHeatMapData.push({location: new google.maps.LatLng(crimeData[elem].latitude, crimeData[elem].longitude), weight: crimeData[elem].severity});
                }
                crimeheatmap.set('data', crimeHeatMapData);
            }
        });
    });
    $('#colSlider').change(function(){
        var colHeatMapData = [];
        var data = {};
        data.slidervalue = $(this).text();
        $.ajax({
            method: 'GET',
            url: '/col',
            data: data,
            dataType: 'json',
            success: function(colData) {
                for (var elem = 0, max = colData.length; elem < max; elem++) {
                    colHeatMapData.push({location: new google.maps.LatLng(colData[elem].lat, colData[elem].lon), weight: colData[elem].weight});
                }
                colheatmap.set('data', colHeatMapData);
            }
        });
    });
    $('#foodSanitationSlider').change(function(){
        var newHeatMapData = [];
        var data = {};
        data.slidervalue = $(this).text();
        alert(data.slidervalue);
        $.ajax({
            method: 'GET',
            url: '/food/sanitation',
            data: data,
            dataType: 'json',
            success: function(foodSanitationData) {
                for (var elem = 0, max = foodSanitationData.length; elem < max; elem++) {
                    if (foodSanitationData[elem].latitude !== null && foodSanitationData[elem].longitude !== null){
                        foodSanitationMarker = new google.maps.Marker({
                            position: {lat: foodSanitationData[elem].latitude, lng: foodSanitationData[elem].longitude},
                            title: foodSanitationData[elem].name,
                            map: map,
                            icon: foodSanitationImage
                        })
                    };
                }
            }
        });
    });



    //    // Create the search box and link it to the UI element.
    //    var input = document.getElementById('pac-input');
    //    var searchBox = new google.maps.places.SearchBox(input);
    //    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    //
    //    // Bias the SearchBox results towards current map's viewport.
    //    map.addListener('bounds_changed', function() {
    //        searchBox.setBounds(map.getBounds());
    //    });
    //
    //
    //    var markers = [];
    //    // [START region_getplaces]
    //    // Listen for the event fired when the user selects a prediction and retrieve
    //    // more details for that place.
    //    searchBox.addListener('places_changed', function() {
    //        var places = searchBox.getPlaces();
    //
    //        if (places.length == 0) {
    //            return;
    //        }
    //
    //        // Clear out the old markers.
    //        markers.forEach(function(marker) {
    //            marker.setMap(null);
    //        });
    //        markers = [];
    //
    //        // For each place, get the icon, name and location.
    //        var bounds = new google.maps.LatLngBounds();
    //        places.forEach(function(place) {
    //            var icon = {
    //                url: place.icon,
    //                size: new google.maps.Size(71, 71),
    //                origin: new google.maps.Point(0, 0),
    //                anchor: new google.maps.Point(17, 34),
    //                scaledSize: new google.maps.Size(25, 25)
    //            };
    //
    //            // Create a marker for each place.
    //            markers.push(new google.maps.Marker({
    //                map: map,
    //                icon: icon,
    //                title: place.name,
    //                position: place.geometry.location
    //            }));
    //
    //            if (place.geometry.viewport) {
    //                // Only geocodes have viewport.
    //                bounds.union(place.geometry.viewport);
    //            } else {
    //                bounds.extend(place.geometry.location);
    //            }
    //        });
    //        map.fitBounds(bounds);
    //    });
    //    // [END region_getplaces]


}