<!DOCTYPE HTML>

<html>
	<head>
		<title>Twitter Sentiment Analysis</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,700|Open+Sans+Condensed:300,700" rel="stylesheet" />
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		
		<!--Javascript for Google Charts API-->
		<script type="text/javascript">
			  google.load("visualization", "1", {packages:["corechart"]});
			  function drawChart1(pos,neg) {
				var data = google.visualization.arrayToDataTable([
				  ['Sentiment', 'Number of Tweets'],
				  ['Positive',pos],
				  ['Negative',neg]
				]);
				var options = {
				  title:'Sentiment by Percentage',
				  is3D: true,
				  colors: ['#00ff00', '#ff0000']
				};
				var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_1'));
				chart.draw(data, options);
			  }
			  function drawChart2(pos,neg) {
				var data = google.visualization.arrayToDataTable([
				  ['Sentiment', 'Number of Tweets'],
				  ['Positive',pos],
				  ['Negative',neg]
				]);
				var options = {
				  title:'Sentiment by Percentage',
				  is3D: true,
				  colors: ['#00ff00', '#ff0000']
				};
				var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_2'));
				chart.draw(data, options);
			  }
    </script>
	
	<style>
	table, td, th, tr
	{
		border:0px solid white;
	}
	</style>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
	</head>
	<body class="left-sidebar">
			<div id="wrapper">
					<div id="content">
						<div id="content-inner">
								<article class="is-post is-post-excerpt">
									<header>								
										<h2><a href="#">Twit Sentiment Analyser</a></h2>
										<span class="byline">Compare the Twitter sentiment for multiple products</span>
									</header>
									<?php
										// Display the input form
										$run = false;
										require 'form_comparator.php';

										// If the user clicked the Run button
										// Execute the API request they entered
										if ($run) {
											require 'display_comparator.php';
										}
									?>									
								</article>
						</div>
					</div>
					
					<div id="sidebar">
							<div id="logo">
								<h1>Sentiment Analyser</h1>
							</div>
							<nav id="nav">
								<ul>
									<li><a href="index.php">Live Analysis</a></li>
									<li><a href="historical.php">Historical Analysis</a></li>
									<li class="current_page_item"><a href="#">Compare Brands</a></li>
									<li><a href="maps.php">Location Based</a></li>
									<li><a href="about.php">About us</a></li>
								</ul>
							</nav>
							<div id="copyright">
								<p>
									&copy; 2014 Twit Sentiment Analyser.<br />
									<?php
									print "<div style='font-size:12px; margin-bottom: 10px'>Created by: <a href='https://twitter.com/Gaurav_Keswani'>Gaurav Keswani</a>, 
									<a href='https://twitter.com/smitsawant'>Smit Sawant</a>, <a href='https://twitter.com/PriyankaAsrani'>Priyanka Asrani</a> & 
									<a href='https://twitter.com/pratiksh_jain'>Pratiksh Jain</a></div>";
									?>
								</p>
							</div>
					</div>
			</div>
	</body>
</html>
