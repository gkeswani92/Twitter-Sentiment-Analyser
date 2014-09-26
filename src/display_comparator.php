<?php
	
	#Pre-processing function
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
			return 0;
		else if ($prob_pos<$prob_neg and $prob_neg-$prob_pos>0.25)
			return 4;
		else
			return 2;
	}
	
	#Function to get data, classify and print the chart
	function get_data_classify($query)
	{
		global $consumer_key,$consumer_secret,$user_token,$user_secret;

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

		//To decode the complete json file. Print this to see total details
		$objJson=json_decode($connection->response['response'],true);
		
		$data = array();
		
		//To retrieve only the text information from the JSON file
		foreach($objJson["statuses"] as $status) 
			$data[] = array("text" => $status["text"]);

		//To send the tweets to the tweets.txt file
		$tweets = fopen("Classifier/Tweets_Comparator.txt","w");
		foreach($data as $x=>$x_value) {
				fwrite($tweets,$x_value['text']);
				fwrite($tweets,"\n");
		}
		fclose($tweets);
	
		global 	$p,$n;
		global $result;
		
		#Opens the file with tweets to read
		$original = fopen("Classifier/Tweets_Comparator.txt","r");
		while(!feof($original))
		{
			$line=fgets($original);
			if($line!="")
			{
				$mod=pre_process($line);
				$sent=naive_bayes($mod);
				if ($sent == 0)
					$p=$p+1;
				elseif ($sent == 4)
					$n=$n+1;
				else 
				{}
			}
		}
		fclose($original);
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

// Connect to the API with OAuth tokens
require 'app_tokens.php';  
require 'tmhOAuth.php';

$result = array();

#Classifying and printing chart for query2
$p=0;
$n=0;
get_data_classify($query1);
$result[0]=$p;
$result[1]=$n;

#Classifying and printing chart for query2
$p=0;
$n=0;
get_data_classify($query2);
$result[2]=$p;
$result[3]=$n;

$p1=intval($result[0])/(intval($result[0])+intval($result[1]));
$p2=intval($result[2])/(intval($result[2])+intval($result[3]));

echo "<center>";
#Calls the JavaScript function for google charts
echo "<table>";
echo "<th colspan='2'>";
if($p1>$p2)
	echo "<b>Twitter says $query1 is better than $query2</b>";
else if($p2>$p1)
	echo "<b>Twitter says $query2 is better than $query1</b>";
else
	echo "<b>Twitter says $query1 is as good as than the $query2</b>";
echo "</th>";

echo "<tr><td>";
echo "<center><u>Sentiment for <b>$query1</b></u></td></center><td><center><u>Sentiment for <b>$query2</b><u></td></tr><tr></center><td>";
echo "<center><div id='piechart_3d_1' style='width: 500px; height: 275px;'>";
echo "<script>drawChart1(".$result[0].",".$result[1].");</script></div></center></td>";

#Calls the JavaScript function for google charts
echo "<td><center><div id='piechart_3d_2' style='width: 500px; height: 275px;'>";
echo "<script>drawChart2(".$result[2].",".$result[3].");</script></div></center>";
echo "</td></tr></table>";
echo "</center>";

?>
