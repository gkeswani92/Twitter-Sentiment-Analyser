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
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="css/ie7.css" /><![endif]-->
	</head>
	<!--
		Note: Set the body element's class to "left-sidebar" to position the sidebar on the left.
		Set it to "right-sidebar" to, you guessed it, position it on the right.
	-->
	<body class="left-sidebar">

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Content -->
					<div id="content">
						<div id="content-inner">
					
							<!-- Post -->
								<article class="is-post is-post-excerpt">
									<header>
										<!--
											Note: Titles and bylines will wrap automatically when necessary, so don't worry
											if they get too long. You can also remove the "byline" span entirely if you don't
											need a byline.
										-->
										<h2><a href="#">Twit Sentiment Analyser</a></h2>
										<span class="byline">Discover the Twitter sentiment for your product or brand</span>
									</header>
									
									<!-- <img src="images/sent2.png" alt="" height="275" width="700"/> -->
									<?php
										// Display the input form
										$run = false;
										require 'form.php';

										// If the user clicked the Run button
										// Execute the API request they entered
										if ($run) {
											require 'search.php';
										}
									?>									
								</article>
						</div>
					</div>

				<!-- Sidebar -->
					<div id="sidebar">
					
						<!-- Logo -->
							<div id="logo">
								<h1>Sentiment Analyser</h1>
							</div>
						
						<!-- Nav -->
							<nav id="nav">
								<ul>
									<li class="current_page_item"><a href="#">Home</a></li>
									<li><a href="about.php">About us</a></li>
								</ul>
							</nav>
						
						
						<!-- Copyright -->
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

