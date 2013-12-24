<?php
/**
* nonLogin.php is a header file for any page after the login/registration section
*
* PHP Version 5
*
* @category PHP
* @package  BabyBook
* @author   Nate Helterbrand <helterb1@illinois.edu>
*/

session_start();
$loggedInProfile = $_SESSION['id'];

$messageAlert = false;
$messageAlertMessage = "";

//valid ID's start at 1, so if this is true, then no one has logged in
if ($loggedInProfile == -1) {
    printf("<script>location.href='login.php'</script>");
}
$connect = new Connect('helterb1_babybook');
//check to see if the user is viewing another user's page or their own
if (!empty($_GET['id'])) {
    $profileInView = $_GET['id'];
    if ($loggedInProfile == $profileInView)
    {
        $isCurrentProfile = true;
        $inViewProfile = $connect->getProfile($loggedInProfile);
    } else {
        $isCurrentProfile = false;
        $currentProfile = $connect->getProfile($loggedInProfile);
        $inViewProfile = $connect->getProfile($profileInView);
    }
} else {
    $isCurrentProfile = true;
    $profileInView = $loggedInProfile;
    $inViewProfile = $connect->getProfile($loggedInProfile);
}

if (isset($_POST['sendMessage'])) {
	if (!empty($_POST['message']) && !empty($_POST['fullName'])) {
		$prof_id = $_POST['prof_id'];
		$message = $_POST['message'];
		$name = $_POST['fullName'];
		$fullName = explode(" ", $name);
		$fname = $fullName[0];
		$lname = $fullName[1];
		$connect->sendMessage($loggedInProfile, $prof_id, $message);
		$messageAlert = true;
		$messageAlertMessage = "You have successfully sent ". $name. " a message";
	} else {
		$messageAlert = true;
		$messageAlertMessage = "You need to fill out all the fields for the message first.";
	}
}

if (isset($_POST['removeMessage']))
{
	$id = $_POST['messageID'];
	$connect->removeMessage($id);
}

if (isset($_POST['postComment'])) {
	if (!empty($_POST['comment'])) {
		$prof_id = $loggedInProfile;
		$message = $_POST['comment'];
		$parentID = $_POST['statusID'];
		$connect->postComment($parentID, $prof_id, $message);
	}
}

if (isset($_POST['likeStatus'])) {
	$statusID = $_POST['statusID'];
	$connect->addLike($statusID, $loggedInProfile);
}

if (isset($_POST['postStatus'])) {
	$message = $_POST['statusMessage'];
	$connect->postStatus($loggedInProfile, $message);
}

if (isset($_POST['removeStatus'])) {
	$id = $_POST['statusID'];
	$connect->removeStatus($id);
}

if (isset($_POST['createAlbum'])) {
	if (!empty($_POST['albumName'])) {
		$prof_id = $loggedInProfile;
		$albumName = $_POST['albumName'];
		$connect->createAlbum($prof_id, $albumName);
	}
}



$alertMessage = "";
$displayAlert = false;
$target = "images/";

if (isset($_POST['addFriend'])) {
    $connect->addFriend($loggedInProfile, $profileInView);
    $alertMessage = "You and " .$connect->getProfile($profileInView)->FirstName. " are now friends! Yay, baby buddies!";
    $displayAlert = true;
} else if (isset($_POST['removeFriend'])) {
    $connect->removeFriend($loggedInProfile, $profileInView);
    $alertMessage = "You and " .$connect->getProfile($profileInView)->FirstName. " are no longer friends :("; 
    $displayAlert = true;
}

if (isset($_POST['addPhoto'])) {
    $pic = ($_FILES['image']['name']);
    $aID = $_POST['albumID'];
    $stmt = mysqli_prepare($connect->link, "INSERT INTO pictures(albumID, prof_id, pic) VALUES (?,?,?)");
    $stmt->bind_param('iis', $aID, $loggedInProfile, $pic);
    $stmt->execute();
    $target = $target. basename($_FILES['image']['name']);

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target)) 
    { 
        $displayAlert = true;
        $alertMessage = "Your picture upload was successful!"; 
    } else { 

        $displayAlert = true;
        $alertMessage = "Sorry, there was a problem uploading your file."; 
    } 
}

