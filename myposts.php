<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 $memberID = $_SESSION['memberID'];
 $profileID = $memberID;
 
 $flag = $_GET['flag'];

 include_once("include/analyticstracking.php"); //google tracking code 
 include ("include/utilities.inc");
 include ("include/eureka_public.inc");
 include("include/header.inc");

 $db = login_connect();
 $visibility = 'public';
 
 $page = 1;
 if ($_GET['page']){
	$page = $_GET['page'];
 }
 
 //extract the user who's profile is to be viewed
 //determining the user
 $posts_array = fetch_customer_posts($db, $memberID);
 
 echo "<div id='main'>";
 echo "<h4>My Posts</h4>";
 
//results pagination 
if (count($posts_array) % 10 == 0){
	$number_of_pages = count($posts_array)/10 ; //number of pages
}
else {
	$number_of_pages = (int)(count($posts_array)/10) + 1; 
}
//extract posts for concerned page 
$start = ($page-1)*10; //10 is the number of posts per page 
$end = 10 - 1; 

$posts_to_display = array_slice($posts_array, $start, $end);

 

	$profile = fetch_profile($profileID, $db);
	if ($_GET['member']){
		while(list($key, $value) = each($profile)){
			$nm = $value['firstName']." ".$value['lastName'];
			echo "<h3>".$nm."'network 's posts</h3>";
		}
	}
	//display pagination only if there's at least one page 
	//otherwise make the user aware that they haven't made any post yet
	if($number_of_pages >= 1){
	
		if ($page == 1) {
			echo "&lt;&lt; back ";
		}
		else {
			$back = $page - 1;
			echo "<a href='myposts.php?page=$back'>&lt;&lt; back </a>";
		}
		echo "| ".$page." of ".$number_of_pages." |";
		
		if ($page == $number_of_pages || $number_of_pages <= 1){
			echo " next &gt;&gt;";
		}
		else {
			$next = $page +1;
			echo "<a href='myposts.php?page=$next'> next &gt;&gt;</a>"; 
		}
	}
	else
	{
		echo "You have not made any post yet! Please click <a href='post.php'>here</a> to share your learning";
	}
	 echo "<ol class='posts'>";
	 while (list($key, $value) = each($posts_to_display)) {
		if (strlen($value['photo']) > 0){
			$img = $value['photo'];
		}
		else{
			$img = "placeholder.png";
		}
		//get the number of comments associated with this post 
		$postID = $value['postID'];
		
		//get author's profile
		
		$author = $value['firstName']." ".$value['lastName'];
		
		$comments_array = fetch_comments($db, $postID);
		$comments = count($comments_array);
		echo "<li style='clear:both;'><a href='index.php?member=".$value['memberID']."'><img src='images/profiles/".$img."' height=50 width = 50 style='float:left; margin-right:10px'>"
		.$author."</a>: <a href='index.php?view=detail&post=$postID'>".$value['title']."</a><br/>".$value['description']."
		<br/><span class='grey';'>comments(".$comments.") . ".$value['datePosted'];  if ($memberID && isAuthor($db, $memberID, $postID)){ echo "<a href='updatePost.php?post=".$postID."'> [update post]</a>"; } echo "</span></li>";
	 }
	 echo "</ol>";

 ?>
 
 <?php

 

 
 echo "</div>";
 
 echo "<div id='right'>";
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
						echo "<a href='edit.php'>Edit>></a>";
					}
					else{
						if (isFollowed($db, $memberID, $profileID)){
							echo "You are connected!>>";
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
 
 #now, a nice-looking footer 
 include("include/footer.inc");
 ?> 