<?php
// Create connection
$con=mysqli_connect("localhost","twit_sentiment","chelsea12","twit_sentiment");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$result = mysqli_query($con,"SELECT * FROM trend_analysis");

echo "<center><table border='1'>
<tr>
<th>Query</th>
<th>Date and Time</th>
<th>Positive</th>
<th>Negative</th>
</tr>";

while($row = mysqli_fetch_array($result))
  {
  echo "<tr><td>".$row['query']."</td><td>".$row['date']."</td><td>".$row['positive']."</td><td>".$row['negative']."</td></tr>";
  }
 echo "</center>";
mysqli_close($con);
 

?>