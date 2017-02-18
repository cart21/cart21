 

$(document).ready(function(){
// Test that changing between layers with differing zoomlevels also updates
        // the zoomlevels in the map + also

		var map = L.map('map',{scrollWheelZoom: false}).setView(L.latLng(41.83, -24.70), 3);

		 L.tileLayer('http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
            attribution: "Map: Tiles Courtesy of MapQuest (OpenStreetMap, CC-BY-SA)",
            subdomains: ["otile1","otile2","otile3","otile4"],
            maxZoom: 12,
            minZoom: 1
        }).addTo(map);
			
		 map.on('click', function(e) {
			    alert(e.latlng);
			});

		L.control.scale().addTo(map);

		for (var i = 0; i < 100; i++) {
			L.marker(getRandomLatLng(map)).addTo(map);
		}

		//////////////////////////////////////////////////////////
		
		
		///// image overlay ////
		
		
		
		var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		osm = L.tileLayer(osmUrl, {maxZoom: 18, attribution: osmAttrib});
		latlng = new L.LatLng(50.5, 30.51);

		var map_image = new L.Map('image_overlay',{scrollWheelZoom: false});
		map_image.addLayer(osm);
	
		var	bounds = new L.LatLngBounds(
	  		new L.LatLng(40.71222,-74.22655),
	  		new L.LatLng(40.77394,-74.12544));
	
		map_image.fitBounds(bounds);
	
		var overlay = new L.ImageOverlay("https://www.lib.utexas.edu/maps/historical/newark_nj_1922.jpg", bounds, {
			opacity: 0.5
		});
		map_image.addLayer(overlay);

		
		
		//////////////////////////////////////////////////////////
		
		
		
		
		
		
		
		/// vector ///
		var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		osm = L.tileLayer(osmUrl, {maxZoom: 18, attribution: osmAttrib});
		latlng = new L.LatLng(50.5, 30.51);
		
	var map_vector = new L.Map('vector_map', {layers: [osm],scrollWheelZoom: false});

	map_vector.addLayer(L.marker(route[0]));
	map_vector.addLayer(L.marker(route[route.length - 1]));

	var canvas = L.canvas();

	var poly = L.polyline([
		[[60, 30], [60, 50], [40, 50]],
		[[20, 50], [20, 70], [0, 70]]
	], {color: 'red'}).addTo(map_vector).bindPopup('Hello SVG');

	var path = L.polygon([
		[route, [[50.5, 30.5], [50.5, 40], [40, 40]]],
		[[[20, 0], [20, 40], [0, 40]]]
	], {renderer: canvas}).addTo(map_vector).bindPopup('Hello Canvas');

	var circle = L.circle([35, 0], 700000, {color: 'green', renderer: canvas}).addTo(map_vector).bindPopup('Hello Circle');
	var circleMarker = L.circleMarker([35, 30], {color: 'magenta', radius: 30}).addTo(map_vector).bindPopup('Happy New Year!');

	// map.on('mousemove', function (e) {
	// 	circle.setLatLng(e.latlng);
	// });

	map_vector.setView([36, 52], 3);

	var layersControl = new L.Control.Layers({
	}, {
		'poly': poly,
		'path': path,
		'circle': circle,
		'circleMarker': circleMarker,
		'canvas': canvas,
		'svg': map._renderer,
	}, {collapsed: false});
		
		/// vector ///
		
		
		
		

});

