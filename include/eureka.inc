<?php

#tasty_utilities.inc -- a private library for t.as.ty recipes

function member_db_connect() {
	#returns a database handle which allows updates to everything except customer_logins
	
	$host = "localhost"; 
	$user = "root";
	$pw = "";
	$database = "tasty_recipes";
	
	$db = mysql_connect($host, $user, $pw)
		or die ("Cannot connect to mysql");
	mysql_select_db($database, $db)
		or die("Cannot connect to database");
		
	return $db;
}

function login_db_connect() {
   //returns a database handle which allows full updates to customer_logins 
   //and limited updates to customer_info

   $host = "localhost";  
   //yourlogin should be your Sandbox login
   $user = "root";
   $pw = "";
   $database = "tasty_recipes";

   $db = mysql_connect($host,$user,$pw)
       or die("Cannot connect to mySQL.");

   mysql_select_db($database,$db)
       or die("Cannot connect to database.");
       
   return $db;
}
function fetch_profile($profileID, $db) {
   $profile_array = array();
   if (is_numeric($profileID)) {
     #make sure that this profile exists
     $command = "SELECT ci.email, ci.homepage, ci.name, UNIX_TIMESTAMP(ci.date_enrolled) 
               AS date_enrolled, cl.login, password(cl.password) FROM
               customer_info ci, customer_logins cl WHERE ci.customer_id=cl.customer_id AND
               cl.date_deactivated <= 0 AND ci.customer_id='".mysql_real_escape_string($profileID)."';";
     $theresult = mysql_query($command, $db);
     if ($theresult && mysql_num_rows($theresult) > 0) {
        $profile_array = mysql_fetch_assoc($theresult);
     }
   }
   return $profile_array;
 }
 function fetch_customer_bookmarks($profileID, $db, $page) {
   #bookmark array is a two-dimensional array.
   #it holds arrays of bookmark information
   $customer_bookmark_array = array();
   if (is_numeric($profileID)) {
     $command = "SELECT cb.bookmark_id, cb.title, cb.notes, UNIX_TIMESTAMP(cb.date_posted) 
               AS date_posted, bu.url FROM
               customer_bookmarks cb, bookmark_urls bu WHERE cb.bookmark_id=bu.bookmark_id AND
               cb.customer_id='".mysql_real_escape_string($profileID)."' AND cb.date_deleted<=0";
     if (!(is_numeric($page)) || $page < 1) {
      $page = 1;
   }
     $command .= " LIMIT ".(($page - 1) * 5).",5";
     $result = mysql_query($command, $db);
     while ($this_bookmark_array = mysql_fetch_assoc($result)) {
        array_push($customer_bookmark_array, $this_bookmark_array);
     }
   }
   return $customer_bookmark_array;
 }
 
 function fetch_connections($profileID, $db) {
   #bookmark array is a two-dimensional array.
   #it holds arrays of connection information
   $connection_array = array();
   if (is_numeric($profileID)) {
    	   
     $com = "SELECT ci.name, cl.login, c.connectionID, cl.customer_id
            FROM customer_info ci, connection c, customer_logins cl WHERE cl.customer_id = ci.customer_id AND
			((ci.customer_id=c.req_member AND c.app_member='".addslashes($profileID)."') 
                 OR (ci.customer_id=c.app_member AND c.req_member='".addslashes($profileID)."')) 
               AND cl.date_deactivated <= 0 AND c.app_date>0;";
     $res = mysql_query($com, $db);
     while ($this_connection_array = mysql_fetch_assoc($res)) {
        array_push($connection_array, $this_connection_array);
     }
   }
   return $connection_array;
 }
 
 function fetch_network_bookmarks($profileID, $db) {
   #network_bookmark array is a two-dimensional array.
   #it holds arrays of connection information. Here the network is made of the member whose profile is being viewed and all the members with whom they are connected
   $network_bookmarks_array = array();
   if (is_numeric($profileID)) {
	   
     $com = "SELECT DISTINCT ci.name, cl.login, c.connectionID, cl.customer_id
            FROM customer_info ci, connection c, customer_logins cl WHERE cl.customer_id = ci.customer_id AND
			((ci.customer_id=c.req_member AND c.app_member='".addslashes($profileID)."') 
                 OR (ci.customer_id=c.app_member AND c.req_member='".addslashes($profileID)."') OR (ci.customer_id = $profileID)) 
               AND cl.date_deactivated <= 0 AND c.app_date>0 GROUP BY customer_id;";
     $res = mysql_query($com, $db);
     while ($this_network_bookmarks_array = mysql_fetch_assoc($res)) {
        array_push($network_bookmarks_array, $this_network_bookmarks_array);
     }
   }
   return $network_bookmarks_array;
 }
 
 //this function determines the number of bookmarks entered by a specific customer. 
 function fetch_customer_bookmarks_number($profileID, $db) {
   #bookmark array is a two-dimensional array.
   #it holds arrays of bookmark information
   $customer_bookmark_number_array = array();
   if (is_numeric($profileID)) {
     $command = "SELECT cb.bookmark_id, cb.title, cb.notes, UNIX_TIMESTAMP(cb.date_posted) 
               AS date_posted, bu.url FROM
               customer_bookmarks cb, bookmark_urls bu WHERE cb.bookmark_id=bu.bookmark_id AND
               cb.customer_id='".mysql_real_escape_string($profileID)."' AND cb.date_deleted<=0;";
     $result = mysql_query($command, $db);
     while ($this_customer_bookmark_number_array = mysql_fetch_assoc($result)) {
        array_push($customer_bookmark_number_array, $this_customer_bookmark_number_array);
     }
   }
   return $customer_bookmark_number_array;
 }

