<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 $memberID = $_SESSION['memberID'];
 $profileID = $memberID;

 include ("include/utilities.inc");
 include ("include/eureka_public.inc");
 include("include/header.inc");


$db = login_connect();

$postToUpdate = 16;	

$word = "hellx";

$word = substr($word, -1);

echo $word;

 
?>

<a class="btn btn-default" href="#" role="button">Link</a>
<input class="btn btn-default" type="submit" value="Submit">
	
<?php

 #now, a nice-looking footer 
 include("include/footer.inc");

?>