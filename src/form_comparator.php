

<?php

// If the user clicked the Run button
if (isset($_POST['submit'])) 
{
	// Gather their input
	// Escape input values before redisplaying them in the form
	$run = true;
	$query1 = htmlspecialchars($_POST['query1'], ENT_QUOTES);
	$query2 = htmlspecialchars($_POST['query2'], ENT_QUOTES);
}

// If the form is run for the first time 
else
{
	$query1 = '';
	$query2 = '';
	$run=false;
}

print "<html>";
print "<head>";
print "<title>Twitter Sentiment Analyser</title>";
print "</head>";
print "<body>";

//Form for entering the query
print "<form action='comparator.php' method='post'>";
print "<input type='text' name='query1' value='$query1'>";
print "<input type='text' name='query2' value='$query2'>";
print "<input type='submit' name='submit' value='Search' /> ";
print "<br/><br/>";



?>