var locations = [
                
                  [, 43.5132101,13.2302635],  
				  [  , 42.5132101,100.2302635],  
				  [ , 23.5132101,93.2302635],  
				  [  , 15.5132101,35.2302635],
			
				  [  , 13.5132101,123.2302635],  
				  [ , 12.5132101,77.2302635],	    
                
                ];
                // Setup the different icons and shadows
                var iconURLPrefix = 'images';
                var icons = [
                                      	'images/map-icon-2.png', 
										'images/map-icon-2.png', 
										'images/map-icon-2.png', 
										'images/map-icon-2.png', 
										'images/map-icon-2.png', 
										'images/map-icon-2.png', 
                                        
                                    ];

                var icons_length = icons.length;
                var shadow = {
                  anchor: new google.maps.Point(16,16),
                  url: iconURLPrefix + 'msmarker.shadow.png'
                };

                var myOptions = {
                  center: new google.maps.LatLng(16,18),
                  mapTypeId: 'roadmap',
                  mapTypeControl: true,
                  streetViewControl: true,
                  panControl: true,
                  scrollwheel: false,
                  draggable: true,
				  
				  				  
                  styles: [{
                        stylers: [{
                            hue: '#e4f2e6'
                        }, {
                            saturation: -30                        }, {
                            lightness: 10                        }]
                    }],
					
								
                   zoom: 3,
                }
                
                var map = new google.maps.Map(document.getElementById("maping-filtered"), myOptions);
                var infowindow = new google.maps.InfoWindow({
                  maxWidth: 350,
                });
                var marker;
                var markers = new Array();
                var iconCounter = 0;

                // Add the markers and infowindows to the map
                for (var i = 0; i < locations.length; i++) {  
                  marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon : icons[iconCounter],
                    shadow: shadow
                  });

                  markers.push(marker);
                  google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                      infowindow.setContent(locations[i][0]);
                      infowindow.open(map, marker);
                    }
                  })(marker, i));
                  
                  iconCounter++;
                  // We only have a limited number of possible icon colors, so we may have to restart the counter
                  if(iconCounter >= icons_length){
                    iconCounter = 0;
                  }
                }