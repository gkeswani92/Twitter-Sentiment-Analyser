<?php	
	$q=$query;
	
	#Retrieving all entries for query
	$con=mysqli_connect("208.115.198.220","a5e4629f_chelsea","sentimentproject","a5e4629f_twit_sentiment");
	if (mysqli_connect_errno())
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
	#Selects the last 10 entries from the table for the query
	$sql_search="SELECT * from trend_analysis WHERE query='$query' LIMIT 10";
	$result=mysqli_query($con,$sql_search);
	
	$pos=array();
	$neg=array();
	$date=array();
	
	#If any such entry exists
	if($result)
	{
		while($row = mysqli_fetch_array($result))
		{
			array_push($pos,$row['positive']);
			array_push($neg,$row['negative']);
			array_push($date,$row['date']);
		}
		echo "<center> <div id='chart_div_gauge' style='margin-left: 200px; margin-right: 200px;'></div></center><br>";
		echo "<center><div id='chart_div' style='width: 900px; height: 500px;'></div></center><br><br>";
		echo "<centre><div id='chart_div_area' style='width: 900px; height: 500px;'></div></center><br><br>";
		mysqli_close($con);
?>
	
		<!-- Converts PHP array into java script array -->
		<script type="text/javascript">
		var posArray= <?php echo json_encode($pos); ?>;
		var negArray= <?php echo json_encode($neg); ?>;
		var dateArray= <?php echo json_encode($date); ?>;
		drawChart(posArray,negArray,dateArray);
		drawChart_gauge(posArray,negArray);
		</script>
<?php
	} 
	else
	{
		echo "Sorry! No historical data found for query";
	}
?>
	
	
	
	