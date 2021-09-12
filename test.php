<?php
#this is a test file
error_reporting( error_reporting() & ~E_NOTICE ); 
session_start();

include ("include/utilities.inc");

$db = login_connect();

//check following 

$profileID = 1;

$query = "select * from memberinfo";
$result = mysql_query($query, $db);

while($row = mysql_fetch_array($result)){
	echo $row['memberID'];
}



?>