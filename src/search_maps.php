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

	$query_mod=$query;
	//echo "QUERY: ", $query_mod;
	//print "<br/><br/>";

	//lang= language of tweets to be displayed
	//count= number of tweets needed
	//query= keyword being searched
	//result_type can be recent, popular or mixed 
	$array=array("lang"=>"en","count"=>"200","q"=>$query_mod,"result_type"=>"recent");

	//Creating the connection and storing it in code
	$code = $connection->request($method, $connection->url($url),$array);

	// Display the response HTTP code
	$code = $connection->response['code'];
	#print "<strong>Code:</strong> $code<br/>";

	// Display the API response as an array
	#print "<strong>Response:</strong><pre style='word-wrap: break-word'>";

	//To print all details
	#print_r(json_decode($connection->response['response'],true));

	//To decode the complete json file
	$objJson=json_decode($connection->response['response'],true);

	$data = array();
	$location = array();
	$x=0;
	
	//To retrieve only the text information from the JSON file
	foreach($objJson["statuses"] as $status) 
	{
		if($objJson["statuses"][$x]["user"]["location"] != "")
		{
			$data[] = array("text" => $status["text"]);
			$location[$status["text"]] = $objJson["statuses"][$x]["user"]["location"];
		}
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
	global $logic;
	
	if ($line!="")
	{
		$line=preg_replace("@\n@","",$line);
		$tweet=$line." ";
		
		#Remove special characters at the end of words and leaves out smileys
		$words=explode(" ",$tweet);
		$words = array_map('trim', $words);
		for($x=0;$x<count($words);$x++)
		{
			$word=$words[$x];
			if (!(in_array($word,$negsmiley) or in_array($word,$possmiley)))
				$word=preg_replace('/(^([^a-zA-Z0-9])*|([^a-zA-Z0-9])*$)/', '', $word);
			$mod_tweet=$mod_tweet." ".$word;
		}
		fwrite($logic,"No Special Characters: $mod_tweet \n");
		$tweet=trim($mod_tweet);
		
		#Removes white spaces and trims the tweet
		$tweet=strtolower($tweet);
		fwrite($logic,"Lower case: $tweet \n");
		
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
		fwrite($logic,"No User name: $tweet \n");
		$tweet=trim($tweet);
		
		#Remove the hash sign and keep the hash tag
		$hcount=substr_count($tweet,'#');
		while ($hcount != 0)
		{
			$hpos=strpos($tweet,'#');
			$tweet=substr_replace($tweet,'',$hpos,1);
			$hcount = $hcount-1;
		}
		fwrite($logic,"Remove hash tag: $tweet \n");
		$tweet=trim($tweet);
		
		#Removes words starting with numbers
		$tweet=trim(str_replace(range(0,9),'',$tweet));
		fwrite($logic,"No words starting with numbers: $tweet \n");
		
		#Removes multiple characters occurring together
		$mod_tweet="";
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
		fwrite($logic,"No multiple characters: $tweet \n");
		
		#Removes all characters less than 3 characters
		$words=explode(" ",$tweet);
		for($x=0;$x<count($words);$x++)
		{
			if(strlen($words[$x])>=2)
				$mod_tweet = $mod_tweet." ".$words[$x];
		}
		fwrite($logic,"Only more than 3 characters: $mod_tweet \n");
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
	global $logic;
	$psmiley = array_map('strtolower', $possmiley);
	$nsmiley = array_map('strtolower', $negsmiley);
	
	
	fwrite($logic,"Pre-Processed: $str \n");
	
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
			fwrite($logic,"Word found in trained set: $current \n");
			fwrite($logic,"P(Positive): $pos \n");
			fwrite($logic,"P(Negative): $neg \n");
			
			#If previous word is a negative word
			if(in_array($previous, $negation))
			{
				$temp=$pos;
				$pos=$neg;
				$neg=$temp;
				fwrite($logic,"Negation detected: $previous \n");
				fwrite($logic,"P(Positive|Negation): $pos \n");
				fwrite($logic,"P(Negative|Negation): $neg \n");
			}
			
			#If previous word is an adverb
			if(array_key_exists($previous,$adverb))
			{
				$pos=$pos*$adverb[$previous];
				$neg=$neg*$adverb[$previous];
				fwrite($logic,"Adverb found in trained set: $previous \n");
				fwrite($logic,"P(Positive|Adverb): $pos \n");
				fwrite($logic,"P(Negative|Adverb): $neg \n");
				
				#If negation appears before intensifier as pre_previous word
				if(in_array($pre_previous, $negation))
				{
					$temp=$pos;
					$pos=$neg;
					$neg=$temp;
					fwrite($logic,"Negation detected before adverb: $pre_previous \n");
					fwrite($logic,"P(Positive|Adverb-Negation): $pos \n");
					fwrite($logic,"P(Negative|Adverb-Negation): $neg \n");
				}
			}
			$prob_pos=$prob_pos+$pos;
			$prob_neg=$prob_neg+$neg; 
			fwrite($logic,"P(Positive|Total): $prob_pos \n");
			fwrite($logic,"P(Negative|Total): $prob_neg \n");
		}
		
		#Sentiment due to smiley's
		if(in_array($current,$psmiley))
		{
			$prob_pos=$prob_pos+0.75;
			fwrite($logic,"Positive smiley Detected: $current \n");
			fwrite($logic,"P(Positive|Smiley): $prob_pos \n");
			fwrite($logic,"P(Negative): $prob_neg \n");
		}
		if(in_array($current,$nsmiley))
		{
			$prob_neg=$prob_neg+0.75;
			fwrite($logic,"Negative smiley Detected: $current \n");
			fwrite($logic,"P(Positive): $prob_pos \n");
			fwrite($logic,"P(Negative|Smiley): $prob_neg \n");
		}
		#Moving the previous word pointer forward
		$pre_previous=$previous;
		$previous=$current;	
	}
	if ($prob_pos>$prob_neg and $prob_pos-$prob_neg>0.3)
	{
		fwrite($logic,"Sentence is Positive \n");
		fwrite($logic,"----------------------------------------------------------------\n");
		return 0;
	}
	else if ($prob_pos<$prob_neg and $prob_neg-$prob_pos>0.3)
	{
		fwrite($logic,"Sentence is Negative \n");
		fwrite($logic,"----------------------------------------------------------------\n");
		return 4;
	}
	else
	{
		fwrite($logic,"Sentence is Neutral \n");
		fwrite($logic,"----------------------------------------------------------------\n");
		return 2;
	}
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
	$loc=array();
	$p=0;$n=0;
	
	#To store the logic being used for classification
	$logic = fopen("Classifier/Logic.txt","w");
	
	#Opens the file with tweets to read
	$original = fopen("Classifier/Tweets.txt","r");
	
	$x=0;
	while(!feof($original))
	{
		$line=fgets($original);
		fwrite($logic,"Tweet: $line");
		if($line!="")
		{
			$mod=pre_process($line);
			$sent=naive_bayes($mod);
			if ($sent == 0)
			{
				if(array_key_exists(trim($line),$location))
				{
					array_push($tweet_arr,$line);
					array_push($sentiment_arr,0);
					array_push($loc,$location[trim($line)]);
					$p=$p+1;
				}
			}
			elseif ($sent == 4)
			{
				if(array_key_exists(trim($line),$location))
				{
					array_push($tweet_arr,$line);
					array_push($sentiment_arr,4);
					array_push($loc,$location[trim($line)]);
					$n=$n+1;
				}
			}
			else 
			{}
		}
	}
	fclose($original);
	//print_r($sentiment_arr);
	//print_r($loc);
	
	$_SESSION['place'] = json_encode($loc);
	$_SESSION['sent'] = json_encode($sentiment_arr);
?>
	<script type="text/javascript">	
	window.open("google.php");
	</script>
<?php
	
?>