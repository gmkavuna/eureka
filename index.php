<?php
 error_reporting( error_reporting() & ~E_NOTICE ); 
 session_start();
 $memberID = $_SESSION['memberID'];
 
 $flag = $_GET['flag'];
 
 if (!($_GET['flag'])){
	$flag = "Recent";
 }

 include_once("include/analyticstracking.php");
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
 if ($memberID){ //if a user is logged in 
	if ($_GET['member']){
		$profileID = $_GET['member'];
		$posts_array = fetch_customer_posts($db, $profileID);
		$pageTitle = "";
	}
	else{
		$profileID = $memberID;
		$posts_array = fetch_network_posts($db, $profileID, $flag);
		$pageTitle = "<h4>".$flag." Posts</h4>";
	}
	  
 }
 else{
	$posts_array = fetch_posts($db, $visibility, $memberID ='', $flag); 
	$pageTitle = "<h4>".$flag." Posts</h4>";
 }
 
 echo "<div id='main'>";
 
 //page title is determined up abov 
 //it sometimes contains the flag (recent/popular) or contains none of them but the name of the author 
 //if we are viewing a user's profile the title will contain their name 
 //otherwise it will contain recent or popular
 //the decision s to how title is to be displayed is decided above
 echo $pageTitle;

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

 if ($_GET['view'] == "detail"){
	//view more details concerning the post
	if ($_POST['submit']){
		echo "<h4>comment submitted</h4>";
		$auth = $_SESSION['memberID'];
		$comment =  $_POST['comment'];
		$concernedPost = $_GET['post'];
		
		//form validation 
		if (strlen($comment) > 0 && is_numeric($concernedPost) && is_numeric($auth)){
			//everything is fine - insert the comment into the database
			$auth = mysql_real_escape_string($auth);
			$comment = mysql_real_escape_string($comment);
			$concernedPost = mysql_real_escape_string($concernedPost);
			$query = "insert into comments values ('', '$concernedPost', '$auth', '$comment', now())";
			mysql_query($query, $db);
		}
		else{
			//redirect user back to the form with user-friendly messages
			header("Location: index.php?view=detail&error=1&s_comment=$comment&post=$concernedPost");
		}
	}
	$post = $_GET['post'];
	$post_details_array = fetch_post_details($db, $post);
	while (list($key, $value) = each($post_details_array)) {
	if (strlen($value['photo']) > 0){
		$photo = $value['photo'];
	}
	else{
		$photo = "placeholder.png";
	}
	//get the number of comments associated with this post 
	$postID = $value['postID'];
	$comments_array = fetch_comments($db, $post);
	$comments = count($comments_array);
	echo "<h4>".$value['title']."</h4>";
	echo "<p style='clear:both; '><a href='#'><img src='images/profiles/".$photo."' height=70 width = 70 style='float:left; margin-right:10px'>".$value['firstName']." ".$value['lastName']."</a>:".$value['description']."
	<br/><strong>Learnt from: </strong>: ".$value['source']."</p>
	<p class='grey'>comments(".$comments.") . ".$value['datePosted']."</p>";
    }
	//display comments to this post here
	
	echo "<div style='background: #f4f4f4; width: 400px; padding:10px; margin-left:80px;'>";
	while(list($key, $value) = each($comments_array)){
		if (strlen($value['photo']) > 0 ){
			$image = $value['photo'];
		}
		else{
			$image = "placeholder.png";
		}
		echo "<li style='clear:both; list-style-type: none' margin-top:5px;><a href='#'><img src='images/profiles/".$image."' height=50 width = 50 style='float:left; margin-right:10px'>".$value['firstName']." ".$value['lastName']."</a>:".$value['comment']."<br/>
	   <br/><span class='grey'>".$value['commentDate']."</span></li>";
	}
	?>
	<?php 
		if ($memberID){
	?>
		<form action="index.php?view=detail&post=<?php echo $post; ?>" method="POST">
			<table>
				<tr>
					<td>
						<?php 
							$commentator_profile= fetch_profile($memberID, $db);
							while (list($key, $value) = each($commentator_profile)){
								$name = $value['firstName']." ".$value['lastName'];
								if (strlen($value['photo']) > 0){
									$img = $value['photo'];
								}
								else {
									$img = "placeholder.png";
								}
								echo "<a href='index.php?member='><span style='font-size:9px;'>".$value['firstName']." ".$value['lastName']."</span><br/><img src='images/profiles/".$img."' height=60 width = 60 style='float:left; margin-right:10px'></a>";
							}
						?>
					</td>
					<td><textarea cols='32' name= "comment" rows="5"></textarea>
						<?php
							if($_GET['error'] == 1 && strlen($_GET['s_comment']) == 0){
								echo "<span style='color:red;'>Please add a comment</span>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="comment"></td>
					
				</tr>
			</table>
		</form>
	<?php
	}
	else{
		echo "<a href='login.php?view=detail&post=".$postID."'>Login</a> to comment";
	}
	
	echo "</div>";
}
 else{
	$profile = fetch_profile($profileID, $db);
	if ($_GET['member']){
		while(list($key, $value) = each($profile)){
			$nm = $value['firstName']." ".$value['lastName'];
			echo "<h3>".$nm."'s posts</h3>";
		}
	}
	
	 if ($number_of_pages > 0){ //show pagination if there's at least one post 

		 if ($page == 1) {
			echo "&lt;&lt; back ";
		 }
		 else {
			$back = $page - 1;
			echo "<a href='index.php?member=$profileID&page=$back'>&lt;&lt; back </a>";
		 }
		 echo "| ".$page." of ".$number_of_pages." |";
		 if ($page == $number_of_pages || $number_of_pages == 0){
			
			echo " next &gt;&gt;";
		 }
		 else {
			$next = $page +1;
			echo "<a href='index.php?member=$profileID&page=$next'> next &gt;&gt;</a>"; 
		 }
	 }
	 //otherwise show just a message telling the visitor that this member has not posted anything yet
	 else{
		echo "<p>Sorry, this member has not posted anything yet.</p>";
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
		<br/><span class='grey';'>comments(".$comments.") . ".$value['datePosted']; ?> <?php if ($memberID && isAuthor($db, $memberID, $postID)){ echo "<a href='updatePost.php?post=".$postID."'>[update post]</a>"; } echo "</span></li>";
	 }
	 echo "</ol>";
	 }
 ?>
 
 <?php

 

 
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
		?>
		</div>
		<div class="box">
		  <div class="top">
		   <div class="inside">
				<div class="title">
				 <div class="left">
				  tags
				 </div>
				 <div class="clear_both"></div>
				</div>
		   </div>
		  </div>
		  <div class="bot">
		   <div class="inside">
		<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/tag_styles.css" />
		<?php
		include("include/tag_cloud.inc");
		$tag_array = fetch_top_tags($memberID, $db);
		$cloud = new wordCloud($tag_array);
		echo $cloud->showCloud();
		?>
		   </div>
		  </div>

 
 <?php
		 echo "</div>";
 echo "</div>";
 
 #now, a nice-looking footer 
 include("include/footer.inc");
 ?> 