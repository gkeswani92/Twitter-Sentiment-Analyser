<?php	
	$q=$query;
	$last =$_POST['duration'];
	#Retrieving all entries for query
	$con=mysqli_connect("localhost","twit_sentiment","chelsea12","twit_sentiment");
	if (mysqli_connect_errno())
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
	
	if($last=="week")
		#Selects last week's entries from the for the query
		$sql_search="SELECT * FROM `trend_analysis` WHERE query='$query' and date>= date_sub(CURDATE(), INTERVAL 7 DAY) ";
		
		
	if($last=="months3")
		#Selects last 3 months' entries from the for the query
		$sql_search="SELECT * FROM `trend_analysis` WHERE query='$query' and date>= date_sub(CURDATE(), INTERVAL 90 DAY) ";
		
	if($last=="months6")
		#Selects last 6 months' entries from the for the query
		$sql_search="SELECT * FROM `trend_analysis` WHERE query='$query' and date>= date_sub(CURDATE(), INTERVAL 180 DAY) ";
		
	if($last=="year")
		#Selects last year's entries from the for the query
		$sql_search="SELECT * FROM `trend_analysis` WHERE query='$query' and date>= date_sub(CURDATE(), INTERVAL 365 DAY) ";
		
	
	
	$result=mysqli_query($con,$sql_search);
	
	$pos=array();
	$neg=array();
	$date=array();
	$month=array();
	$p_display=array();
	$n_display=array();
	$m_display=array();
	
	#If any such entry exists
	if($result)
	{
		while($row = mysqli_fetch_array($result))
		{
			array_push($pos,$row['positive']);
			array_push($neg,$row['negative']);
			array_push($date,$row['date']);
		}
		//print_r($date);
	
		for($x=0;$x<count($date);$x=$x+1)
		{
			
			$dateValue=$date[$x];
			$formattedValue = date("F Y", strtotime($dateValue));
			array_push($month,$formattedValue);
		}
		
	
		$count=1;
		$total_pos=0;
		$total_neg=0;
		$month[count($date)]="";
		for($x=0;$x<count($date);$x=$x+1)
		{
			if($count==1)
			{
				$total_pos= $total_pos + $pos[$x];
				$total_neg= $total_neg + $neg[$x];
			}
			
			if($month[$x] != $month[$x + 1])
			
			{
			
				$pos_avg = $total_pos/$count;
				$neg_avg = $total_neg/$count;
				array_push($p_display,$pos_avg);
				array_push($n_display,$neg_avg);
				array_push($m_display,$month[$x]);
				$count= 1;
				$total_pos = 0;
				$total_neg = 0;
				
			}
			else 
			{
				$total_pos=$total_pos + $pos[$x + 1];
				$total_neg=$total_neg + $neg[$x + 1];
				$count = $count + 1;	
				
			}
			
			
		}																									
		
	
		
	
	
		
		
		echo "<center> <div id='chart_div_gauge' style='margin-left: 200px; margin-right: 200px;'></div></center><br>";
		echo "<center><div id='chart_div' style='width: 900px; height: 500px;'></div></center><br><br>";
		echo "<centre><div id='chart_div_area' style='width: 900px; height: 500px;'></div></center><br><br>";
		mysqli_close($con);
		if ($last=="week")
		{
			$p_display=$pos;
			$n_display=$neg;
			$m_display=$date;
		}
		
?>
	<!-- Converts PHP array into java script array -->
		<script type="text/javascript">
		var posArray= <?php echo json_encode($p_display); ?>;
		var negArray= <?php echo json_encode($n_display); ?>;
		var dateArray= <?php echo json_encode($m_display); ?>;
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

	
	