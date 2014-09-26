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
	//To retrieve only the text information from the JSON file
	foreach($objJson["statuses"] as $status) {
		$data[] = array(
			"text" => $status["text"]
		);
	}

	//To send the tweets to the tweets.txt file
	$tweets = fopen("Classifier/Tweets.txt","w");
	foreach($data as $x=>$x_value) {
			fwrite($tweets,$x_value['text']);
			fwrite($tweets,"\n");
	}
	fclose($tweets);
	
	require 'classify.php';
?>