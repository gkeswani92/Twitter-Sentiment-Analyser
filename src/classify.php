<?php

#Naive_Bayes function
function naive_bayes($str)
{
	#Variable Declaration
	global $trained_set, $negation, $possmiley, $negsmiley, $adverb, $answers_file;
	$current="";
	$previous="";
	$pre_previous="";
	$prob_pos=0;
	$prob_neg=0;
	
	$words=explode(' ',$str);
	$arrlength=count($words);
	for($x=0;$x<$arrlength;$x++)
	{
		$current=$words[$x];
		#If word is found in the trained classifier
		if (array_key_exists($current, $trained_set))
		{
			$pos=$trained_set[$current];
			$neg=1-$pos;
			
			#If previous word is a negative word
			if(in_array($previous, $negation))
			{
				$temp=$pos;
				$pos=$neg;
				$neg=$temp;
			}
			
			#If previous word is an adverb
			if(array_key_exists($previous,$adverb))
			{
				$pos=$pos*$adverb[$previous];
				$neg=$neg*$adverb[$previous];
				
				#If negation appears before intensifier as pre_previous word
				if(in_array($pre_previous, $negation))
				{
					$temp=$pos;
					$pos=$neg;
					$neg=$temp;
				}
			}
			$prob_pos=$prob_pos+$pos;
			$prob_neg=$prob_neg+$neg; 
		}
		
		#Sentiment due to smiley's
		if(in_array($current,$possmiley))
		{
			$prob_pos=$prob_pos+1;
		}
		if(in_array($current,$negsmiley))
		{
			$prob_neg=$prob_neg+1;
		}
		
		#Moving the previous word pointer forward
		$pre_previous=$previous;
		$previous=$current;	
	}
	if ($prob_pos>$prob_neg and $prob_pos-$prob_neg>0.25)
		fwrite($answers_file,"0,$str");
	else if ($prob_pos<$prob_neg and $prob_neg-$prob_pos>0.25)
		fwrite($answers_file,"4,$str");
	else
		fwrite($answers_file,"2,$str");
}

