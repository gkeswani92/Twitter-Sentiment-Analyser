<!DOCTYPE HTML>

<html>
	<head>
		<title>Twitter Sentiment Analysis</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,700|Open+Sans+Condensed:300,700" rel="stylesheet" />
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		</style>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	</head>
	<body class="left-sidebar">
			<div id="wrapper">
					<div id="content">
						<div id="content-inner">
								<article class="is-post is-post-excerpt">
									<header>								
										<h2><a href="#">Twit Sentiment Analyser</a></h2>
										<span class="byline">View the sentiment for a product on the world map</span>
									</header>
									<?php
										// Display the input form
										session_start();
										
										$run = false;
										require 'form_maps.php';

										// If the user clicked the Run button
										// Execute the API request they entered
										if ($run) {
											require 'search_maps.php';
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
									<li><a href="comparator.php">Compare Brands</a></li>
									<li class="current_page_item"><a href="#">Location Based</a></li>
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