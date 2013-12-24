<?php
/**
* inbox.php provides a page for users to see their messages from friends
*
* @category PHP
* @package  BabyBook
* @author   Nate Helterbrand <helterb1@illinois.edu>
*
*/
require "connect.php";
require "nonLogin.php";

$inbox = $connect->getMessages($loggedInProfile);
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
		    	<?php if ($messageAlert) { ?>
                    <div class="alert alert-info">
                        <?php echo $messageAlertMessage; ?>
                    </div>
                <?php } ?>
                <div class="col-sm-2">
                    <a href="home.php"><img src="<?php echo "images/".$connect->getProfile($profileInView)->Picture;?>" width="100px" height="100px"/></a>
                </div>
                <div class="col-sm-4">
                    <h4><?php echo $inViewProfile->FirstName. " ". $inViewProfile->LastName;?></h4>
                    <h5><?php echo $inViewProfile->Email;?></h5>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#messageModal">Send Message</button>
                </div>       
            </div>
            <br/>
            <div class="row">
            	<div class="col-md-8 col-md-offset-2">
            		<h3>Inbox</h3>
            		<?php if ($inbox) { ?>
	            		<?php foreach ($inbox as $item) { ?>
	            			<div class="panel panel-default">
	            				<div class="panel-heading">
	            					<strong><?php echo $item->Date; ?></strong> from 
	            					<strong>
	            						<?php echo $connect->getProfile($item->prof_id)->FirstName. " ". $connect->getProfile($item->prof_id)->LastName; ?>
	            					</strong>
	            				</div>
	            				<div class="panel-body">
	            					<?php echo htmlspecialchars($item->Message); ?>
	            				</div>
	            				<div class="panel-footer">
		            				<form action="inbox.php" method="post" name="removeMessage">
		            					<input type="hidden" value="<?php echo $item->ID;?>" name="messageID">
		            					<button class="btn btn-danger btn-sm" type="submit" name="removeMessage">Remove Message</button>
		            				</form>
	            				</div>
	            			</div>
	            		<?php } ?>
            		<?php } else { ?>
            			<h4>No Messages!</h4>
            		<?php } ?>
                </div>
            </div>
		</div>
	</body>
</html>
