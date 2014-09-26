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
			  function drawChart(pos,neg) {
				var data = google.visualization.arrayToDataTable([
				  ['Sentiment', 'Number of Tweets'],
				  ['Positive',pos],
				  ['Negative',neg]
				]);
				var options = {
				  title: 'Sentiment by Percentage',
				  is3D: true,
				  colors: ['#00ff00', '#ff0000']
				};
				var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
				chart.draw(data, options);
			  }
    </script>
	
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		<style>
			table {
			*border-collapse: collapse; /* IE7 and lower */
			border-spacing: 0;
			width: 100%;    
			}
			.bordered {
				border: solid #ccc 1px;
				-moz-border-radius: 6px;
				-webkit-border-radius: 6px;
				border-radius: 6px;
				-webkit-box-shadow: 0 1px 1px #ccc; 
				-moz-box-shadow: 0 1px 1px #ccc; 
				box-shadow: 0 1px 1px #ccc;         
			}
			.bordered tr:hover {
				background: #fbf8e9;
				-o-transition: all 0.1s ease-in-out;
				-webkit-transition: all 0.1s ease-in-out;
				-moz-transition: all 0.1s ease-in-out;
				-ms-transition: all 0.1s ease-in-out;
				transition: all 0.1s ease-in-out;     
			}    				
			.bordered td, .bordered th {
				border-left: 1px solid #ccc;
				border-top: 1px solid #ccc;
				padding: 10px;
				text-align: left;    
			}
			.bordered td:first-child, .bordered th:first-child {
				border-left: none;
			}
			.bordered tr:last-child td:first-child {
				-moz-border-radius: 0 0 0 6px;
				-webkit-border-radius: 0 0 0 6px;
				border-radius: 0 0 0 6px;
			}
			.bordered tr:last-child td:last-child {
				-moz-border-radius: 0 0 6px 0;
				-webkit-border-radius: 0 0 6px 0;
				border-radius: 0 0 6px 0;
			}
		</style>
	</head>
	<body class="left-sidebar">
			<div id="wrapper">
					<div id="content">
						<div id="content-inner">
								<article class="is-post is-post-excerpt">
									<header>								
										<h2><a href="#">Twit Sentiment Analyser</a></h2>
										<span class="byline">Discover the Twitter sentiment for your product or brand</span>
									</header>
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
					
					<div id="sidebar">
							<div id="logo">
								<h1>Sentiment Analyser</h1>
							</div>
							<nav id="nav">
								<ul>
									<li class="current_page_item"><a href="#">Home</a></li>
									<li><a href="historical.php">Trend Analysis</a></li>
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

