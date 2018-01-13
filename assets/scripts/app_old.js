var app = {
	init: function () {
		jQuery('.btn-navbar').on('click', app.menuToggle);
		app.map.requestLocation();
	},
	menuToggle: function () {
		jQuery('.main-nav').toggleClass('open');
	},
	plugins: {
		flexslider: function (el, settings) {
			jQuery(el).flexslider(settings);
		}
	},
	map: {
		requestLocation: function () {
			if (typeof sessionStorage !== "undefined" && typeof sessionStorage.lat !== "undefined" && typeof sessionStorage.lng !== "undefined") {
				var position = {
					coords: {
						latitude: sessionStorage.lat,
						longitude: sessionStorage.lng
					}
				};
				app.map.getCurrentLocation(position);
			} else {
			    if (navigator.geolocation) {
			        navigator.geolocation.getCurrentPosition(app.map.getCurrentLocation);
			    } else {
					app.map.showMap();
			    }
			}
		},
		getCurrentLocation: function (position) {
			if (typeof sessionStorage !== "undefined") {
				sessionStorage.setItem("lat", position.coords.latitude);
				sessionStorage.setItem("lng", position.coords.longitude);
			}
			app.map.showMap(position.coords.latitude, position.coords.longitude, 'footer_map');
		},
		/* 
		 * @array	Locations array
		 */
		locations: [
			['Alta Vista', 45.3876551, -75.6759249, '', '1379 Bank Street<br />K1H 8N3<br /><a href="tel:613-733-5500">613.733.5500</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png'],
			['Orleans', 45.467553, -75.532927, '', '2211 St. Joseph Blvd.<br />K1C 7C5<br /><a href="tel:613-834-0333">613.834.0333</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png'],
			['Barrhaven', 45.274996, -75.721516, '', '3171 Strandherd Drive<br />K2J 5N1<br /><a href="tel:613-825-2844">613.825.2844</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png'],
			['Kanata', 45.296113, -75.890014, '', '484 Hazeldean Road<br />K2L 1V4<br /><a href="tel:613-831-5005">613.831.5005</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png'],
			['Nepean', 45.3446855, -75.7339638, '', '1600 Merivale Road<br />K2G 5J8<br /><a href="tel:613-224-1772">613.224.1772</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png'],
			['Elgin', 45.418714, -75.69126, '', '220 Elgin Street<br />K2P 1L7<br /><a href="tel:613-567-1772">613.567.1772</a><br /><a href="/locations">Read More</a>', '/wp-content/themes/assets/images/pin.png']
		],
		/* 
		 * @var		infowindow
		 */
		infowindow: '',
		/* 
		 * @Func	showMap
		 * @Params	Lat, Lng, TargetID
		 */
		showMap: function (lat, lng, id, index) {
			var myLatlng, myMarker, mapOptions, map, infowindow;

			if (typeof lat !== "undefined" && typeof lng !== "undefined") {
				myLatlng = new google.maps.LatLng(lat, lng);
				if (id === 'widget_map') {
					myMarker = ['Ottawa', lat, lng, '', '', '/wp-content/themes/assets/images/ottawa-pin.png'];
				} else {
					myMarker = ['Your Location', lat, lng, '', '', '/wp-content/themes/assets/images/you.png'];
				}
				app.map.locations.push(myMarker);
			}
			mapOptions = {
				center: myLatlng,
				zoom: 12,
				mapTypeControl: false,
				streetViewControl: false,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			map = new google.maps.Map(document.getElementById(id), mapOptions);

			if (typeof index !== 'undefined') {
				app.map.setMarkers(map, app.map.locations[index]);
			} else {
				app.map.setMarkers(map, app.map.locations);
			}
			app.map.infowindow = new google.maps.InfoWindow({
				content: 'loading...'
			});
		},
		setMarkers: function (map, markers) {
			var i, locations, siteLatLng, marker;
			for (i = 0; i < markers.length; i = i + 1) {
				locations = markers[i];
				siteLatLng = new google.maps.LatLng(locations[1], locations[2]);
				marker = new google.maps.Marker({
					position: siteLatLng,
					map: map,
					title: locations[0],
					icon: locations[5],
					html: '<div style=\'min-height:50px; width:200px;\'><h3>' + locations[0] + '</h3><p>' + locations[4] + '</p></div>'
				});
				// app.map.addListener(map, marker);

				google.maps.event.addListener(marker, 'click', function () {
					app.map.infowindow.setContent(this.html);
					app.map.infowindow.open(map, this);
				});
			}
		},
		addListener: function (map, marker) {
			console.log('here');
			var infowindow;
		}
	}
};