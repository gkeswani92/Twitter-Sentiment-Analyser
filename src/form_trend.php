	<script>
	!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id))
	{js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
	fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
	</script>
	
	<script>
	!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);
	js.id=id;js.src="https://platform.twitter.com/widgets.js";
	fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
	</script>

	<?php
	// If the user clicked the Run button
	if (isset($_POST['submit'])) 
	{
		// Gather their input
		// Escape input values before redisplaying them in the form
		$run = true;
		$query = htmlspecialchars($_POST['query'], ENT_QUOTES);
		$last = htmlspecialchars($_POST['duration'], ENT_QUOTES);
		if($last=='week')
			$dur='Last Week';
		if($last=='months3')
			$dur='Last 3 Months';
		if($last=='months6')
			$dur='Last 6 Months';
		if($last=='year')
			$dur='Last year';
			
		
	}
	// If the form is run for the first time 
	else
	{
		$dur = 'Select Duration';
		$query = '';
		$run=false;
		
	}
	print "<html>";
	print "<head>";
	print "<title>Twitter Sentiment Analyser</title>";
	print "</head>";
	print "<body>";

	//Form for entering the query
	print "<form action='historical.php' method='post' >"; 	
	print "<select name= 'duration' >";
	print "<option selected='true' style='display:none;'> $dur  </option>";
	print "<option></option>";
	print "<option value='week'>Last Week</option>";
	print "<option value='months3'>Last 3 Months </option>";
	print "<option value='months6'>Last 6 Months </option>";
	print "<option value='year'>Last Year </option>";
	print "</select>";	
	print "<input type='text' name='query' value='$query'>";
	print "<input type='submit' name='submit' value='Search' /> ";
	print "<br/><br/>";

	//Credits
	print "<center>";
	print "<a href='https://twitter.com/Twit_Sentiment' class='twitter-follow-button' data-show-count='true' data-lang='en'>Follow @Twit_Sentiment</a>";
	print "<a href='https://twitter.com/share' class='twitter-share-button' count='none' data-lang='en' data-text='Twit Sentiment Analyser really helps you analyse your products brand value. Try it out :) https://twitter.com/Twit_Sentiment'>Tweet</a>";

	print "</center>";


	?>