//helper function which allows members to edit a bookmark 
function bookmark_to_edit($profileID, $db, $bookmark_id = '') {
  #bookmark array is a two-dimensional array.
  #it holds arrays of bookmark information
  $bookmark_array = array();
  if ($profileID) {
     $command = "SELECT cb.bookmark_id, cb.title, cb.notes, 
               UNIX_TIMESTAMP(cb.date_posted) AS date_posted, bu.url FROM
               customer_bookmarks cb, bookmark_urls bu WHERE cb.bookmark_id=bu.bookmark_id AND
               cb.customer_id='".mysql_real_escape_string($profileID)."' AND cb.date_deleted<=0";
     if (is_numeric($bookmark_id)) {
        $command .= " AND cb.bookmark_id='".$bookmark_id."'";
     }
     $result = mysql_query($command, $db);
     while ($this_bookmark_array = mysql_fetch_assoc($result)) {
        array_push($bookmark_array, $this_bookmark_array);
     }
  }
  return $bookmark_array;
}
 function get_customer_login($db, $customer_id) {
	$login_array = array();
	$customer_id = mysql_real_escape_string($customer_id);
	$command = "SELECT login from customer_logins WHERE customer_id = '$customer_id'";
	$result = mysql_query($command, $db);
	while ($this_login_array = mysql_fetch_assoc($result)) {
		array_push($login_array, $this_login_array);
	}
	return $login_array;
      
 }
  
//helper function to delete/flag a bookmark 
function delete_bookmark($bookmark_id, $db, $customer_id) {
 
             $success = true;  //flag to determine success of transaction
             //start transaction
             $command = "SET AUTOCOMMIT=0";
             $result = mysql_query($command);
             $command = "BEGIN";
             $result = mysql_query($command);

             #first, customer_logins
             $command = "UPDATE customer_bookmarks set date_deleted = now() where bookmark_id = $bookmark_id AND customer_id = $customer_id";
             $result = mysql_query($command);
             if (($result == false) ||
                 (mysql_affected_rows() == 0)) {
                 $success = false;
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
              #set session variable
              $_SESSION['customer_id'] = $customer_id;
              $_SESSION['customer_login'] = $login;
            }
            $command = "SET AUTOCOMMIT=1";  //return to autocommit
            $result = mysql_query($command);             
          }
//helper function to retrieve tags 
function fetch_bookmark_tags($profileID, $db, $bookmark_id = '') {
   #tag_array is a one-dimensional array holding tags.
   $tag_array = array();
   if ($profileID) {
     $command = "SELECT bt.tag_id, tt.tag FROM
	       bookmark_tags bt, tasty_tags tt WHERE bt.tag_id=tt.tag_id AND
	       bt.customer_id='$profileID' AND bt.date_deleted<=0";
     if (is_numeric($bookmark_id)) {
        $command .= " AND bt.bookmark_id='".$bookmark_id."'";
     }
     $result = mysql_query($command);
     while ($this_tag_array = mysql_fetch_array($result)) {
        array_push($tag_array, $this_tag_array['tag']);
     }
   }
   return $tag_array;
 }
//this function helps to retrieve all tags stored 
function fetch_tags($db) {
   #tag_array is a one-dimensional array holding tags.
   $tag_array = array();
     $command = "SELECT bt.tag_id, tt.tag FROM
	       bookmark_tags bt, tasty_tags tt WHERE bt.tag_id=tt.tag_id AND
	       bt.date_deleted<=0";
     $result = mysql_query($command);
     while ($this_tag_array = mysql_fetch_array($result)) {
        array_push($tag_array, $this_tag_array['tag']);
     }
   
   return $tag_array;
 }
//helper function to retrieve bookmarks from tag_id 
function fetch_bookmarks_by_tag($tag_id, $db, $page) {
    #bookmark array is a two-dimensional array.
    #it holds arrays of bookmark information
    $bookmarks_by_tag_array = array();
    $command = "SELECT bookmark_id from bookmark_tags where tag_id = '$tag_id'";
    $result = mysql_query($command, $db);
    while ($row = mysql_fetch_array($result)) {
		$bookmark_id = $row['bookmark_id'];
		$com= "SELECT cb.bookmark_id, cb.title, cb.notes, UNIX_TIMESTAMP(cb.date_posted) 
               AS date_posted, bu.url FROM
               customer_bookmarks cb, bookmark_urls bu WHERE cb.bookmark_id=bu.bookmark_id AND cb.bookmark_id = '$bookmark_id'";
		 if (!(is_numeric($page)) || $page < 1) {
		  $page = 1;
		}
		 $res = mysql_query($com, $db);
		 while ($this_bookmark_array = mysql_fetch_assoc($res)) {
			array_push($bookmarks_by_tag_array, $this_bookmark_array);
		 }  
	}
	return $bookmarks_by_tag_array;
 }

	
?>