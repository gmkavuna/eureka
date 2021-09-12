<?php
 #logout.php -- removes session variable 'memberID'    
 error_reporting( error_reporting() & ~E_NOTICE ); 
 
 include_once("include/analyticstracking.php"); //google tracking code 

 session_start();
 $_SESSION['memberID'] = '';
 $_SESSION['login'] = ''; 

 #a nice-looking header
 include("include/header.inc");
?>

<h4>You have successfully logged out. <a href="index.php">Click here</a> to view public posts.</h4>


<?php
 #a nice-looking footer
 include("include/footer.inc");
?>
