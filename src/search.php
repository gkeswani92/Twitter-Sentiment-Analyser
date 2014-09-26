<?php
	// Connect to the API with OAuth tokens
	require 'app_tokens.php';  
	require 'tmhOAuth.php';

	//Creating the connection using the 4 keys included in app_tokens.php
	$connection = new tmhOAuth(array(
	  'consumer_key' => $consumer_key,
	  'consumer_secret' => $consumer_secret,
	  'user_token' => $user_token,
	  'user_secret' => $user_secret
	)); 

	//search/tweets API uses GET method and mentioned URL
	$method='GET';
	$url='/1.1/search/tweets';

	$query_mod=$query." -http"." -https"." -RT";

	//result_type can be recent, popular or mixed 
	$array=array("lang"=>"en","count"=>"50","q"=>$query_mod,"result_type"=>"mixed");

	//Creating the connection and storing it in code
	$code = $connection->request($method, $connection->url($url),$array);

	// Display the response HTTP code
	$code = $connection->response['code'];

	// Display the API response as an array
	//print "<strong>Response:</strong><pre style='word-wrap: break-word'>";

	//To decode the complete json file. Print this to see total details
	$objJson=json_decode($connection->response['response'],true);
	
	$data = array();
	$picture = array();
	$name = array();
	$x=0;
	
	//To retrieve only the text information from the JSON file
	foreach($objJson["statuses"] as $status) 
	{
		$data[] = array("text" => $status["text"]);
		$picture[$status["text"]]=$objJson["statuses"][$x]["user"]["profile_image_url"];
		$name[$status["text"]]=$objJson["statuses"][$x]["user"]["name"];
		$x=$x+1;
	}

	//To send the tweets to the tweets.txt file
	$tweets = fopen("Classifier/Tweets.txt","w");
	foreach($data as $x=>$x_value) {
			fwrite($tweets,$x_value['text']);
			fwrite($tweets,"\n");
	}
	fclose($tweets);
	
