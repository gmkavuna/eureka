<?php 
//login page 
ob_start();
session_start();
error_reporting( error_reporting() & ~E_NOTICE ); 

include_once("include/analyticstracking.php"); //google tracking code 
include("include/header.inc");
include ("include/utilities.inc");

$db = login_connect();

function checkPass($login, $pass, $db){
	$query = "select * from memberlogins where login = '$login' AND password = SHA('$pass')";
	$result = mysql_query($query, $db);
	if (mysql_num_rows($result) > 0){
		return true;
	}
	else {
		return false;
	}
}

if ($_POST['submit']){
	$login = $_POST['login'];
	$password = $_POST['password'];
	$view = $_POST['view'];
	$post = $_POST['post'];
	
	
	if (strlen($login) && strlen($password) && (checkPass($login, $password, $db) == true)){
		//correct 
		$_SESSION['login'] = $login;
			$query = "select memberID from memberlogins where login = '$login'";
			$result = mysql_query($query, $db);
			while ($row = mysql_fetch_array($result)){
				$_SESSION['memberID'] = $row['memberID'];
			}
			if (is_numeric($post) && $view == 'detail'){
				header("Location: index.php?view=detail&post=".$post."");
			}
			else{
			?>
			
			<script>window.location.href = window.location.pathname;</script>
			<?php
			}
	}
	else{
		//if login info is incorrect determine the error
		//if any field has been left empty give an appropriate error message
		if (strlen($login) == 0 OR strlen($password) == 0){
			$error_message = "Please make sure you provide both a login and a password";
			
		}
		//in case the password or the login is incorrct give the appropriate message
		if (checkPass($login, $password, $db) == false){
			$error_message =  $error_message." Login or password incorrect.";
		}
		header("Location: login.php?error=$error_message");
	}
}

else{
	//in case the login form has not been submitted two options are there
	//1st check if the user is already logged in 
	if ($_SESSION['login']) {
		Header("Location: index.php");
	}
	else{
?>

<h2>Login</h2>

<?php
	echo "<p style='color:red;'>".$_GET['error']."</p>";
?>
<form action="login.php" method="POST">
	<table style="padding-top:0px;">
		<tr>
			<td>Username: </td>
			<td><input type="text" name="login"></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="submit"></td>
			<td></td>
		</tr>
		
	</table>
	<?php 
		if ($_GET['view'] == 'detail' && is_numeric($_GET['post'])){		
		?>
		<input type="hidden" name="view" value="<?php echo $_GET['view']; ?>">
		<input type="hidden" name="post" value="<?php echo $_GET['post']; ?>">
		<?php
		}
	?>
</form>
<?php
   }
}
include("include/footer.inc");
?>