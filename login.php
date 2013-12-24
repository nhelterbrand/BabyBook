<?php
/**
 * login.php used to login already registered users
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

$connect = new Connect('helterb1_babybook');
session_start();
$_SESSION['id'] = -1; //set to -1 meaning no one is logged in yet
$link = $connect->link;
$problemMessage = "";
$isProblem = false;
$showRegisterPrompt = false;

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $email = mysqli_escape_string($link, $email);
    $query = "SELECT Password, prof_id FROM profiles WHERE Email = '$email';";
    $result = $connect->selectQuery($query);
    
    //if there is no result than there is a problem, otherwise normal login process continues
    if (!$result) {
        $isProblem = true;
        $showRegisterPrompt = true;
        $problemMessage = "Profile does not exist";
    } else {
        $result = $result[0];
        $hashed_password = hash("sha256", trim($password));
        $hashed_password = substr($hashed_password, 0, 60);
        
        if ($result->Password != $hashed_password) {
            $isProblem = true;
            $problemMessage = "Invalid Password";
        } else {
            session_start();
            $_SESSION['id'] = $result->prof_id;
            //header('Location: newsFeed.php');
            printf("<script>location.href='newsFeed.php'</script>");
        }
    }
    mysqli_close($connect->link);
    
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>BabyBook</title>
		<link href="bootstrap-3.0.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<body>
		<div class="container">
		    <div class="row">
		        <div class="col-xs-4 col-md-offset-4">
		            <div class="panel panel-default">
        		    <div class="panel-heading">
        		        <h3 class="panel-title">Login Here</h3>
        		    </div>
        		    <div class="panel-body">
        		        <div class="col-mid-4">
            		        <form class="form-horizontal" id="login" name="login" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    			<input type="text" class="form-control required email" name="email" id="email" placeholder="Enter Email Address">
                    			<input type="password" class="form-control required" name="password" id="password" placeholder="Enter Password">
                    			<button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>
                    		</form>
        		        </div>
        		    </div>
        		    <?php if ($isProblem) {?>
        		    <div class="alert alert-danger alert-dismissable">
        		        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>Warning!</strong> <?php echo $problemMessage;?>
                        <?php if ($showRegisterPrompt) {?>
                            <a href="register.php">Click Here to Sign Up</a>
                        <?php }?>
        		    </div>
        		    <?php }?>
        		    </div>
		        </div>
            </div>
		</div>
	</body>
	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
	<script language="javascript" type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.min.js"></script>
	<script>
    $("#login").validate();
	</script>
</html>
