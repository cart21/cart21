 

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

		var map = new L.Map('image_overlay');
		map.addLayer(osm);
	
		var	bounds = new L.LatLngBounds(
	  		new L.LatLng(40.71222,-74.22655),
	  		new L.LatLng(40.77394,-74.12544));
	
		map.fitBounds(bounds);
	
		var overlay = new L.ImageOverlay("https://www.lib.utexas.edu/maps/historical/newark_nj_1922.jpg", bounds, {
			opacity: 0.5
		});
		map.addLayer(overlay);

		
		
		//////////////////////////////////////////////////////////
		
		
		
		
		/// heat ///
		
		var map = L.map('heat_map').setView([-37.87, 175.475], 12);

		var tiles = L.tileLayer('http://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
		    attribution: '<a href="https://www.mapbox.com/about/maps/">Terms and Feedback</a>',
		    id: 'examples.map-20v6611k'
		}).addTo(map);

		var heat = L.heatLayer(addressPoints).addTo(map);
		
		/// heat ///
		
		
		

});