if (isset($_POST['addProfPic'])) {
    $pic = ($_FILES['image']['name']);
    $stmt = mysqli_prepare($connect->link, "UPDATE profiles SET Picture = ? WHERE prof_id = $loggedInProfile");
    $stmt->bind_param('s', $pic);
    $stmt->execute();
    $target = $target. basename($_FILES['image']['name']);

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target)) 
    { 
        $displayAlert = true;
        $alertMessage = "Your picture upload was successful!"; 
    } else { 

        $displayAlert = true;
        $alertMessage = "Sorry, there was a problem uploading your file."; 
    } 
}
	

$pictures = $connect->getPictures($profileInView);
$json = $connect->getAutoCompleteEncode($profileInView);
?>
<!DOCTYPE html>
<html>
	<style>
		.ui-helper-hidden-accessible { display:none; }
	</style>
    <head>
		<title>BabyBook</title>
		<link href="bootstrap-3.0.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<body style="padding-top:70px">
	    <nav class="navbar navbar-default navbar-fixed-top" style="background-color: #f2b1ec">
			<div class="navbar-header">
				<a class="navbar-brand" style="color: #1830ab" href="home.php">BabyBook</a>
			</div>
			<div class="nav navbar-nav">
				<li><a href="newsFeed.php" style="color: #1830ab">News Feed</a></li>
			    <li><a href="buddies.php" style="color: #1830ab">Your Buddies</a></li>
			    <li><a href="allbuddies.php" style="color: #1830ab">Find Buddies</a></li>
			</div>
			<div class="nav navbar-nav navbar-right">
			    <li><a href="inbox.php" style="color: #1830ab">Inbox</a></li>
			    <li><a href="albums.php" style="color: #1830ab">Photo Albums</a></li>
			    <li><a href="login.php" style="color: #1830ab">Logout</a></li>
			</div>
			<?php if (!$isCurrentProfile) {?>
			    <p class="navbar-text navbar-right">Signed in as <?php echo $currentProfile->FirstName. " ". $currentProfile->LastName;?></p>
			<?php } else {?>
			    <p class="navbar-text navbar-right">Signed in as <?php echo $inViewProfile->FirstName. " ". $inViewProfile->LastName;?></p>
			<?php }?>
		</nav>
        <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="form-horizontal" method="post" action="inbox.php" name="sendMessageForm" id="sendMessageForm">
                    	<div class="modal-header">
	                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                        <h4 class="modal-title" id="messageModalLabel">Send Message</h4>
                    	</div>
	                    <div class="modal-body">
	                    	<div class="form-group">
	                    		<label for="toRecieve">To: </label>
	                    		<input class="form-control" type="text" name="fullName" id="toRecieve"/>
	                    		<input type="hidden" name="prof_id" id="prof_id"/>
	                    	</div>
	                       	<div class="form-group">
	                       		<label for="message">Message:</label>
	                       		<input class="form-control" name="message" type="textarea" id="message" />
	                        </div>
	                    </div>
	                    <div class="modal-footer">
	                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	                        <input type="submit" name="sendMessage" class="btn btn-success pull-right" value="Send Message">
	                    </div>
                	</form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="profPicModal" tabindex="-1" role="dialog" aria-labelledby="profPicModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                	<div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                	</div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                    	<form enctype="multipart/form-data" action="home.php" method="post" name="addPhotoForm" id="addPhotoForm">
                            <input name="image" type="file" class="button">
                            <button type="submit" class="btn btn-primary pull-left" name="addProfPic" value="Change Photo">Change Photo</button>
                        </form>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
	</body>
	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.9.0/jquery-ui.min.js"></script>
	<script src="bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
	<script language="javascript" type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.min.js"></script>
	<script>
	$(function() {
	    $("#toRecieve").autocomplete({
	        source: <?php echo $json;?>,
	        focus: function(event, ui) {
	            $("#toRecieve").val(ui.item.label);
	            return false;
	        },
	        select: function(event, ui) {
	            $("#toRecieve").val(ui.item.label);
	            $("#prof_id").val(ui.item.value);
	            return false;
	        }
		});
	});
	</script>
</html>
