<?php
//utilities contains a number of functions which are being used throughout the application

//member with limited access
function public_connect(){
//returns database connection
	$host = "localhost";
	$user = "root";
	$pw = "";
	$database = "muhire5_eureka";
	$db = mysql_connect($host, $user, $pw)
			or die("Unable to connect to mysql");
	mysql_select_db($database, $db) 
			or die ("Unable to connect to database");
	return $db;
}
function login_connect(){
//returns database connection
	$host = "localhost";
	$user = "muhire5_login";
	$pw = "login@123";
	$database = "muhire5_eureka";
	$db = mysql_connect($host, $user, $pw)
			or die("Unable to connect to mysql");
	mysql_select_db($database, $db) 
			or die ("Unable to connect to database");
	return $db;
}

//check if login exists 
function login_exist($input, $db){
		$query = "select * from memberlogins where login = '$input'";
	$result = mysql_query($query, $db);
	if (mysql_num_rows($result) > 0){
		return true;
	}
	else {
		return false;
	}
}

/*
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
*/
	


#a function to check inputs against a regular expression representing the proper input format.
 function valid_input($myinput, $good_input) {
   //$myinput is the string being checked.  
   //$good_input is the regular expression used to check it.
   if (preg_match("/$good_input/", $myinput, $regs)) {
        return true;
   }
   else {
        return false;
   }
 }
 
#helper function to output latest posts by any member

 //function to retrieve member;s information
function fetch_customer_posts($db, $memberID) {
   #bookmark array is a two-dimensional array
   #it holds arrays of bookmark information
   $member_post_array = array();
    if (is_numeric($memberID)) {
     $command = "SELECT p.postID, p.title, p.memberID, p.description, p.datePosted, mi.firstName, mi.lastName, mi.photo from posts as p left join memberinfo as mi on 
			   p.memberID = mi.memberID where p.memberID='".mysql_real_escape_string($memberID)."'";
    
     $result = mysql_query($command, $db);
     while ($this_bookmark_array = mysql_fetch_assoc($result)) {
        array_push($member_post_array, $this_bookmark_array);
     }
   }
   return $member_post_array;
 }
 function fetch_profile($memberID, $db) {
   $profile_array = array();
   if (is_numeric($memberID)) {
     #make sure that this profile exists
     $command = "select mi.*, ml.* from memberinfo as mi left join memberlogins as ml on mi.memberID = ml.memberID where mi.memberID = '$memberID'";
     $result = mysql_query($command, $db);
     while($this_array = mysql_fetch_assoc($result)){
		array_push($profile_array, $this_array);
	 }
   }
   return $profile_array;
 }
 //fetch posts 
function fetch_posts($db, $visibility, $memberID, $flag) {
   #fetch_posts is a two-dimensional array.
   #it holds arrays of post information
   $posts_array = array();
   
   $command = "select p.title, p.datePosted, p.memberID, p.postID, p.description, mi.memberID, mi.firstName, mi.lastName, 
               mi.photo, count(com.postID) as numberOfComments from posts as p LEFT JOIN memberinfo as mi 
               ON mi.memberID = p.memberID left join comments as com on p.postID = com.postID";
	if ($visibility == 'public'){
		$command = $command." WHERE p.visibility = 1 ";
	}
	if ($visibility == 'private'){
		$command = $command." WHERE p.visibility <= 2 "; 
	}
	if ($visibility == 'personal'){
		$command = $command." Where p.visibility <= 3 ";
	}
	if ($memberID){
		$command = $command. " AND p.memberID = '$memberID' ";
	}
	
	$command = $command." Group by p.postID ";
	if ($flag == 'popular'){
		$command = $command." order by numberOfComments DESC ";
	}
	else{
		$command = $command." order by p.datePosted DESC ";
	}
	
   $result = mysql_query($command, $db);
   while ($this_posts_array = mysql_fetch_assoc($result)) {
        array_push($posts_array, $this_posts_array);
   }
   return $posts_array;
}
//fetch post details 
function fetch_post_details($db, $post){
	$post_details_array = array();
	$command = "select p.title, p.datePosted, p.memberID, p.postID, p.source, p.description, p.visibility, mi.memberID, mi.firstName, mi.photo from posts as p LEFT JOIN memberinfo as mi 
              ON mi.memberID = p.memberID where postID = '$post'";
	$result = mysql_query($command);
	while ($this_array = mysql_fetch_assoc($result)){
		array_push($post_details_array, $this_array);
	}
	return $post_details_array;
}
//fetch comments
function fetch_comments($db, $postID) {
   #fetch_posts is a two-dimensional array.
   #it holds arrays of post information
   $comments_array = array();
   
   $command = "select c.*, mi.* from comments as c left join memberinfo as mi on c.memberID = mi.memberID where c.postID = '$postID'";
   $result = mysql_query($command, $db);
   while ($this_comments_array = mysql_fetch_assoc($result)) {
        array_push($comments_array, $this_comments_array);
   }
   return $comments_array;
}
function fetch_connections($db, $member){
	$network_array = array();
	$command = "select c.follower, mi.* from connections as c left join memberinfo as mi on c.follower = mi.memberID where c.followedMember = '$member'";
	$result = mysql_query($command, $db);
	while ($this_network_array = mysql_fetch_assoc($result)) {
        array_push($network_array, $this_network_array);
    }
	return $network_array;
}
//The difference between this function and fetch_posts is that it does not start with an array declaration because it is to be called 
// after fetch_posts has been called

