<?php
/* this page allows to add other members to one's network*/
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 $memberID = $_SESSION['memberID'];
 $profileID = $memberID;
 
 $flag = $_GET['flag'];

 include_once("include/analyticstracking.php");
 include ("include/utilities.inc");
 include ("include/eureka_public.inc");
 include("include/header.inc");

 
 echo "<h4>Follow a member</h4>";
 
 

 $db = login_connect();
 
 $page = 1;
 if ($_GET['page']){
	$page = $_GET['page'];
 }
 
 echo "<div id='main'>";
 
 if ($_GET['memberToAdd']){
	$follower = $memberID;
	$followedMember = $_GET['memberToAdd'];
	
	//insert into the database
	$query = "insert into connections values ('', '$follower', '$followedMember', now(), '')";
	$result = mysql_query($query, $db);
	if (mysql_insert_id() != 0){
		echo "<span style='color:green;'>You are now following ".$_GET['name']."</span><br/><br/>";
	}
 }
 $toSuggestArray = fetch_to_suggest($db, $memberID);
 
 //results pagination 
if (count($toSuggestArray) % 10 == 0){
	$number_of_pages = count($toSuggestArray)/10 ; //number of pages
}
else {
	$number_of_pages = (int)(count($toSuggestArray)/10) + 1;
}
//extract posts for concerned page 
$start = ($page-1)*10; //10 is the number of posts per page 
$end = 10 - 1; 

$toDisplay = array_slice($toSuggestArray, $start, $end);
if (count($toSuggestArray) == 0) {
	echo "Looks like you are following all the members in the network";
 }
 else{
if ($page == 1) {
		echo "&lt;&lt; back ";
	 }
	 else {
		$back = $page - 1;
		echo "<a href='add.php?page=$back'>&lt;&lt; back </a>";
	 }
	 echo "| ".$page." of ".$number_of_pages." |";
	 if ($page == $number_of_pages){
		
		echo " next &gt;&gt;";
	 }
	 else {
		$next = $page +1;
		echo "<a href='add.php?page=$next'> next &gt;&gt;</a>"; 
	 }
}


echo "<ol class='posts'>";
while (list($key, $value) = each($toDisplay)){
		
	if (strlen($value['photo']) > 0){
			$img = $value['photo'];
		}
		else{
			$img = "placeholder.png";
		}
		$id = $value['memberID'];
			echo "<li style='clear:both;'><a href='index.php?member=".$value['memberID']."'>
			        <img src='images/profiles/".$img."' height=50 width = 50 style='float:left; margin-right:10px'>"
					.$value['firstName']." ".$value['lastName']."</a>: <br/>".$value['intro']."<br/><a href='add.php?memberToAdd=".$value['memberID']."&name=".$value['firstName']."' style='font-weight:bold;'>[follow]</a></li>";
		}
		echo "</ol>";
 

 
 
 echo "</div>";
 
 echo "<div id='right'>";
	echo "<div id='profile'>";
		 if ($profileID){ //check if member is logged in 
			//if there's a request to follow a member whom profile we are viewing 
			 if (is_numeric($_GET['addMember'])){
				  $follower = $_SESSION['memberID'];
				  $followedMember = $_GET['addMember'];
				  $query = "insert into connections values ('', '$follower', '$followedMember', now(), '')";
				  $result = mysql_query($query, $db);
				  if (mysql_insert_id() != 0){
					  echo "<br/><span style='color:green;'>You are now following ".$_GET['name']."</span>";
				  }
			 }
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

			echo "<h4 style='color:#0e90ad;'>".$memberName."</h4>";
			echo "<span class='grey'>Member Since ".$joinDate."</span>";
			?>
			
			<table style="clear:both; width:100%">
				<tr class="grey" style="text-align:left;">
					<th>Posts</th>
					<th>Followers</th>
					<th>Following</th>
				</tr>
				<tr style="text-align:center;">
					<td><a href="<?php echo "" ;?>"><?php echo count(fetch_customer_posts($db, $profileID)); ?></a></td>
					<td><a href="<?php echo 'network.php?member='.$profileID ;?>"><?php echo count(fetch_connections($db, $profileID)); ?></a></td>
					<td><a href="<?php echo 'network.php?mode=following&member='.$profileID ;?>"><?php echo count(fetch_following($db, $profileID)); ?></a></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><?php 
							if ($profileID == $memberID) {
								echo "<a href='profile.php'>Edit>></a>";
							}
							else{
								if (isFollowed($db, $memberID, $profileID)){
									echo "Following>>";
								}
								else{
									echo "<a href='index.php?addMember=".$profileID."&name=".$nm."'>Follow>></a>";
								}
							}
						?>
					</td>
				</tr>
			</table>
			
			<?php
			
		}
		 echo "</div>";
 echo "</div>";
 
 #now, a nice-looking footer 
 include("include/footer.inc");
 ?> 