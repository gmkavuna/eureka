<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 #join.php--interface for joining the tasty recipe site  
 #script includes transaction, escape with both htmlescapecharcaters and mysql_real_escape_string functions 
 #form input is thouroughly validated - can't pass the form if any field is empty, if email is invalid or if using a login or email which is already in the system

 include_once("include/analyticstracking.php"); //google tracking code 
 
 #a nice-looking header
 include("include/header.inc");
 include ("include/utilities.inc");
 
 //pulling in database connection 
 $db = login_connect();
 
 
if (isset($_POST['submit'])) {
	
	$email = htmlspecialchars($_POST['email']);
	$login = htmlspecialchars($_POST['login']);
	$password = htmlspecialchars($_POST['password']);
	$password2 = htmlspecialchars($_POST['password2']);
	$good_email = "[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}";
	
	//determining the error
	if(strlen($email) == 0 || strlen($login) ==  0 || strlen($password) == 0 || strlen($password2) == 0){
		$error_message = "Please fill all the fileds.";
	}
	if(valid_input($email, $good_email) == false) {
		$error_message = $error_message." Please provide a valid email address.";	
	}
	if ($password != $password2){
		$error_message = $error_message." Password does not match";
	}
	if (email_exist($email,$db)){
		$error_message = $error_message." Another account with the same email address exists";
	}
	if (login_exist($login, $db)){
		$error_message = $error_message." <span style='text-decoration:underline; font-weight:bold;'>".$login."</span> is taken. Please choose another login";
	}
	
	//validate input
	if(strlen($email) > 0 && strlen($login) > 0 && strlen($password) > 0 && strlen($password2) > 0 && valid_input($email, $good_email) && $password == $password2 && (email_exist($email, $db) == false) && (login_exist($login, $db) == false)){
		//insert data into the database
		$email = mysql_real_escape_string($email);
		$login = mysql_real_escape_string($login);
		$password = mysql_real_escape_string($password);
		#all checks have passed--time to create the member
             $success = true;  //flag to determine success of transaction
             $customer_id = '';//customer_id to be determined by auto_increment

             //start transaction
             $command = "SET AUTOCOMMIT=0";
             $result = mysql_query($command);
             $command = "BEGIN";
             $result = mysql_query($command);

             #first, customer_logins
             $command = "INSERT INTO memberlogins (memberID, login, password, dateDeactivated) VALUES
                    ('', '$login', SHA('$password'), '')";
             $result = mysql_query($command);
             if (($result == false) ||
                 (mysql_affected_rows() == 0)) {
                 $success = false;
             }
             else {
                #now, customer_info
                $memberID = mysql_insert_id();
                $command = "INSERT INTO memberinfo (memberID, email, dateEnrolled) VALUES ('$memberID', '$email', now())";
                $result = mysql_query($command);
                if (($result == false) ||
                    (mysql_affected_rows() == 0)) {
                    $success = false;
                }
            }

            if (!$success) {
              $command = "ROLLBACK";
              $result = mysql_query($command);
              $error_message = "We're sorry, there has been an error on our end.  
                                Please contact us to report this bug.";
            }
            else {
              $command = "COMMIT";
              $result = mysql_query($command);
			  echo "<p style='color:green;'>Your account has been successfully created!!</p>";
              #set session variable
              $_SESSION['memberID'] = $memberID;
              $_SESSION['login'] = $login;
              
            }
            $command = "SET AUTOCOMMIT=1";  //return to autocommit
            $result = mysql_query($command);             
	}
	else{
		header("Location: join.php?error_message=$error_message&s_email=$email&s_login=$login&s_password=$password&s_password2=$password2");
	}
	
}
if ($_SESSION['memberID']) {
    header("Location: index.php");
}
else{
?>

<h4>Join now and start sharing your learning.  It's easy and free!</h4>    
<span style="color:red;font-size:12px;">
<?php
   echo $_GET['error_message'];
?>
</span>
<form method="POST" action="join.php">
<table>
  <tr>
    <td align="right">
      Your email address:
    </td>
    <td align="left">
      <input type="text" size="25" max="50" name="email" value="<?php echo $_GET['s_email']; ?>">
    </td>
  </tr>
  <tr>
    <td align="right">
      Choose a login:
    </td>
    <td align="left">
      <input type="text" size="12" max="12" name="login" value="<?php echo $_GET['s_login']?>">
    </td>
  </tr>
  <tr>
    <td align="right">
      Choose a password:
    </td>
    <td align="left">
      <input type="password" size="12" max="12" name="password">
    </td>
  </tr>
  <tr>
    <td align="right">
      Retype your password:
    </td>
    <td align="left">
      <input type="password" size="12" max="12" name="password2">
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" value="SUBMIT" name="submit">
    </td>
  </tr>
</table><br>
Already a member?  <a href="login.php">Click here</a> to log in!
</form>


<?php
}
 #a nice-looking footer
 include("include/footer.inc");
?>