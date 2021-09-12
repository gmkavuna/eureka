<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 include_once("include/analyticstracking.php"); //google tracking code 
 if (!($_SESSION['memberID'])){
	//id user is not logged in redirect to login page 
	header("Location: login.php");
 }
 #join.php--interface for joining the tasty recipe site  
 #script includes transaction, escape with both htmlescapecharcaters and mysql_real_escape_string functions 
 #form input is thouroughly validated - can't pass the form if any field is empty, if email is invalid or if using a login or email which is already in the system

 #a nice-looking header
 
 include("include/header.inc");
 include ("include/utilities.inc");
 
 //pulling in database connection 
 $db = login_connect();
 
 //pulling ID 
 $memberID = $_SESSION['memberID'];
 
 
if (isset($_POST['submit'])) {
	
	$title = htmlspecialchars($_POST['title']);
	$description = htmlspecialchars($_POST['description']);
	$source = htmlspecialchars($_POST['source']);
	$reference = htmlspecialchars($_POST['reference']);
	$tags = htmlspecialchars($_POST['tags']);
	$visibility = htmlspecialchars($_POST['visibility']);
	
	
	$tag_array = explode(",", $tags);

	//determining the error
	if(strlen($title) == 0 || strlen($description) ==  0 || strlen($source) == 0 || strlen($tags) || $visibility == ""){
		$error_message = "Please fill all the required fileds.";
	}
	if (strlen($title) == 0){
	    $error_message = $error_message. "<br/>Title is required.";
	}
	if (strlen($description) == 0){
	    $error_message = $error_message. "<br/>Description is required.";
	}
	if (strlen($source) == 0){
	    $error_message = $error_message. "<br/>Source is required.";
	}
	if (strlen($tags) == 0){
	    $error_message = $error_message. "<br/>At least one tag is required.";
	}
	if (strlen($visibility) == 0){
	    $error_message = $error_message. "<br/>Please choose your post's visibility.";
	}
	
	//validate input
	if(strlen($title) > 0 && strlen($description) > 0 && strlen($source) > 0 && strlen($tags) > 0 && $visibility > 0){
		//prepare data for database inclusion
		$title = mysql_real_escape_string($title);
		$description = mysql_real_escape_string($description);
		$source = mysql_real_escape_string($source);
		$reference = mysql_real_escape_string($reference);
		#all checks have passed--time to create the member
             $success = true;  //flag to determine success of transaction
             //start transaction
             $command = "SET AUTOCOMMIT=0";
             $result = mysql_query($command);
             $command = "BEGIN";
             $result = mysql_query($command);

             #first, insert into posts table
             $command = "INSERT INTO posts (postID, title, description, source, reference, memberID, datePosted, visibility) VALUES
                    ('', '$title', '$description', '$source', '$reference', '$memberID', now(), $visibility)";
             $result = mysql_query($command);
             if (($result == false) ||
                 (mysql_affected_rows() == 0)) {
                 $success = false;
             }
             else {
                #now, tags
                $postID = mysql_insert_id();
				
				
				for ($i = 0; $i < count($tag_array); $i++) {
					$tag = $tag_array[$i];
					if ($tag_array[$i][0] == " "){
					    $tag = str_replace($tag_array[$i][0], '', $tag);
					}
					//first check if the tag is alredy in the database
					$query = "SELECT * FROM tags where tag = '$tag'";
					$result = mysql_query($query, $db);
					if (mysql_num_rows($result) > 0 ){
						while ($rows = mysql_fetch_array($result)){
							$tagID = $rows['tagID'];
						}
					}
					else{
						$command = "INSERT INTO tags (tagID, tag) VALUES ('', '$tag')";
						$result = mysql_query($command);
						$tagID = mysql_insert_id();
						if (mysql_affected_rows() == 0){
							$success = false;	
						}
					}
					//now update posts_tags
					if ($success){
						$query = "insert into posts_tags (memberID, postID, tagID, dateDeactivated) values ('$memberID', '$postID', '$tagID', '')";
						$result = mysql_query($query, $db);
					}
				}
                if (($result == false) ||
                    (mysql_affected_rows() == 0)) {
                    $success = false;
                }
            }

            if (!$success) {
              $command = "ROLLBACK";
              $result = mysql_query($command);
              echo "<p class='error'>We're sorry, there has been an error on our end.  
                                Please contact us to report this bug.</p>";
			 
            }
            else {
              $command = "COMMIT";
              $result = mysql_query($command);
			  echo "<p class='success'>Your post has been successfully created!!</p>";
              #set session variable
              
            }
            $command = "SET AUTOCOMMIT=1";  //return to autocommit
            $result = mysql_query($command);   
			 
 
	}
	else{
		header("Location: post.php?error_message=$error_message&s_title=$title&s_description=$description&s_source=$source&s_reference=$reference&tag_array=$size&s_tags=$tags");
	}
	
}
else{
?>

<h4>Save/share what you have learnt today!</h4>  

<p>It is straight forward. Just fill out the flowm below while specifying who you want your post to be visible to.</p>  
<span style="color:red;font-size:12px;">
<?php
   echo $_GET['error_message'];
?>
</span>
<form method="POST" action="post.php">
<table>
  <tr>
    <td align="right">
      *What did you learn today?:
    </td>
    <td align="left">
      <input type="text" size="70" max="70" name="title" value="<?php echo $_GET['s_title']; ?>">
    </td>
  </tr>
  <tr>
    <td align="right">
      *More details about it:<br/>
	  <span style='color:gray; font-size:11px;'>(300 characters max)</span>
    </td>
    <td align="left">
      <textarea name="description" rows="5" cols="61"><?php if (isset($_GET['s_description'])) {echo $_GET['s_description']; }?></textarea>
    </td>
  </tr>
  <tr>
    <td align="right">
      *Learnt from :
    </td>
    <td align="left">
      <input type="name" size="70" max="12" name="source" value="<?php echo $_GET['s_source']?>">
    </td>
  </tr>
  <tr>
    <td align="right">
      Any reference? 
    </td>
    <td align="left">
      <input type="text" size="70" max="12" name="reference" value="<?php echo $_GET['s_reference']?>">
    </td>
  </tr>
 <tr>
    <td align="right">
      *Tag your post <br/>
	  <span style='color:gray; font-size:11px;'>(separated by ",")</span>
    </td>
    <td align="left">
      <input type="text" size="70" max="12" name="tags" value="<?php echo $_GET['s_tags']?>">
    </td>
  </tr>
  <tr>
    <td align="right">
      *Visibility
    </td>
    <td align="left">
      <select name="visibility">
		<?php 
			$query="select * from visibility";
			$res = mysql_query($query, $db);
			echo "<option value=''>Make a choice</option>";
			while($row = mysql_fetch_array($res)){
				echo "<option value='".$row['visibilityID']."'>".$row['visibility']."</option>";
			}
		 ?>
		
	  </select>
	  
	  
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
	  <br/>
      <input type="submit" value="SUBMIT" name="submit">
    </td>
  </tr>
</table>

</form>

<p style='color:gray; font-size:11px;'><strong>Note</strong>: <br/>Visibility for a post determines who will be able to view it. A public post is visible to everyone, logged in or not.
   A private post is only visible to the author and those following the author. A personal post is only visible to the author.
   <br/><br/>* denotes required field.</p>


<?php
}
 #a nice-looking footer
 
 include("include/footer.inc");
?>