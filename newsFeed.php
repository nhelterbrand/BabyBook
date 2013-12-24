<?php
/**
* newsFeed.php provides a page for users to see their friends' activity
*
* @category PHP
* @package  BabyBook
* @author   Nate Helterbrand <helterb1@illinois.edu>
*
*/

require "connect.php";
require "nonLogin.php";

$newsFeed = $connect->getNewsFeed($profileInView);
?>
<!DOCTYPE html>
<html>
    <head>
		<title>BabyBook</title>
		<link href="bootstrap-3.0.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<style>
	textarea {
	   resize: none;
	}
	</style>
	<body>
		<div class="container">
		    <div class="row">
                <div class="col-sm-2">
                    <a href="home.php?id=<?php echo $profileInView;?>"><img src="<?php echo "images/".$connect->getProfile($profileInView)->Picture;?>" width="100px" height="100px"/></a>
                </div>
                <div class="col-sm-4">
                    <h4><?php echo $inViewProfile->FirstName. " ". $inViewProfile->LastName;?></h4>
                    <h5><?php echo $inViewProfile->Email;?></h5>
                    <?php if ($isCurrentProfile) { ?>
	                	<form class="form-inline" action="" method="post" name="statusForm">
	                		<div class="form-group">
	                			<input type="text" class="form-control" id="postStatus" name="statusMessage" placeholder="Post a Status">
	                		</div>
	                		<button type="submit" name="postStatus" class="btn btn-default">Post</button>
	                	</form>
                	<?php } ?>
                </div> 
                <div class="col-sm-2 col-sm-offset-2">
                	<?php if (!$isCurrentProfile){?>
                        <form method="post" action="" name="friendForm" id="friendForm">
                            <?php if (!$connect->isFriend($loggedInProfile, $profileInView)) {?>
                                <input type="submit" class="btn btn-primary btn-block" name="addFriend" id="addFriend" value="Add Friend" />
                            <?php } else {?>
                                <input type="submit" class="btn btn-primary btn-block" name="removeFriend" id="removeFriend" value="Remove Friend" />                 
                            <?php }?>
			    <a href="albums.php?id=<?php echo $profileInView;?>" class="btn btn-primary btn-block">View Photos</a>
                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#messageModal">Send Message</button>
                        </form>
                    <?php }?>
                </div>       
            </div>
            <br/>
            <div class="row">
            	<div class="col-md-8 col-md-offset-2">
            		<?php if ($newsFeed) { ?>
	            		<?php foreach ($newsFeed as $item) { ?>
	            			<div class="panel panel-default">
	            				<div class="panel-heading">
	            					<strong><?php echo $item->Date; ?></strong> from 
	            					<strong>
	            						<?php echo $connect->getProfile($item->prof_id)->FirstName. " ". $connect->getProfile($item->prof_id)->LastName; ?>
	            					</strong>
	            				</div>
	            				<div class="panel-body">
	            					<?php echo htmlspecialchars($item->Status); ?>
	            				</div>
	            				<div class="panel-footer">
	            					<form action="" method="post">
	            						<input type="hidden" value="<?php echo $item->ID; ?>" name="statusID">
			            				<?php if ($item->prof_id == $loggedInProfile) {?>		            					
			            					<button class="btn btn-danger btn-sm" type="submit" name="removeStatus">Remove Status</button>
			            				<?php } else { ?>
			            					<button class="btn btn-success btn-sm" type="submit" name="likeStatus">Like Status</button>
			            					<a class="btn btn-default btn-sm" data-toggle="collapse" data-parent="#accordion" href="#<?= $item->ID;?>collapse">Comment</a>
			            				<?php } ?>
			            				<div id="<?php echo $item->ID;?>collapse" class="panel-collapse collapse">
								      <div class="panel-body">
								      	<ul class="list-group">
								         <?php $statusComments = $connect->getComments($item->ID);
								               if ($statusComments) {
								               	foreach($statusComments as $sc) { ?>
								               	  <li class="list-group-item">
								               	  	<strong><?php echo $sc->Date; ?></strong> from
								               	  	<strong><?php echo $connect->getProfile($sc->prof_id)->FirstName. " ". $connect->getProfile($sc->prof_id)->LastName;?></strong>
								               	  	<?php echo $sc->message;?>
								               	  </li>
								         <?php } }?>
								      	</ul>
								      </div>
								      <div class="panel-footer">
								      	<textarea class="form-control" name="comment" rows="2"></textarea>
								      	<br/>
								      	<button class="btn btn-success btn-sm" type="submit" name="postComment">Post Comment</button>
								      </div>
								</div>
		            				</form>
	            				</div>
	            			</div>
	            		<?php } ?>
            		<?php } else { ?>
            			<h3>Theres nothing here currently</h3>
            		<?php } ?>
                </div>
            </div>
		</div>
	</body>
</html>