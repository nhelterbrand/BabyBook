<?php
require_once 'connect.php';

class ConnectTest extends PHPUnit_Framework_TestCase {
     protected $testLink;

     protected function setUp() {
         $this->testLink = new Connect('testbabybook');
         $this->testLink->clearBuddies();
         $this->testLink->insertQuery('test', 'test', 'test@test.com', 'test');
         $this->testLink->insertQuery('test2', 'test2', 'test2@test.com', 'test2');
     }
     
     public function testGetProfile()
     {
         $result = $this->testLink->getProfile(51);
         $this->assertEquals('test', $result->FirstName);
     }
     
     public function testGetProfiles()
     {
         $results[] = $this->testLink->getAllProfiles();
         $this->assertEquals(3, count($results[0]));
     }
     
     public function testInsertQuery()
     {
         $this->testLink->insertQuery('testbob', 'man', 'man@man.com', 'read');
         $results[] = $this->testLink->getAllProfiles();
         $this->assertEquals(3, count($results[0]));
     }
     
     public function testAddFriend()
     {
         $friendCount = count($this->testLink->getFriends(51));
         $this->testLink->addFriend(51,52);
         $results = $this->testLink->getFriends(51);
         $friendCount++;
         $this->assertEquals($friendCount, count($results));
     }
     
     public function testIsFriend()
     {
         $this->testLink->addFriend(51,52);
         $this->assertEquals(true, $this->testLink->isFriend(51,52));
         $this->assertEquals(true, $this->testLink->isFriend(52,51));
         $this->assertEquals(false, $this->testLink->isFriend(51, 57));
     }
     
     public function testRemoveFriend()
     {
         $this->testLink->addFriend(51,52);
         $this->assertEquals(true, $this->testLink->isFriend(51,52));
         $this->testLink->removeFriend(51,52);
         $this->assertEquals(false, $this->testLink->isFriend(51,52));
     }

     public function testGetNonFriends()
     {
        $profileCount = count($this->testLink->getAllNonFriends(51));
        $this->assertEquals($profileCount, count($this->testLink->getAllNonFriends(51)));
        $this->testLink->addFriend(51,57);
        $profileCount--;
        $this->assertEquals($profileCount, count($this->testLink->getAllNonFriends(51)));
     }

     public function testNewsFeed()
     {
        $this->testLink->clearNewsFeed();
        $this->testLink->postStatus(51, "01/01/01", "hello");
        $newsFeed = $this->testLink->getNewsFeed(51);
        $this->assertEquals(1, count($newsFeed));
        $this->testLink->removeStatus($newsFeed[0]->ID);
        $newsFeed = $this->testLink->getNewsFeed(51);
        $this->assertEquals(0, count($newsFeed));
     }

     public function testMessages()
     {
        $this->testLink->clearMessages();
        $this->testLink->sendMessage(51, 52, "wassup");
        $allMessages = $this->testLink->getMessages(52);
        $this->assertEquals(1, count($allMessages));
        $this->testLink->removeMessage($allMessages[0]->ID);
        $allMessages = $this->testLink->getMessages(52);
        $this->assertEquals(0, count($allMessages));
     }
}

