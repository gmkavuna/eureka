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