#Creates the associative array for the training set
$trained_file = fopen("Classifier/Trained Set.txt","r");
$trained_set=array();
while(!feof($trained_file))
{
	$line=fgets($trained_file);
	if ($line != null)
	{
		#Finds position of the comma's
		$com1=strpos($line,',');
		$com2=strpos($line,',',$com1+1);
			
		#Extract the word and the count
		$word=substr($line,0,$com1);
		$pos_count=substr($line,$com1+1,$com2-$com1-1);
		$neg_count=substr($line,$com2+1);
			
		#Calculate the probability of word being positive
		$pos_prob=$pos_count/($pos_count+$neg_count);
		$trained_set[$word]=$pos_prob;
	}
}
fclose($trained_file);
	
	#Creates an array for the negation list
	$negation_file=fopen("Classifier/Negation List.txt","r");
	$negation=array();
	while(!feof($negation_file))
	{
		$line=fgets($negation_file);
		if ($line != null)
			array_push($negation,$line);
	}
	fclose($negation_file);

	#Creates an array for the positive smiley list
	$possmiley_file=fopen("Classifier/Emoticon List Positive.txt","r");
	$possmiley=array();
	while(!feof($possmiley_file))
	{
		$line=fgets($possmiley_file);
		if ($line != null)
			array_push($possmiley,$line);
	}
	fclose($possmiley_file);

	#Creates an array for the negative smiley list
	$negsmiley_file=fopen("Classifier/Emoticon List Negative.txt","r");
	$negsmiley=array();
	while(!feof($negsmiley_file))
	{
		$line=fgets($negsmiley_file);
		if ($line != null)
			array_push($negsmiley,$line);
	}
	fclose($negsmiley_file);

	#Creates an array for the adverbs list
	$adverb_file=fopen("Classifier/Adverb List.txt","r");
	$adverb=array();
	while(!feof($adverb_file))
	{
		$line=fgets($adverb_file);
		if ($line != null)
		{
			$com1=strpos($line,',');
			$word=substr($line,0,$com1);
			$degree=substr($line,$com1+1);
			$adverb[$word]=$degree;
		}
	}
	fclose($adverb_file);

	#Trims the strings from the \n and \r
	$negation = array_map('trim', $negation);
	$possmiley = array_map('trim', $possmiley);
	$negsmiley = array_map('trim', $negsmiley);
	$adverb = array_map('trim', $adverb);

	#Opens the file with tweets to read and answers to write
	$tweets_file = fopen("Classifier/Tweets.txt","r");
	$answers_file= fopen("Classifier/Answers.txt","w");
	while(!feof($tweets_file))
	{
		$line=fgets($tweets_file);
		if($line!="")
			naive_bayes($line);
	}
	fclose($tweets_file);
	fclose($answers_file);

	#Finds the count of the positive and negative tweets
	$answers_file = fopen("Classifier/Answers.txt","r");
	$p=0; $n=0;
	do 
	{
		$line=fgets($answers_file);
		$sentiment=substr($line,0,1);
		if (strcmp($sentiment,"0")==0)
			$p=$p+1;
		elseif (strcmp($sentiment,"4")==0)
			$n=$n+1;
		else 
		{}
	}
	while(!feof($answers_file));
	fclose($answers_file);

	#Calls the JavaScript function for google charts
	echo "<center><div id='piechart_3d' style='width: 500px; height: 275px;'>";
	echo "<script>drawChart(".$p.",".$n.");</script></div></center>";

	#Displays the table of tweets with the appropriate colour
	$tweets_file = fopen("Classifier/Tweets.txt","r");
	$answers_file = fopen("Classifier/Answers.txt","r");
	do{  
		 $line=fgets($answers_file);
		 $sentiment=substr($line,0,1);
		 $tweet=fgets($tweets_file);
		 if (strcmp($tweet,"")!=0)
		 {
			 if (strcmp($sentiment,"0")==0)
				echo "<table class='bordered'><tr><td bgcolor = '#E1F5A9' style='border-color:green'>".$tweet."<br></td></tr></table>";
			 elseif (strcmp($sentiment,"4")==0) 
				echo "<table class='bordered'><tr><td bgcolor = '#F5A9A9' style='border-color:red'>".$tweet."<br></td></tr></table>";
			 else{
				//echo "<td bgcolor='#FFFFFF'>".$tweet."<br></td>";
				}
		 }
	}
	while(!feof($answers_file)) ;
	fclose($tweets_file);
	fclose($answers_file);
	
	
	#Adding the values of $p and $n to the trend analysis table
	$con=mysqli_connect("208.115.198.220","a5e4629f_chelsea","sentimentproject","a5e4629f_twit_sentiment");
	if (mysqli_connect_errno())
		echo "Failed to connect to MySQL: " . mysqli_connect_error();

	$posperc=intval($p*100/($p+$n));
	$negperc=intval($n*100/($p+$n));
	echo "New:".$posperc." ".$negperc."<br>";
	$d=date("Y-m-d");
	
	$sql_search="SELECT * from trend_analysis WHERE query='$query' and date='$d'";
	$result=mysqli_query($con,$sql_search);
	
	#If previous record is found for the same day
	if($result)
	{
		while($row = mysqli_fetch_array($result))
		{
			echo "Old:".$row['positive']." ".$row['negative']."<br>";
			$posperc = intval(($posperc + $row['positive'])/2);
			$negperc = intval(($negperc + $row['negative'])/2);
		}
		$sql_delete="DELETE FROM `trend_analysis` WHERE query='$query' and date='$d'";
		$result_delete=mysqli_query($con,$sql_delete);
		echo "Modified:".$posperc." ".$negperc."<br>";

	}
	
	#Inserts new entry OR updates the current entry
	$sql="INSERT INTO trend_analysis (query, date, positive, negative) VALUES('$query','$d','$posperc','$negperc')";
	$result=mysqli_query($con,$sql);
	if(!$result)
		die('Error: '.mysqli_error($con));
		
	mysqli_close($con);

?>