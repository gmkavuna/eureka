<?php
#

function public_db_connect() {
    #returns a database handle which allows ONLY SELECT on customer_bookmarks and bookmark_urls
  
    $host = "localhost";  
    //yourlogin should be your Sandbox login
    $user = "muhire5_public";
    $pw = "public@123";
    $database = "tasty_recipes";
    $db = mysql_connect($host,$user,$pw)
       or die("Cannot connect to mySQL.");

    mysql_select_db($database,$db)
       or die("Cannot connect to database.");
   
    return $db;
 }
?>