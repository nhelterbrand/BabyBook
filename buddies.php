<?php 
/**
 * buddies.php displays the buddy list for that profile
 * 
 * PHP Version 5
 * 
 * @category PHP
 * @package  BabyBook
 * @author   Nate Helterbrand <helterb1@illinois.edu>
 * @license  none.com none
 * @link     none
 */

require "connect.php";
require "nonLogin.php";

$friends = $connect->getFriends($loggedInProfile);

?>
<!DOCTYPE html>
<html>
    <head>
		<title>BabyBook</title>
		<link href="bootstrap-3.0.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<body>
		<div class="container">
		    <div class="row">
		        <?php if (!$friends) {?>
		            <h3>You have zero buddies! Find some Baby buddies!</h3>
		        <?php } else {?>
	            <table class="table">
		            <?php foreach ($friends as $friend) {?>
		                <tr>
    		                <td><img src="<?php echo "images/".$friend->Picture;?>" width = "50px" height = "50px"></td>
        		            <td><?php echo $friend->FirstName. " ". $friend->LastName;?></td>
        		            <td><a href="newsFeed.php?id=<?php echo $friend->prof_id;?>" class="btn btn-default" role="button">View <?php echo $friend->FirstName;?>'s Profile</a></td>
    		            </tr>
		            <?php }}?>
	            </table>
            </div>
		</div>
	</body>
</html>
