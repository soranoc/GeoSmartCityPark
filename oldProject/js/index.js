/**
* @class index
*/

var map;
var markers = [];
var inMarker;
var addingToggle = false;

/**
* Search for a marker with specified id within an array and return its index (or -1 if not found).
*
* @param id the id (segment) to look for
* @array an array through which to search
*
* @return the first index at which an appropriate marker was found or -1
*/
function inArray(id, arr) {
    for (var i = 0; i < arr.length; i++) {
	if (arr[i].id === id)
	    return i;
    }
    return -1;
}

/**
* Loads and displays parkings as markers on the map in the specified area
*
* @method loadMarkers
*
* @param bounds the bounds of the area (leaflet LatLngBounds object)
*/
function loadMarkers(bounds) {
    //var url = "api/parking/zone/" + bounds.getSouth() + "/" + bounds.getWest() + "/" + bounds.getNorth() + "/" + bounds.getEast();
    var url = "api/api.php?request=parking/zone/" + bounds.getSouth() + "/" + bounds.getWest() + "/" + bounds.getNorth() + "/" + bounds.getEast();

    $.getJSON(url, function(data) {
	// copying old markers to be able to clean the inactive ones later
	var oldMarkers = markers.slice();
	markers = [];
	for (var i = 0; i < data.length; i++) {
	    var index = inArray(data[i].id, oldMarkers);
	    if (index !== -1) {
		// marker is already displayed, just need to transfed to active array
		markers.push(oldMarkers[index]);
		oldMarkers.splice(index, 1);
	    } else {
		// adding new marker to the map
		addMarker(data[i]);
	    }
	}

	// deleting these markers are they aren't visible anymore
	while (oldMarkers.length > 0) {
	    map.removeLayer(m = oldMarkers.pop());
	}
    });
}

/**
* Adds a marker to the map and to the list of markers
*
* @method addMarker
*
* @param infos {Object} Informations of the marker as returned by the api
*/
function addMarker(infos) {
    var marker;
	
    if(infos.payment == "Subject to a fee") {
	marker = new cilogi.L.Marker(new L.LatLng(infos.latitude, infos.longitude), {
	    fontIconColor: "#FF0000",
	    fontIconFont: "awesome",
	    fontIconName: "P"
	});
    } else {
	marker = new cilogi.L.Marker(new L.LatLng(infos.latitude, infos.longitude), {
	    fontIconColor: "#0000FF",
	    fontIconFont: "awesome",
	    fontIconName: "P"
	});
    }
    
    marker.addTo(map);
    marker.id = infos.id;
    
    marker.bindPopup("<b>" + infos.name + '</b>', {offset: new L.Point(0, -30)});
    marker.on("click", function() {
	displayDetails(infos.id);
    });
    markers.push(marker);
}

/**
* Adds a temporary marker (green) when the user tries to add a marker on the map
*
* @method addMarkerTmp
*
* @param lat {double} Latitude
* @param lng {double} Longitude
*/
function addTempMarker(lat, lng) {
    if (inMarker !== undefined)
		map.removeLayer(inMarker);
    
    inMarker = new cilogi.L.Marker(new L.LatLng(lat, lng), {
		fontIconColor: "#308014",
		fontIconFont: "awesome",
		fontIconName: "P"
    });
    
    inMarker.addTo(map);
}

/**
* Updates the 3 adress input fields with the location chosen on the map
*
* @method updateAdress
* 
* @param result {Object} Result
*/
function updateAddress(result) {
    if (result === undefined) {
	$("#entrance").empty();
	$("#walking").empty();
	$("#exit").empty();
    } else {
	$("#entrance").val(result.address.Match_addr);
	$("#walking").val(result.address.Match_addr);
	$("#exit").val(result.address.Match_addr);
    }
}
/**
* Fetches and displays the details of a marker in the side panel
*
* @method displayDetail
*
* @param id {Object} Id
*/
function displayDetails(id) {
    //var url = "api/parking/id/" + id;
    var url = "api/api.php?request=parking/id/" + id;
    
    $.getJSON(url, function(data) {
	console.log(data);
	var ul = $('#side_panel');
	ul.empty();
	ul.append("<thread>");
	ul.append("<h4>Name: <span class=\"label label-default\">" + data.name + "</span></h4>");
	ul.append("<h4>Max Capacity: <span class=\"label label-default\">" + data.slots + "</span></h4>");
	ul.append("<h4>Opened From: <span class=\"label label-default\">" + data.openingHour + "</span> To <span class=\"label label-default\">" + data.closingHour + "</h4>");
	ul.append("<h4>Entrance Adress: <span class=\"label label-default\">" + data.entrance + "</span></h4>");
	ul.append("<h4>Exit Adress: <span class=\"label label-default\">" + data.exit + "</span></h4>");
	ul.append("<h4>Walking Adress: <span class=\"label label-default\">" + data.walking + "</span></h4>");
	ul.append("<button id='toggle' onclick='toggleDetails()' class=\"btn btn-default\"  data-toggle='button'> Toggle details </button>");
	var spoiler = $("<div id='details'>");
	ul.append(spoiler);
	
	spoiler.append("<h4>Payment : <span class=\"label label-default\">" + data.payment + "</span></h4>");
	
	var list = $('<ul>');
	spoiler.append("<h4>Features: ");
	spoiler.append(list);
	for (var i = 0; i < data.features.length; i++) {
	    var feat = data.features[i];
	    console.log(feat);
	    list.append("<li><span class=\"label label-default\">" + 
			'<a href="#" data-toggle="tooltip" title="' + feat.desc + '" >' + feat.type + '</a></span></li>');
	}
	$('#side_panel').show();
	$('#input_form').hide();
	$('#details').hide();
    });
}

