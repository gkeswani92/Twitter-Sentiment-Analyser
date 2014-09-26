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
	<script type="text/javascript">
      google.load("visualization", "1", {packages:["gauge","corechart"]});
	  
	  <!--Java script for drawing bar chart-->
      function drawChart(pos,neg,date) {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
        data.addColumn('number', 'Positive');
        data.addColumn('number', 'Negative');
		data.addRows(pos.length);
		
		var j=0;
		for (i=0;i<pos.length;i++)
		{
			data.setValue(i,j,date[i]);
			j++;
			data.setValue(i,j,parseInt(pos[i]));
			j++;
			data.setValue(i,j,parseInt(neg[i]));
			j=0;
		}
		
		<!-- Options and chart for bar chart -->
        var options = {
          title: 'Sentiment Trend',
          vAxis: {title: 'Date',  titleTextStyle: {color: 'black'}},
		  colors: ['#E1F5A9','#F5A9A9'],
		  isStacked: true,
        };
		
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
		
		<!-- Option and chart for area chart-->
		var options2 = {
          title: 'Sentiment Trend',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0},
		  colors: ['green','#F5A9A9']
        };
		
		var chart = new google.visualization.AreaChart(document.getElementById('chart_div_area'));
        chart.draw(data, options2);
      }
	  
	  <!--Java script for drawing gauge-->
	  function drawChart_gauge(pos_g,neg_g) {

		var p=0;
		var n=0;
		
		<!--Calculates overall positive and negative sentiment -->
		for (i=0;i<pos_g.length;i++)
		{
			p = p + parseInt(pos_g[i]);
			n = n + parseInt(neg_g[i]);
		}
		p = parseInt(p / pos_g.length);
		n = parseInt(n / neg_g.length);	
		
		var data2 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Positive', p],
          ['Negative', n]
        ]);
		
        var options2 = {
          width: 400, height: 320,
          redFrom: 90, redTo: 100,
          yellowFrom:75, yellowTo: 90,
          minorTicks: 5
        };
        var chart2 = new google.visualization.Gauge(document.getElementById('chart_div_gauge'));
        chart2.draw(data2, options2);
		
      }
    </script>
	
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
										<span class="byline">Perform trend analysis on a product of your choice</span>
									</header>
									<?php
										// Display the input form
										$run = false;
										require 'form_trend.php';

										// If the user clicked the Run button
										// Execute the API request they entered
										if ($run) {
											require 'search_historical.php';
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
									<li><a href="index.php">Home</a></li>
									<li class="current_page_item"><a href="#">Trend Analysis</a></li>
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