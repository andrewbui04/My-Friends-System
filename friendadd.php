<?php
session_start();

// Check the login status -> If user not log in yet -> Redirect to log in page
if (!isset($_SESSION['loginStatus']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

require_once('settings.php');
$conn = new mysqli($host, $user, $pwd, $dbnm);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get profile name and number of friends of the logged-in user
$email = $_SESSION['email'];
$sql1 = "SELECT profile_name, num_of_friends, friend_id FROM friends WHERE friend_email='$email'";
$result1 = $conn->query($sql1);
$row = $result1->fetch_assoc();
$profileName = $row['profile_name'];
$numOfFriend = $row['num_of_friends'];
$userId = $row['friend_id'];  //Also get the friend id of logged-in user for function add_friend()


//Create function for adding friends
function add_friend($friendsId)
{
    global $conn, $userId, $numOfFriend;
    //Add friends from table 'myfriends'
    $addFriend = "INSERT INTO myfriends (friend_id1, friend_id2) 
                  VALUES ($userId, $friendsId)";
    if ($conn->query($addFriend)){
      // Update the friend's number of logged-in user after adding someone
      $numOfFriend += 1;
      $updateFriend = "UPDATE friends SET num_of_friends = $numOfFriend WHERE friend_id = $userId";
      $conn->query($updateFriend);

      // Update friend's number of the added friend from friends table
      $getFriendNum = "SELECT num_of_friends
                      FROM friends
                      WHERE friend_id = '$friendsId'";
      $getFriendNumResult = $conn->query($getFriendNum);
      $row = $getFriendNumResult->fetch_assoc();
      $newNumOfFriends = $row["num_of_friends"];

      // Update the new friend number of added friend
      $newNumOfFriends += 1;
      $updateFriendNum = "UPDATE friends
                          SET num_of_friends = '$newNumOfFriends' WHERE friend_id = '$friendsId'";
      $conn->query($updateFriendNum);
    }
    
}

//Check if logged-in user click the addfriend button
if (isset($_POST['addfriend'])) {
    add_friend($_POST["friendsId"]);
    // Reset friendadd page after add friend with someone
    header("Location: friendadd.php");
    exit();
}

// Pagination function
$friendPerPage = 5;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
}else{
    $page = 1;
}

// Calculate previous and next page
$previousPage = $page - 1;
if ($previousPage < 1) {
    $previousPage = 1; 
}
$nextPage = $page + 1;

//Create an offset to retrieve records from table
$startingPoint = ($page - 1)*$friendPerPage;

// Retrieve all registered users who are not friends of the logged-in user (display only 5 friends per page)
$sql2 = "SELECT *
         FROM friends
         WHERE friend_id != $userId
         AND friend_id NOT IN ( 
             SELECT friend_id1 FROM myfriends WHERE friend_id2 = $userId
             UNION
             SELECT friend_id2 FROM myfriends WHERE friend_id1 = $userId)  
             LIMIT $startingPoint, $friendPerPage";

$result2 = $conn->query($sql2);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COS30020 - Assignment 2</title>
  <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body>
  <header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Log In</a></li>
                <li><a href="about.php">About this assignment</a></li>
            </ul>
        </nav>
       
    </div>
  </header>

  <h1 class="header">My Friend System</h1>
  <h1 class="header"><?php echo htmlspecialchars($profileName); ?>'s Add Friend Page</h2>
  <h1 class="header">Total number of friends is: <?php echo($numOfFriend); ?></h2>

    <table>
        <thead>
            <tr>
                <th>People you may know</th>
                <th>Mutual friends</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result2->num_rows > 0) {
                    while ($row = $result2->fetch_assoc()) {
                        $friendsId = $row["friend_id"];
                        // Display number of mutual friends 
                        $mutualFriendsNum = array();
                        $selectMutualFriend = "SELECT COUNT(*) AS num_of_mutual_friends
                                    FROM myfriends AS friend1 
                                    JOIN myfriends AS friend2 ON friend1.friend_id2 = friend2.friend_id1
                                    WHERE friend1.friend_id1 = $userId AND friend2.friend_id2 = $friendsId";
                        $mutualFriendResult = $conn->query($selectMutualFriend);
                        $mutualFriendRow = $mutualFriendResult->fetch_assoc();
                        $mutualFriendsNum[$friendsId] = $mutualFriendRow['num_of_mutual_friends'];
                        $mutualFriends = isset($mutualFriendsNum[$friendsId]) ? $mutualFriendsNum[$friendsId] : 0;
                        echo "<tr>";
                        echo "<td>". $row['profile_name']. "</td>";
                        echo "<td>". "<p>". $mutualFriends. " mutual friends</p>" . "</td>";
                        echo "<td>
                        <form method='POST' action='friendadd.php'>
                            <input type='hidden' name='friendsId' value='{$friendsId}'>
                            <input class='addfriendButton' type='submit' name='addfriend' value='Add friend'>
                        </form></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<p style=color:#c21807><strong>You do not have any friends! Let's add friend!</strong></p>";
                }
                $conn->close();
            ?>
        </tbody>
    </table>

    <div class="paginationFriend">
        <?php if ($page > 1): ?>
            <a href="friendadd.php?page=<?php echo $previousPage; ?>" class="previousButton">Previous</a>
        <?php endif; ?>
        <?php if ($result2->num_rows == $friendPerPage): ?>
            <a href="friendadd.php?page=<?php echo $nextPage; ?>" class="nextButton">Next</a>
        <?php endif; ?>
    </div>

    <div class="Ta1">
        <a href="friendlist.php" class="clearButton">Friend Lists</a>
        <a href="logout.php" class="clearButton">Log Out</a>
    </div>

</body>

</html>