/**
* Stores a new position in the input form
*
* @method updatePosition
*
* @param lat {double} Latitude
* @param lng {double} Longitude
*/
function updatePosition(lat, lng) {
    $("#latitude").attr("value", lat);
    $("#longitude").attr("value", lng);
};

/**
* Toggle the details of current parking in the side panel
*
*  @method toggleDetails
*/
function toggleDetails() {
    $('#details').toggle();
}

/**
* Invoked by clicking on the map, displays a marker and updates the display
* and relevant fields in the input form
*
* @method onMapClick
*
* @param e {Object} Event
*/
function onMapClick(e) {    
    if (addingToggle === true) {
	loadFeatures();
	$('#side_panel').hide();
	$('#input_form').show();
	var geocodeService = new L.esri.Geocoding.Services.Geocoding();
	
	geocodeService.reverse().latlng(e.latlng).run(function(error, result) {
	    updateAddress(result);
	});
	updatePosition(e.latlng.lat, e.latlng.lng);
	addTempMarker(e.latlng.lat, e.latlng.lng);
    }
}

/**
* Makes API call to add a parking to the database using the informations
* entered in the input form
*
* @method addParking
*/
function addParking() {
    console.log("adding parking");
    
    $.ajax({
	url: "api/api.php?request=parking/add",
	method: "POST",
	data: {
	    latitude : $('#latitude').val(),
	    longitude : $('#longitude').val(),
	    name : $('#name').val(),
	    slots : $('#slots').val(),
	    openedFrom : $('#openedFrom').val(),
	    closedAt : $('#closedAt').val(),
	    entrance : $('#entrance').val(),
	    exit : $('#exit').val(),
	    walking : $('#walking').val(),
	    payment : $('#payment').val(),
	    features : $('#features').val()
	},
	success: function(msg,data, settings){
	    loadMarkers(map.getBounds());	    
	    clearForm();
	},
	error: function(msg, data, settings){
	    loadMarkers(map.getBounds()); // Temporary
	    console.log(msg + " / " + data + " / " + settings);
	    clearForm();
	}
    })
};

/**
* Clears the input form
*
* @method clearForm
*/
function clearForm(){
	$("#name").val("");
	$("#slots").val("");
	$("#openedFrom").val("");
	$("#closeAt").val("");
	$("#entrance").val("");
	$("#exit").val("");
	$("#walking").val("");
	$("#services").val("");
}

/**
* Switches the map mode (browsing <-> adding) and updates the display accordingly
*
* @method changeMode
*/
function changeMode() {
    addingToggle = !addingToggle;
    
    if (addingToggle) {
	$('#map').css('cursor', 'crosshair');
	$('#input').html('Cancel');
	console.log('cancel');
    } else {
	$('#input_form').hide();
	$('#map').css('cursor','pointer');
	if (inMarker !== undefined)
	    map.removeLayer(inMarker);
	$('#input').html('Add a marker');
    }		
}

/**
* Fetches the list of features 
* and adds them to the input form
*/ 
function loadFeatures() {
    $('#features').empty();
    $.getJSON("api/api.php?request=feature/list", function(data) {
	for(var i = 0 ; i < data.length ; i++) {
	    var item = $('<option value="' + data[i] + '">' + data[i] + '</option>');
	    item.appendTo("#features");
	}
    });
}


$("document").ready(function() {
    // initializing the map plugin with an arbitrary position in case the user doesn't want to share his position
    map = L.map('map').setView([60.443, 22.275], 13);
    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		{ attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>', maxZoom: 18}).addTo(map);

    //this offers the user to share the positon and places the map accordingly
    map.locate({setView: true, maxZoom: 16});
    //displaying markers in the initial zone
    loadMarkers(map.getBounds());

    // will refresh the markers when the map is moved
    map.on('moveend', function(e) {
	loadMarkers(map.getBounds());
    });

    // toggles map mode (view/add) when clicking the add button
    $("#input").click(function () {
	changeMode();
    });

    map.on('click', function(e) {
	onMapClick(e);
    });

    // adding parking to the database and updating database
    $('#input_submit').click(function(e) {
	e.preventDefault();
	addParking();
	changeMode();
	clearForm();
	console.log("parking added");
    });
    
    $('#input_form').hide();

    $('[data-toggle="tooltip"]').tooltip(); 
});
