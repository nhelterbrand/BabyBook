<?php
/**
* albums.php provides a page for users to see someone's photo albums
*
* @category PHP
* @package  BabyBook
* @author   Nate Helterbrand <helterb1@illinois.edu>
*
*/
require "connect.php";
require "nonLogin.php";

$albums = $connect->getAlbums($profileInView);
$currentAlbum = "";
if (isset($_POST['albumID'])) {
	$albumID = $_POST['albumID'];
	$currentAlbum = $connect->getAlbum($albumID);
}

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
               	    <div class="row">
               	    	<?php if ($isCurrentProfile) { ?>
               	    		<form action="" method="post" class="form-inline">
	       	    			<div class="form-group">
					    <label class="sr-only" for="exampleInputEmail2">Album Name</label>
					    <input type="text" class="form-control" name="albumName" placeholder="Album Name">
					</div>
					<button type="submit" name="createAlbum" class="btn btn-default">Create Album</button>
               	    		</form>
               	    </div>
               	    <div class="row">
               	    	<?php } if ($albums) { ?>
               	    		<?php foreach ($albums as $a) { ?>
               	    			<div class="col-xs-6 col-md-3">
               	    				<form action="" method="post">
						       	<img class="thumbnail" src="images/album.png">
						        <div class="caption">
						        	<input type="hidden" name="albumID" value="<?php echo $a->ID; ?>">
						        	<button type="submit" class="btn btn-primary btn-sm" name="clickedAlbum" data-toggle="modal" data-target="#albumModal">View <?php echo $a->albumName;?></button>
						       	</div>
					        </form>
					</div>
               	    		<?php } ?>
               	    	<?php } else { ?>
               	    		<h3>No albums here!</h3>
               	    	<?php } ?>
                   </div>
		</div>
		<div class="modal fade" id="albumModal" tabindex="-1" role="dialog" aria-labelledby="albumModalLabel" aria-hidden="true">
	            <div class="modal-dialog">
	                <div class="modal-content">
	                    <div class="modal-header">
	                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                        <h4 class="modal-title" id="albumModalLabel"><?php echo $currentAlbum->albumName; ?></h4>
	                    </div>
	                    <div class="modal-body">
	                    	<?php $pictures = $connect->getPictures($currentAlbum->ID); ?>
	                        <?php if (!$pictures) { ?>
	                            <h4>No pictures here!</h4>
	                        <?php } else { ?>
	                        <?php foreach ($pictures as $picture) {?>
	                            <img src="<?php echo "images/".$picture->pic;?>" width = "100px" height = "100px">
	                        <?php }}?>
	                    </div>
	                    <div class="modal-footer">
	                    	<?php if ($isCurrentProfile) { ?>
	                    		<input type="hidden" name="albumID" value="<?php echo $currentAlbum->ID; ?>"/>
		                        <form enctype="multipart/form-data" action="home.php" method="post" name="addPhotoForm" id="addPhotoForm">
		                            <input name="image" type="file" class="button">
		                            <button type="submit" class="btn btn-primary pull-left" name="addPhoto" value="Add Photo">Add Photo</button>
		                        </form>
	                        <?php } ?>
	                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	                    </div>
	                </div>
	            </div>like
	        </div>
	</body>
	<script>
	 $(document).ready(function(){
	    $("clickedAlbum").click(function() {
	    	var albumID = $('albumID').attr('value');
	        $.ajax({
	            type: "POST",
	            url: "vote.php",
	            data: "albumID=" + albumID,   
	            success: submitFinished
	            });
	
	            function submitFinished() {
	  }	
	    });
	});
	</script>
</html>