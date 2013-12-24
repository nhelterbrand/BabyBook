<?php
/**
 * connect.php connects the page to the mysql database
 *
 * PHP Version 5
 *
 * @category PHP
 * @package  BabyBook
 * @author   Nate Helterbrand <helterb1@illinois.edu>
 * @license  none.com none
 * @link     none
 */

/**
 * Connect class creates the database for BabyBook to use
 * 
 * @author Nate Helterbrand
 *
 */
class Connect
{
    public $link;
    /**
     * constructor for Connect object
     */
    public function __construct($database)
    {           
        $this->link = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu","helterb1_newuser","newuser",$database);
    }
    /**
     * insertQuery for insert strings
     *
     * @param String $value of what to query
     *
     * @return resource returns MySQL result
     */
    function insertQuery($fname, $lname, $email, $password)
    {
        $query = "INSERT INTO profiles(FirstName, LastName, Email, Password)VALUES(?,?,?,?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("ssss", $fname, $lname, $email, $password);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * select query for select statements
     *
     * @param String $value of what to query
     *
     * @return resource returns MySQL result
     */
    function selectQuery($value)
    {
        $result = mysqli_query($this->link, $value);
        $results = null;
	while ($obj = mysqli_fetch_object($result)) {
		$results[] = $obj;
	}
	return $results;
    }
    
    /**
    * insertQuery for insert strings
    *
    * @param String $prof_id of what to query
    *
    * @return resource returns MySQL result
    */
    function getProfile($prof_id)
    {
        $query = "SELECT * FROM profiles WHERE prof_id = '$prof_id'";
        $result = $this->selectQuery($query);
        return $result[0];
    }
    
    /**
    * insertQuery for insert strings
    *
    * @return resource returns MySQL result
    */
    function getAllProfiles()
    {
        $query = "SELECT * FROM profiles";
        $results = $this->selectQuery($query);
        return $results;
    }
    
    /**
     * function that shows all Profiles that aren't friends
     * 
     * @param  $prof_id is the id of the current Profile
     * 
     * @return resource returns MySQL result
     */
    function getFriends($prof_id)
    {
        $queryProfiles = "SELECT * FROM profiles WHERE prof_id IN (SELECT friend_id FROM buddies WHERE prof_id = $prof_id)";
        $results = $this->selectQuery($queryProfiles);
        return $results;
    }

    /**
    * function that returns all profiles that aren't friends with the current profile
    *
    * @param $prof_id of the ID to query against
    *
    * @return array of profiles
    */
    function getAllNonFriends($prof_id)
    {
        $allProfiles = $this->getAllProfiles();
        $results = array();
        foreach ($allProfiles as $profile)
        {
            if (!$this->isFriend($prof_id, $profile->prof_id) && $prof_id != $profile->prof_id) {
                array_push($results, $this->getProfile($profile->prof_id));
            }
        }
        return $results;
    }
    
    /**
     * sees whether there is a friend relationship between two Id's
     * 
     * @param prof_id $idOne
     * @param prof_id $idTwo
     * @return boolean
     */
    function isFriend($idOne, $idTwo)
    {
        $query = "SELECT * FROM buddies WHERE ((prof_id = $idOne AND friend_id = $idTwo) OR (prof_id = $idTwo AND friend_id = $idOne))";
        $result = mysqli_query($this->link, $query);
        $numResults = mysqli_num_rows($result);
        return ($numResults == 2);
    }
    
    /**
     * add Friend relationship between two profiles
     * 
     * @param $idOne id of first Profile
     * @param $idTwo id of second Profile
     */
    function addFriend($idOne, $idTwo)
    {
        $query = "INSERT INTO buddies(prof_id, friend_id) VALUES ($idOne,$idTwo)";
        $result = mysqli_query($this->link, $query);
        $queryTwo = "INSERT INTO buddies(prof_id, friend_id) VALUES ($idTwo,$idOne)";
        $resultTwo = mysqli_query($this->link, $queryTwo);
    }

    /**
     * clears buddies Database
     * 
     */
    function clearBuddies()
    {
        $query = "TRUNCATE TABLE buddies";
        $result = mysqli_query($this->link, $query);
    }
    
    /**
     * removes Friend relationship between two profiles
     * 
     * @param $idOne id of First Profile
     * @param $idTwo id of Second Profile
     */
    function removeFriend($idOne, $idTwo)
    {
        $query = "DELETE FROM buddies WHERE prof_id = $idOne AND friend_id = $idTwo";
        $result = mysqli_query($this->link, $query);
        $queryTwo = "DELETE FROM buddies WHERE prof_id = $idTwo AND friend_id = $idOne";
        $resultTwo = mysqli_query($this->link, $queryTwo);
    }
    
    /**
     * gets Pictures
     * 
     * @param album_id $album_id
     */
    function getPictures($album_id)
    {
        $queryProfiles = "SELECT pic FROM pictures WHERE albumID  = $album_id";
        $results = $this->selectQuery($queryProfiles);
        return $results;
    }

    /**
     * gets Pictures
     * 
     * @param prof_id $prof_id
     */
    function getNewsFeed($prof_id)
    {
        $query = "SELECT * FROM status WHERE prof_id IN 
                    (SELECT friend_id FROM buddies WHERE prof_id = $prof_id) OR prof_id = $prof_id
                    ORDER BY Date DESC";
        $results = $this->selectQuery($query);
        return $results;
    }

    /**
     * clears status Database
     * 
     */
    function clearNewsFeed()
    {
        $query = "TRUNCATE TABLE status";
        $result = mysqli_query($this->link, $query);
    }

    /**
     * posts status for current user
     * 
     * @param prof_id $prof_id
     * @param string  $message
     */
    function postStatus($prof_id, $status)
    {
    	$date = date('m/d/Y h:i:s a', time());
        $query = "INSERT INTO status(prof_id, Date, Status)VALUES(?,?,?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("iss",$prof_id, $date, $status);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * sends Message between the two Id's
     *
     * @param $date the current timestamp of message
     * @param $prof_id the person sending the message
     * @param $friend_id the person recieving the message
     * @param $message the message text actually being sent
     */
    function sendMessage($prof_id, $friend_id, $message)
    {
        $date = date('m/d/Y h:i:s a', time());
        $query = "INSERT INTO messages(Date, prof_id, friend_id, Message) VALUES (?,?,?,?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("siis", $date, $prof_id, $friend_id, $message);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * removes Message between the two Id's
     *
     * @param $id ID of message
     */
    function removeMessage($id)
    {
        $query = "DELETE FROM messages WHERE ID = $id";
        $result = mysqli_query($this->link, $query);
    }

    /**
     * removes status post for current user
     *
     * @param ID of message
     */
    function removeStatus($id)
    {
        $query = "DELETE FROM status WHERE ID = $id";
        $result = mysqli_query($this->link, $query);
    }

    /**
     * gets all inbox messages for a user
     *
     * @param $prof_id of inbox to retrieve
     */
    function getMessages($prof_id)
    {
        $query = "SELECT * FROM messages WHERE friend_id = $prof_id ORDER BY ID DESC";
        $results = $this->selectQuery($query);
        return $results;
    }
    
    /**
     * adds a like to a status
     *
     * @param $status_id id of status thats getting the like
     * @param $prof_id id of person liking status
     */
    function addLike($status_id, $prof_id) {
        $query = "INSERT INTO likes(status_id, prof_id) VALUES (?, ?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("ii",$status_id, $post_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * removes a like from a status
     *
     * @param $status_id id of status that has the like
     * @param $prof_id id of person removing like
     */
    function removeLike($status_id, $prof_id) {
    	$query = "DELETE FROM likes WHERE status_id = $status_id AND prof_id = $prof_id";
        $result = mysqli_query($this->link, $query);
    }
    
    /**
     * determines whether status is liked or not
     *
     * @param $status_id of status
     * @param $prof_id of person viewing page
     */
    function isLiked($status_id, $prof_id)
    {
        $query = "SELECT * FROM likes WHERE (prof_id = $prof_id AND status_id = $status_id)";
        $result = mysqli_query($this->link, $query);
        $numResults = mysqli_num_rows($result);
        return ($numResults == 2);
    }
    
    /**
     * posts a comment to a status
     *
     *
     */	
    function postComment($parent_id, $prof_id, $message) {
    	$date = date('m/d/Y h:i:s a', time());
        $query = "INSERT INTO comments(parentID, prof_id, message, Date) VALUES (?,?,?,?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("iiss", $parent_id, $prof_id, $message, $date);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * gets comments for a status
     *
     *
     */
    function getComments($parent_id) {
    	$query = "SELECT * FROM comments WHERE parentID = $parent_id ORDER BY Date ASC";
        $results = $this->selectQuery($query);
        return $results;
    }

    /**posts a comment to a status
     * clears messages Database
     * 
     */
    function clearMessages()
    {
        $query = "TRUNCATE TABLE messages";
        $result = mysqli_query($this->link, $query);
    }
    
    /**
     * gets albums for that profile
     *
     *
     */
    function getAlbums($prof_id) {
    	$query = "SELECT * FROM photoalbums WHERE prof_id = $prof_id";
        $results = $this->selectQuery($query);
        return $results;
    }
    
   /**
     * gets one album for that profile
     *
     *
     */
    function getAlbum($album_id) {
    	$query = "SELECT * FROM photoalbums WHERE ID = $album_id";
        $results = $this->selectQuery($query);
        return $results;
    }
    
    /**
     * creates an album for that profile
     *
     *
     */
    function createAlbum($prof_id, $name) {
    	$query = "INSERT INTO photoalbums(prof_id, albumName) VALUES (?,?)";
        if ($stmt = $this->link->prepare($query))
        {
            $stmt->bind_param("is", $prof_id, $name);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * writes all the friends of that prof_id into json format for jquery autocomplete
     *
     * @param $prof_id of profile to search with
     */
    function getAutoCompleteEncode($prof_id)
    {
        $results = $this->getFriends($prof_id);
        $toReturn = array();
        if (!$results) return $toReturn;
        foreach ($results as $result)
        {
            $toReturn[] = array(
                'label' => $result->FirstName." ".$result->LastName,
                'value' => $result->prof_id
            );
        }
        
        return json_encode($toReturn);
    }
}

session_start();
?>
<!DOCTYPE html>
<html>
    <head>
		<title>BabyBook</title>
		<link href="bootstrap-3.0.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<body style="padding-top:70px">
	    <nav class="navbar navbar-default navbar-fixed-top" style="background-color: #f2b1ec">
			<div class="navbar-header">
				<a class="navbar-brand" style="color: #1830ab" href="home.php">BabyBook</a>
			</div>
			<div class="nav navbar-nav navbar-right">
			    <li><a href="login.php" style="color: #1830ab">Login</a></li>
			</div>
			<div class="nav navbar-nav navbar-right">
			    <li><a href="register.php" style="color: #1830ab">Sign Up</a></li>
			</div>
		</nav>
	</body>
</html>