#Function to pre-process tweets from the tweets file
function pre_process($line)
{	
	$mod_tweet="";
	global $negsmiley, $possmiley;
	
	if ($line!="")
	{
		#Remove special characters at the end of words and leaves out smileys
		$words=explode(" ",$line);
		for($x=0;$x<count($words);$x++)
		{
			$word=$words[$x];
			if (!(in_array($word,$negsmiley) or in_array($word,$possmiley)))
				$word=preg_replace('/(^([^a-zA-Z0-9])*|([^a-zA-Z0-9])*$)/', '', $word);
			$mod_tweet=$mod_tweet." ".$word;
		}
		$tweet=trim($mod_tweet);
		
		#Removes white spaces and trims the tweet
		$tweet=strtolower(trim($tweet));
		
		#Removes the user name
		$tweet=$tweet." ";
		$acount=substr_count($tweet,'@');
		while($acount !=0)
		{
			$apos=strpos($tweet,'@');
			$spos=strpos($tweet,' ',$apos);
			$tweet=substr_replace($tweet,"",$apos,$spos-$apos);
			$acount = $acount - 1;
		}
		$tweet=trim($tweet);
		
		#Remove the hash sign and keep the hash tag
		$hcount=substr_count($tweet,'#');
		while ($hcount != 0)
		{
			$hpos=strpos($tweet,'#');
			$tweet=substr_replace($tweet,'',$hpos,1);
			$hcount = $hcount-1;
		}
		$tweet=trim($tweet);
		
		#Removes words starting with numbers
		$tweet=trim(str_replace(range(0,9),'',$tweet));
		
		#Removes multiple characters occurring together
		$words=explode(" ",$tweet);
		for($x=0;$x<count($words);$x++)
		{
			$current=$words[$x];
			$a=0; $b=1; $c=2;
			while ($c <= strlen($current))
			{
				if(substr($current,$a,1) == substr($current,$b,1) and substr($current,$b,1) == substr($current,$c,1))
				{
					$current=substr_replace($current,'',$c,1);
				}
				else
				{
					$a = $a + 1;
					$b = $b + 1;
					$c = $c + 1;
				}
			}	
			$mod_tweet=$mod_tweet." ".$current;
		}
		$tweet=$mod_tweet;
		$mod_tweet="";
		
		#Removes all characters less than 3 characters
		$words=explode(" ",$tweet);
		for($x=0;$x<count($words);$x++)
		{
			if(strlen($words[$x])>=3)
				$mod_tweet = $mod_tweet." ".$words[$x];
		}
		return $mod_tweet;
	}
}
	
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
			$prob_pos=$prob_pos+0.5;
		}
		if(in_array($current,$negsmiley))
		{
			$prob_neg=$prob_neg+0.5;
		}
		
		#Moving the previous word pointer forward
		$pre_previous=$previous;
		$previous=$current;	
	}
	if ($prob_pos>$prob_neg and $prob_pos-$prob_neg>0.5)
		return 0;
	else if ($prob_pos<$prob_neg and $prob_neg-$prob_pos>0.5)
		return 4;
	else
		return 2;
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
	
	#Variables for sentiment and tweet storage
	$tweet_arr=array();
	$sentiment_arr=array();
	$p=0;$n=0;
	
	#Opens the file with tweets to read
	$original = fopen("Classifier/Tweets.txt","r");
	while(!feof($original))
	{
		$line=fgets($original);
		if($line!="")
		{
			$mod=pre_process($line);
			$sent=naive_bayes($mod);
			if ($sent == 0)
			{
				array_push($tweet_arr,$line);
				array_push($sentiment_arr,0);
				$p=$p+1;
			}
			elseif ($sent == 4)
			{
				array_push($tweet_arr,$line);
				array_push($sentiment_arr,4);
				$n=$n+1;
			}
			else 
			{}
		}
	}
	fclose($original);
	
	#Calls the JavaScript function for google charts
	echo "<center><div id='piechart_3d' style='width: 500px; height: 275px;'>";
	echo "<script>drawChart(".$p.",".$n.");</script></div></center>";
	
	$tweet_arr = array_map('trim', $tweet_arr);
	
	#Displays the tweets in the table with the proper colour for sentiment
	for($x=0;$x<count($tweet_arr);$x=$x+1)
	{
		if(array_key_exists($tweet_arr[$x],$picture))
		{
			$link=$picture[$tweet_arr[$x]];
			if(intval($sentiment_arr[$x])==0)
			{
				echo "<table class='bordered' style='table-layout: fixed;'><tr bgcolor = '#E1F5A9' style='border-color:green'>";
				echo "<td style='width:75px; height=75px;'><img src='$link' style='border-radius: 25px 25px 25px 25px'></td>";
				echo "<td style='vertical-align:middle'><b>".$name[$tweet_arr[$x]].":</b> ".$tweet_arr[$x]."<br></td></tr></table>";
			}
			else
			{
				echo "<table class='bordered' style='table-layout: fixed;'><tr bgcolor = '#F5A9A9' style='border-color:red'>";
				echo "<td style='width:75px; height=75px;'><img src='$link' style='border-radius: 25px 25px 25px 25px'></td>";
				echo "<td style='vertical-align:middle'><b>".$name[$tweet_arr[$x]].":</b> ".$tweet_arr[$x]."<br></td></tr></table>";
			}
		}
	}
	
	#Adding the values of $p and $n to the trend analysis table
	$con=mysqli_connect("208.115.198.220","a5e4629f_chelsea","sentimentproject","a5e4629f_twit_sentiment");
	if (mysqli_connect_errno())
		echo "Failed to connect to MySQL: " . mysqli_connect_error();

	$posperc=intval($p*100/($p+$n));
	$negperc=intval($n*100/($p+$n));
	$d=date("Y-m-d");
	
	$sql_search="SELECT * from trend_analysis WHERE query='$query' and date='$d'";
	$result=mysqli_query($con,$sql_search);
	
	#If previous record is found for the same day
	if($result)
	{
		while($row = mysqli_fetch_array($result))
		{
			$posperc = intval(($posperc + $row['positive'])/2);
			$negperc = intval(($negperc + $row['negative'])/2);
		}
		$sql_delete="DELETE FROM `trend_analysis` WHERE query='$query' and date='$d'";
		$result_delete=mysqli_query($con,$sql_delete);
	}
	
	#Inserts new entry OR updates the current entry
	$sql="INSERT INTO trend_analysis (query, date, positive, negative) VALUES('$query','$d','$posperc','$negperc')";
	$result=mysqli_query($con,$sql);
	if(!$result)
		die('Error: '.mysqli_error($con));
		
	mysqli_close($con);
	
	#print_r($picture);
?>