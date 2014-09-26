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

$query_mod=$query." -http"." -https";
//echo "QUERY: ", $query_mod;
//print "<br/><br/>";

//lang= language of tweets to be displayed
//count= number of tweets needed
//query= keyword being searched
//result_type can be recent, popular or mixed 
$array=array("lang"=>"en","count"=>"30","q"=>$query_mod,"result_type"=>"recent");

//Creating the connection and storing it in code
$code = $connection->request($method, $connection->url($url),$array);

// Display the response HTTP code
$code = $connection->response['code'];
//print "<strong>Code:</strong> $code<br/>";

// Display the API response as an array
//print "<strong>Response:</strong><pre style='word-wrap: break-word'>";

//To print all details
//print_r(json_decode($connection->response['response'],true));

//To decode the complete json file
$objJson=json_decode($connection->response['response'],true);

$data = array();
//To retrieve only the text information from the JSON file
foreach($objJson["statuses"] as $status) {
    $data[] = array(
        "text" => $status["text"]
    );
}

//To display the associative array with only text
$i=1;
foreach($data as $x=>$x_value) {
		echo $i, ". ", $x_value['text'];
		$i=$i+1;
		print"<br/><br/>";
}

//Prints the complete associative array in standard format
//echo print_r($data,1);
?>