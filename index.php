<?php
	$host = "localhost";
	$username = "root";
	$password = "";
	//$db = "gmaps"; 
	$db = "googlemaps"; 
	
	$con = mysqli_connect($host, $username, $password, $db);
	
	if(mysqli_connect_error()) die("Connection Failure");
	else {
		$markerArray = array();
		$mallArray = array();
		
		$query = "SELECT lat,lng FROM markers";
		$result = mysqli_query($con, $query);

		$index = 0;
		while($row = mysqli_fetch_assoc($result)){
			$markerArray[$index] = $row;
			$index++;
		}		
		
		$query = "SELECT lat,lng FROM markers WHERE type='Mall'";
		$result = mysqli_query($con, $query);
		
		$index = 0;
		while($row = mysqli_fetch_assoc($result)){
			$mallArray[$index] = $row;
			$index++;
		}		
		
		mysqli_close($con);
		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<script src="http://maps.googleapis.com/maps/api/js?key=API_KEY"></script> <!-- API Key removed. -->
	
	<script>
		var latlngMarkerArray = <?php echo json_encode($markerArray); ?>;
		var latlngMallArray = <?php echo json_encode($mallArray); ?>;
		
		function initialize() {
			var mapProp = {
				center: {lat:14.167870, lng:121.243820},
				zoom:30,
				mapTypeId:google.maps.MapTypeId.ROADMAP
			};
			var map=new google.maps.Map(document.getElementById("googleMap"), mapProp);
			
			//Adds marker for UPLB. Not needed, but muh UPLB.
			var myUPLB = new google.maps.LatLng(14.167525, 121.243368);	
			var upMarker=new google.maps.Marker({
				position:myUPLB,
			});
			upMarker.setMap(map);
			
			//Displays points in the database.
			for (var key in latlngMarkerArray) {
				let val = latlngMarkerArray[key];

				var myLatLng = {lat: parseFloat(val.lat), lng: parseFloat(val.lng)};
				var marker=new google.maps.Marker({
					position: myLatLng,
					map: map,
					icon: getPinIcon(),
					shadow: getPinShadow()
				});
				marker.setMap(map);				
			}
			
			//Displays a line that connects the malls.
			var mallLine = new google.maps.Polyline({
				map: map,
				path: getPath(latlngMallArray),
				geodesic: true,
				strokeColor: '#FF0000',
				strokeOpacity: 0.7,
				strokeWeight: 2
			});
			mallLine.setMap(map);

			//Displays a circle of radius 250 and SM Calamba as the center.
			var smCalamba = new google.maps.LatLng(14.202888, 121.155655);
			var smCalambaCircle = new google.maps.Circle({
				strokeColor: '#0000FF',
				strokeOpacity: 0.7,
				strokeWeight: 2,
				fillColor: '#0000FF',
				fillOpacity: 0.30,
				map: map,
				center: smCalamba,
				radius: 250
			});
			smCalambaCircle.setMap(map);

		}
		
		//Helper function. Generates a randomly colored marker pin.
		function getPinIcon () {
			//One line hex color generator: http://www.paulirish.com/2009/random-hex-color-code-snippets/
			var pinColor = Math.floor(Math.random()*16777215).toString(16); 

			var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor, 
							new google.maps.Size(21, 34),
							new google.maps.Point(0,0),
							new google.maps.Point(10, 34));

			return pinImage;
		}

		//Helper function. Generates a shadow for a marker pin.		
		function getPinShadow () {
			var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
						new google.maps.Size(40, 37),
						new google.maps.Point(0, 0),
						new google.maps.Point(12, 35));

			return pinShadow;
		}
		
		//Helper function. Generates an array containing the coordinates of the malls. For path-making.
		function getPath(latlngMallArray) {
			var path = [];
			
			for (var key in latlngMallArray) {
				let val = latlngMallArray[key];
				
				var myLatLng = new google.maps.LatLng(parseFloat(val.lat), parseFloat(val.lng));
				path.push(myLatLng);
			}

			return path;
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
		
	</script>

</head>

<body>
	<!--div id="header"> 
		<h1>MUH HEADER</h1> 
	</div-->

	<div id="googleMap" style="width:1500px;height:1500px;"></div>

	<!--div id="footer"> 
		<h1> MUH FOOTER</h1> 
	</div-->
	
</body>
</html>
