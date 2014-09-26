<?php
#Function to pre-process tweets from the tweets file
function pre_process($line)
{	
	$mod_tweet="";
	global $negsmiley, $possmiley;
	
	if ($line!="")
	{	
		#Remove special characters at the end of words
		$words=explode(" ",$line);
		for($x=0;$x<count($words);$x++)
		{
			$word=$words[$x];
			echo $word."<br>";
			if (!(in_array($word,$negsmiley) or in_array($word,$possmiley)))
			{
				$word=preg_replace('/(^([^a-zA-Z0-9])*|([^a-zA-Z0-9])*$)/', '', $word);
			}
			else
				echo "Smiley detected<br>";
			$mod_tweet=$mod_tweet." ".$word;
			
		}
		$tweet=trim($mod_tweet);
		
		#Removes white spaces and trims the tweet
		$tweet=strtolower(trim($tweet));
		
		return $tweet;
	}
}

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

	
	$negsmiley = array_map('trim', $negsmiley);
	$possmiley = array_map('trim', $possmiley);
	
	$str=pre_process("Gaurav likes the iphone! :) :D :P ");
	echo $str;
	
	
	
?>