/* this function is discarded. I run into duplicates problems when I tried it in the first place 
function fetch_network_posts2($db, $member, $flag){
	$network_posts_array = fetch_posts($db, $visibility = 1, $memberID, $flag);
	$connection_array = fetch_connections($db, $member);
	while(list($key, $value) = each($connection_array)){
		$id = $value['follower'];
		$command = "select p.postID, p.memberID, p.title, p.description, mi.firstName, mi.lastName,  p.datePosted, count(com.postID) 
		            as popularity
		            from posts as p left join memberInfo as mi on p.memberID = mi.memberID left join comments as com on p.postID = com.postID
					where mi.memberID = '$id' AND p.visibility <= 2 GROUP BY p.postID ";
		if ($flag == 'popular'){
			$command = $command." order by popularity DESC ";
		}
		if ($flag == 'recent'){
			$command = $command." order by p.datePosted DESC ";
		}
		$result = mysql_query($command, $db);
		while($this_array = mysql_fetch_assoc($result)){
				array_push($network_posts_array, $this_array);
		}
	}
	return array_unique($network_posts_array, SORT_REGULAR);
}
*/

function fetch_network_posts($db, $member, $flag){
	$network_posts_array = array();
	$query = "select p.postID, p.memberID, p.title, p.description, mi.firstName, p.visibility, mi.lastName,  p.datePosted, count(com.postID) 
		            as popularity from posts as p left join memberinfo as mi on p.memberID = mi.memberID left join comments 
					as com on p.postID = com.postID GROUP BY p.postID ";
	if ($flag == 'popular'){
		$query = $query." ORDER BY popularity DESC ";
	}
	if ($flag == 'recent'){
		$query = $query." ORDER BY p.datePosted DESC ";
	}
	$result = mysql_query($query, $db);
	while($this_array = mysql_fetch_assoc($result)){
		if ($this_array['memberID'] == 1 OR (isFollowed($db, $member, $this_array['memberID']) && $this_array['visibility'] == 2) OR $this_array['visibility'] == 1){
			array_push($network_posts_array, $this_array);
		}
	}
	return $network_posts_array;
}
//this function returns an array of members who are following a given member
function fetch_following($db, $member){
	$following_array = array();
	$command = "select c.*, mi.* from connections as c left join memberinfo as mi on c.followedMember = mi.memberID WHERE c.follower = '$member'";
	$result = mysql_query($command, $db);
	while ($this_array = mysql_fetch_assoc($result)){
		array_push($following_array, $this_array);
	}
	return $following_array;
}
function fetch_to_suggest($db, $memberID){
	$to_suggest_array = array();
	$query = "select * from memberinfo where memberID != '$memberID'";
	$result = mysql_query($query, $db);
	while ($this_array = mysql_fetch_assoc($result)){
		$member = $this_array['memberID'];
		$command = "select * from connections where follower = '$memberID' && followedMember = '$member'";
		$res = mysql_query($command, $db);
		if (mysql_num_rows($res) == 0){
			array_push($to_suggest_array, $this_array);
		}
	}
	return $to_suggest_array;
}
function isFollowed($db, $follower, $followedMember){
	$query = "select * from connections where follower = '$follower' AND followedMember = '$followedMember'";
	$result = mysql_query($query, $db);
	if(mysql_num_rows($result) > 0){
		return true;
	}
	else{
		return false;
	}
}
//function to retrieve the top 25 tags
function fetch_top_tags($profileID, $db, $postID = '') {
   #tag_array is a one-dimensional array holding tags.
   $tag_array = array();
  
     $command = "SELECT pt.tagID, t.tag FROM
	       posts_tags pt, tags t WHERE pt.tagID=t.tagID ";
    
     $result = mysql_query($command, $db);
     while ($this_tag_array = mysql_fetch_assoc($result)) {
        array_push($tag_array, $this_tag_array['tag']);
     }
   return $tag_array;
}

//function to get post's tags 
function fetch_post_tags($db, $postID) {
   #tag_array is a one-dimensional array holding tags.
   $tag_array = array();
  
     $command = "SELECT pt.tagID, pt.postID , t.tag FROM posts_tags as pt left join tags as t ON pt.tagID = t.tagID WHERE pt.postID = '$postID'";

     $result = mysql_query($command, $db);
     while ($this_tag_array = mysql_fetch_assoc($result)) {
        array_push($tag_array, $this_tag_array);
     }
   return $tag_array;
}

//chech if email exists
	function email_exist($address, $db){
		$command = "select * from memberinfo where email = '$address'";
		$result = mysql_query($command, $db);
		if (mysql_num_rows($result) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	function another_email_exist($address, $memberID, $db){
		$command = "select * from memberinfo where email = '$address' && memberID != '$memberID'";
		$result = mysql_query($command, $db);
		if (mysql_num_rows($result) > 0){
			return true;
		}
		else{
			return false;
		}
	}

function isAuthor($db, $member, $postToUpdate){
	$postDetails = fetch_post_details($db, $postToUpdate); 
	while (list($key, $value) = each($postDetails)) {
		$author = $value['memberID'];
	}
	if ($author == $member){
		return true;
	}
	else{
		return false;
	}
}



?>