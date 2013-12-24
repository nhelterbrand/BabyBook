<?php
/**
 * register.php registers user into profile MySQL database
 * 
 * PHP Version 5
 * 
 * @category PHP
 * @package  BabyBook
 * @author   Nate Helterbrand <helterb1@illinois.edu>
 * @license  none.com none
 * @link     none
 */
require 'connect.php';
session_start();
$_SESSION['id'] = -1; // set to -1 because no one has logged in yet
$isProblem = false;
$problemMessage = "";

//server-side form verification
if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    
    $connect = new Connect('helterb1_babybook');
    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($password2))
    {
        $problemMessage = "All Fields Must be Filled";
        $isProblem = true;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $problemMessage = "Please Enter a Valid Email Address";
        $isProblem = true;
    } else if ($password != $password2) {
        $problemMessage = "The Passwords Do Not Match";
        $isProblem = true;
    } else {
        $hashed_password = hash("sha256", trim($password));
        $result = $connect->insertQuery($fname, $lname, $email, $hashed_password);
        var_dump($result);
        $result = $connect->selectQuery("SELECT * FROM profiles WHERE Email = '$email'");
        $result = $result[0];
        session_start();
        $_SESSION['id'] = $result->prof_id;
        mysqli_close($connect->link);
        printf("<script>location.href='newsFeed.php'</script>");
    }
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
		        <div class="col-xs-4 col-md-offset-4">
		            <div class="panel panel-default">
        		    <div class="panel-heading">
        		        <h3 class="panel-title">Begin Your BabyBook Adventure!</h3>
        		    </div>
        		    <div class="panel-body">
        		        <div class="col-mid-4">
            		        <form class="form-horizontal" name="register" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="register">
                                <input type="text" class="form-control required" id="fname" placeholder="Your First Name" name="fname">
                                <input type="text" class="form-control required" id="lname" placeholder="Your Last Name" name="lname">
                                <input type="text" class="form-control required email" id="email" placeholder="Your Email" name="email">
                                <br/>
                                <input type="password" class="form-control required" id="password" placeholder="Password" name="password">
                                <input type="password" class="form-control required" equalTo="#password" id="password2" placeholder="Re-enter Password" name="password2">
                                <div class="form-footer">
                                     <button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>
                                </div>
                            </form>
                            <?php if ($isProblem) {?>
                		    <div class="alert alert-danger alert-dismissable">
                		        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <strong>Warning!</strong> <?php echo $problemMessage;?>
                		    </div>
                		    <?php }?>
        		        </div>
        		    </div>
        		    </div>
		        </div>
            </div>
		</div>
	</body>
	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
	<script language="javascript" type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.min.js"></script>
	<script>
    $("#register").validate();
	</script>
</html>
