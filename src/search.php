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
	$array=array("lang"=>"en","count"=>"200","q"=>$query_mod,"result_type"=>"mixed");

	//Creating the connection and storing it in code
	$code = $connection->request($method, $connection->url($url),$array);

	// Display the response HTTP code
	$code = $connection->response['code'];

	// Display the API response as an array
	//print "<strong>Response:</strong><pre style='word-wrap: break-word'>";

	//To decode the complete json file. Print this to see total details
	$objJson=json_decode($connection->response['response'],true);
	
	$data = array();
	//To retrieve only the text information from the JSON file
	foreach($objJson["statuses"] as $status) {
		$data[] = array(
			"text" => $status["text"]
		);
	}

	//To send the tweets to the tweets.txt file
	$tweets = fopen("C:\\xampp\\htdocs\\Twitter_Sentiment_Analyser\\Tweets.txt","w");
	foreach($data as $x=>$x_value) {
			//echo $i, ". ", $x_value['text'];
			fwrite($tweets,$x_value['text']);
			fwrite($tweets,"\n");
	}
	fclose($tweets);

	system("C:\\Python27\\python.exe C:\\xampp\\cgi-bin\\Processing.py");
	system("C:\\Python27\\python.exe C:\\xampp\\cgi-bin\\Bayes.py");
	$answers = fopen("C:\\xampp\\htdocs\\Twitter_Sentiment_Analyser\\Answers.txt","r");
	$tweets = fopen("C:\\xampp\\htdocs\\Twitter_Sentiment_Analyser\\Tweets.txt","r");

	//Finds the count of the positive and negative tweets
	$p=0;
	$n=0;
	do 
	{
		$line=fgets($answers);
		$sentiment=substr($line,0,1);
		if (strcmp($sentiment,"0")==0) {
				$p=$p+1;}
			 elseif (strcmp($sentiment,"4")==0) { 
				$n=$n+1;}
			 else {}
	}while(!feof($answers)) ;
	fclose($answers);

	//Calls the javascript function for google charts
	echo "<center><div id='piechart_3d' style='width: 500px; height: 275px;'>";
	echo "<script>drawChart(".$p.",".$n.");</script></div></center>";

	//Displays the table of tweets with the appropriate colour
	$answers = fopen("C:\\xampp\\htdocs\\Twitter_Sentiment_Analyser\\Answers.txt","r");
	do{  
		 $line=fgets($answers);
		 $sentiment=substr($line,0,1);
		 $tweet=fgets($tweets);
		 if (strcmp($tweet,"")!=0)
		 {
			 if (strcmp($sentiment,"0")==0) {
				echo "<table class='bordered'><tr><td bgcolor = '#E1F5A9' style='border-color:green'>".$tweet."<br></td></tr></table>";}
			 elseif (strcmp($sentiment,"4")==0) {
				echo "<table class='bordered'><tr><td bgcolor = '#F5A9A9' style='border-color:red'>".$tweet."<br></td></tr></table>";}
			 else{
				//echo "<td bgcolor='#FFFFFF'>".$tweet."<br></td>";
				}
		 }
	}while(!feof($answers)) ;
	fclose($answers);

	//Prints the complete associative array in standard format
	//echo print_r($data,1);
?>