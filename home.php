<?php 
/**
 * home.php is the home page for a logged in user
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
                <?php if ($displayAlert) { ?>
                    <div class="alert alert-info">
                        <?php echo $alertMessage; ?>
                    </div>
                <?php } ?>
                <div class="col-md-5">
                    <img src="<?php echo "images/".$connect->getProfile($profileInView)->Picture;?>" width="384px" height="384px"/>
                </div>
                <div class="col-md-4">
                    <h2><?php echo $inViewProfile->FirstName. " ". $inViewProfile->LastName;?></h2>
                    <h4><?php echo $inViewProfile->Email;?></h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <?php if (!$isCurrentProfile){?>
                                <form method="post" action="" name="friendForm" id="friendForm">
                                    <?php if (!$connect->isFriend($loggedInProfile, $profileInView)) {?>
                                        <button type="submit" class="btn btn-primary btn-block" name="addFriend" id="addFriend">Add Friend</button>
                                    <?php } else {?>
                                        <button type="submit" class="btn btn-primary btn-block" name="removeFriend" id="removeFriend">Remove Friend</button>                 
                                    <?php }?>
                                </form>
                            <?php }?>
                            <a href="albums.php?id=<?php echo $profileInView;?>" class="btn btn-primary btn-block">View Photos</a>
                            <?php if ($isCurrentProfile) { ?>
                                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#profPicModal">Change Profile Picture</button>
                            <?php } else {?>
                                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#messageModal">Send Message</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>        
            </div>
		</div>
	</body>
</html>
