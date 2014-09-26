<!DOCTYPE HTML>

<html>
	<head>
		<title>Twitter Sentiment Analysis</title>
		<style>
		  html, body, #map-canvas {
			height: 100%;
			margin: 0px;
			padding: 0px
		  }
		</style>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script>
			var pins = [];
			var markers = [];
			var map;
			var berlin = new google.maps.LatLng(52.520816, 13.410186);
			function initialize(lat,longi,sent,tweet) 
			{
				var mapOptions = { zoom: 3, center: berlin };
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				for (var i = 0; i < lat.length; i++) 		
					var j= pins.push(new google.maps.LatLng(lat[i],longi[i]));
				drop(sent,tweet);
			}
			function drop(sent,tweet)
			{
				for (var i = 0; i < pins.length; i++) 
				{
					if (sent[i] == 0)
						markers.push(new google.maps.Marker({position: pins[i],map: map,icon:'http://maps.google.com/mapfiles/ms/icons/green-dot.png',title:tweet[i],}));
					else
						markers.push(new google.maps.Marker({position: pins[i],map: map,title:tweet[i],}));
				}
			}
		</script>
	</head>
	<body>
		<?php
		session_start();
		print "<div id='map-canvas'></div>";
		$place=json_decode($_SESSION['place']);
		$sent=json_decode($_SESSION['sent']);
		$message=json_decode($_SESSION['message']);
	
		$lat_arr = array();
		$lng_arr = array();
		$sent_arr = array();
		$tweet_arr = array();
		
		
		for ($x=0; $x<count($place); $x++)
		{
			$address=$place[$x];
			$address = str_replace(" ", "+", $address);
			$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
			$json = json_decode($json);
			if(isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}))
			{
				$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
				$lng = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
				array_push($lat_arr,$lat);
				array_push($lng_arr,$lng);
				array_push($sent_arr,$sent[$x]);
				array_push($tweet_arr,$message[$x]);
			}
		}
		?>
	</body>
	<script>
		var lat= <?php echo json_encode($lat_arr); ?>;
		var lng= <?php echo json_encode($lng_arr); ?>;
		var sent= <?php echo json_encode($sent_arr); ?>;
		var twt= <?php echo json_encode($tweet_arr); ?>;
		initialize(lat,lng,sent,twt);
	</script>
</html>