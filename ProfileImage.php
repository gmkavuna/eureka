<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 $memberID = $_SESSION['memberID'];
 $profileID = $memberID;

 include_once("include/analyticstracking.php"); //google tracking code 
 include ("include/utilities.inc");
 include ("include/eureka_public.inc");
 include("include/header.inc");
 
 echo "<h4>Update Profile</h4>";
 $db = login_connect();
 
 $profile_array = fetch_profile($profileID, $db);

 echo "<div id='main'>";
 
 if ($_POST['submit']){
	$fName = $_POST['firstName'];
	$lName = $_POST['lastName'];
	$email = $_POST['email'];
	$intro = $_POST['intro'];
	$good_email = "[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}";
	
	//determining the error message 
	if(strlen($fName) == 0 OR strlen($lName) == 0 OR strlen($email) == 0 OR strlen($intro) == 0){
		$error_message = "Please fill all the fileds.";
	}
	
	if (strlen($fName) > 0 && strlen($lName) > 0 && strlen($email) > 0 && strlen($intro) > 0 && valid_input($email, $good_email)
	   && another_email_exist($email, $memberID, $db) == false){
		
		//insert data into the database
		$email = mysql_real_escape_string($email);
		#all checks have passed--time to create the member
             $success = true;  //flag to determine success of transaction
             

             //start transaction
             $command = "SET AUTOCOMMIT=0";
             $result = mysql_query($command);
             $command = "BEGIN";
             $result = mysql_query($command);

             
             
             $command = "update memberInfo set firstName = '$fName', lastName='$lName', email = '$email', intro = '$intro', updated = now() WHERE memberID = '$memberID'";
             $result = mysql_query($command);
             
			 if (($result == false) ||
                (mysql_affected_rows() == 0)) {
                 $success = false;
             }
           
            if (!$success) {
              $command = "ROLLBACK";
              $result = mysql_query($command);
              echo  "We're sorry, there has been an error on our end.  
                                Please contact us to report this bug.";
			  echo "MemberID is ".$memberID;
            }
            else {
              $command = "COMMIT";
              $result = mysql_query($command);
			  echo "<p style='color:green;'>Your profile has been successfully updated!!</p>";
              #set session variable
              
            }
            $command = "SET AUTOCOMMIT=1";  //return to autocommit
            $result = mysql_query($command); 
	}
	else{
		Header("Location: profile.php?error=1&s_firstName=$fName&s_lastName=$lName&s_email=$email&s_intro=$intro&error_message=$error_message");
	}
 }
 else{
	echo "<p style='color:red;'>".$_GET['error_message']."</p>";
	
	?>
	<form action="profile.php" method="POST">
		<?php 
			while(list($key, $value) = each($profile_array)){
				$fName = $value['firstName'];
				$lName = $value['lastName'];
				$email = $value['email'];
				$intro = $value['intro'];
				//find profile image
				if (strlen($value['photo']) > 0){
					$photo = $value['photo'];
				}
				else{
					$photo = "placeholder.png";
				}
				echo "<img src='images/profiles/".$photo."' style='float:left; margin-right:5px;' height=100 width = 100></p>";
			}
		?>
		<table>
			
			<tr>
				<td>Intro</td>
				<td>
					<textarea name="intro"><?php if ($_GET['error'] == 1) { echo $_GET['s_intro']; } else { echo $intro; } ?></textarea>
				</td>
			</tr>
			
			
			<tr>
				<td><input type="submit" name="submit" value="update"></td>
				<td></td>
			</tr>
			
		</table>
	</form>
	
	
	<?php
 }
 
 echo "</div>";
 
 
 
 #now, a nice-looking footer 
 include("include/footer.inc");
 ?> 