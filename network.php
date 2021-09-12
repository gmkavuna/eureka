<?php

#network page - displays a list of people a member is following on one hand on another hand it displays a list of members 
#whom the member is following 
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();

 include_once("include/analyticstracking.php"); //google tracking code 
 
$memberID = $_SESSION['memberID'];
 
 
 if (!($memberID)){
	header("Location: login.php");
 }

 include ("include/utilities.inc");
 include ("include/eureka_public.inc");
 include("include/header.inc");

 $db = login_connect();
 
 $page = 1;
 if ($_GET['page']){
	$page = $_GET['page'];
 }
 
 //determining the user
 if ($_GET['member']){
	$profileID = $_GET['member'];
 }
 else{
	$profileID = $memberID;
 }
 
 echo "<div id='main'>";

$mode = $_GET['mode'];
if ($mode == 'following'){
	$network_array = fetch_following($db, $profileID);
	echo "<h4>Following</h4>";
}
else{
	$network_array = fetch_connections($db, $profileID);
	echo "<h4>Followed by</h4>";
}

//results pagination 
if (count($network_array) % 10 == 0){
	$number_of_pages = count($network_array)/10 ; //number of pages
}
else {
	$number_of_pages = (int)(count($network_array)/10) + 1; 
}
//extract posts for concerned page 
$start = ($page-1)*10; //10 is the number of posts per page 
$end = 10 - 1; 

$members_to_display = array_slice($network_array, $start, $end);

if ($page == 1) {
		echo "&lt;&lt; back ";
	 }
	 else {
		$back = $page - 1;
		echo "<a href='network.php?mode=".$mode."&page=$back'>&lt;&lt; back </a>";
	 }
	 echo "| ".$page." of ".$number_of_pages." |";
	 if ($page == $number_of_pages){
		
		echo " next &gt;&gt;";
	 }
	 else {
		$next = $page +1;
		echo "<a href='network.php?mode=".$mode."&page=$next'> next &gt;&gt;</a>"; 
	 }
	 

echo "<ol class='posts'>";
	while (list($key, $value) = each($members_to_display)) {
	   if (strlen($value['photo']) > 0){
			$img = $value['photo'];
		}
		else{
			$img = "placeholder.png";
		}	
		//get author's profile
		
		if ($mode == 'following'){
			$id = $value['followedMember'];
		}
		else {
			$id = $value['follower'];
		}
		
		echo "<li style='clear:both;'><a href='index.php?member=".$id."'>
		      <img src='images/profiles/".$img."' height=50 width = 50 style='float:left; margin-right:10px'>
			  ".$value['firstName']." ".$value['lastName']."
			  </a></li>";
	 }
	 echo "</ol>";
 ?>
 
 <?php

 

 
 echo "</div>";
 
 echo "<div id='right'>";
 if ($profileID){ //check if member is logged in 
    $profile = fetch_profile($profileID, $db);
	while(list($key, $value) = each($profile)){
		$photo = $value['photo'];
		$joinDate = $value['dateEnrolled'];
		$memberName = $value['firstName']." ".$value['lastName'];
	}
	
	if (strlen($photo) == 0){
		$passport = "placeholder.png";
	}
	else {
		$passport = $photo;
	}
	echo "<p style='padding-top:0px; margin-top:0px;'><img src='images/profiles/".$passport."' style='float:left; margin-right:5px;' height=100 width = 100></p>";

	echo "<h4>".$memberName."</h4>";
	echo "<span class='grey'>Member Since ".$joinDate."</span>";
	?>
	
	<table style="clear:both; width:100%">
		<tr class="grey" style="text-align:left;">
			<th>Posts</th>
			<th>Followers</th>
			<th>Following</th>
		</tr>
		<tr style="text-align:center;">
			<td><a href="<?php echo 'index.php?member='.$profileID ;?>"><?php echo count(fetch_customer_posts($db, $profileID)); ?></a></td>
			<td><a href="<?php echo 'network.php?member='.$profileID ;?>"><?php echo count(fetch_connections($db, $profileID)); ?></a></td>
			<td><a href="<?php echo 'network.php?mode=following&member='.$profileID ;?>"><?php echo count(fetch_following($db, $profileID)); ?></a></td>
		</tr>
	</table>
	
	<?php
}
 echo "</div>";
 
 #now, a nice-looking footer 
 include("include/footer.inc");
 ?> 