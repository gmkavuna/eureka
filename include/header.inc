<?php
ob_start();
//function which deals with the menu
function print_navigation($nav_flag, $nav_title, $nav_url, $last = false) {
    $good_url = "[a-z]+.php(\?[a-z]+=[a-zA-Z0-9%]+)?";
    if (preg_match("/$good_url/",$nav_url)) {
      $class_name = "nav";
      if ($last) {
         $class_name = "last";
      }
      if ($nav_flag == $nav_title) {
      ?>
      <b class="<?php echo $class_name; ?>"><?php 
         echo htmlentities($nav_title); ?></b>
      <?php
      }
      else {
      ?>
      <a class="<?php echo $class_name; ?>" href="<?php echo $nav_url; ?>"><?php 
         echo htmlentities($nav_title); ?></a>
      <?php
      }
    }
 }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>eureka! lets you save or share what you have learn't today</title>

<!-- Bootstrap -->
    <!-- <link href="http://localhost/eureka/css/bootstrap.min.css" rel="stylesheet"> -->
	
	<link href='http://fonts.googleapis.com/css?family=Lato:100,300' rel='stylesheet' type='text/css'>
	
 
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

	<link rel="stylesheet" rev="stylesheet" href="css/styles.css" />

</head>

<body>

<!-- Google analytics tracking code -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-58775820-1', 'auto');
  ga('send', 'pageview');

</script>

<div id="page">
    
	<div id="tasty_header">
		<div class="top">
			<div class="inside">
				<table style="width:100%;"><tr>
					<td rowspan=2 style="padding-right:10px;width:60px;">
					<a href="index.php"><img src="images/logo.png"  height = "60" alt="eureka" /></a>
				</td>
				
				<td align="right" valign="top" style="padding-right:8px;">
				    <?php if ($_SESSION['memberID'] && $_SESSION['login']) { 
				    	?>
		                        <span class="nav">logged in as <b><?php 
		                            echo htmlentities($_SESSION['login']); ?></b>
		                        </span>
		                        <a class="last"  href="logout.php">logout</a>
		                        <?php 
		                        }
		                       else { ?>
		                        <a class="nav" href="login.php">login</a><a
		                            class="last" href="join.php">join</a>
				         <?php 
				         } 
				        ?>	
				</td>
				
				</tr>
				<tr><td colspan=2 style="align:left;font-size:20px;">
					
				</td></tr></table>
			</div>
		</div>
		<div class="bot">
			<div class="inside">
				<table style="width:100%;"><tr>
				<td style="font-weight:bold;align:left;">
				<?php if ($_SESSION['memberID']) 
				     { ?>
		                       <?php print_navigation($navigation_flag, "my posts",
	                                            "myposts.php"); ?>
	                            <?php print_navigation($navigation_flag, "post what you've learnt today",
                                                "post.php"); ?>
								<?php print_navigation($navigation_flag, "follow",
                                                "add.php"); ?>
								<?php print_navigation($navigation_flag, "update profile",
                                                "profile.php"); ?>
                                       
		                         
                                <?php
		                      }
		                      
		                      else { ?>
		                        <a href="post.php">save/share</a> what you have learn't today...<a 
                        href="index.php">browse</a> what others are learning...<a 
                        href="join.php">join</a> today, it's easy!   
				         <?php
				         } 
				        ?>
				
                                 
                        </td>
						<td align=right valign=top>
							<?php print_navigation($navigation_flag, "popular",
													"index.php?flag=popular"); ?>
							<?php print_navigation($navigation_flag, "recent",
													"index.php?flag=recent"); ?>
						</td>
                        <td align=right valign=top></td>
				</tr></table>
			</div>
		</div>
	</div><!-- /header -->
        
    
	<div id="tasty_content">
	
