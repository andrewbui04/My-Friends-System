<?php
session_start();

// Check the login status -> If user not log in yet -> Redirect to login page
if (!isset($_SESSION['loginStatus']) || !isset($_SESSION['email']) || $_SESSION['loginStatus'] !== true) {
    header("Location: login.php");
    exit;
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
$userId = $row['friend_id'];  //Also get the friend id of logged-in user for function Unfriend

// Get list of current friends of logged-in user
$sql2 = "SELECT DISTINCT friends.*
         FROM myfriends 
         JOIN friends ON (myfriends.friend_id2 = friends.friend_id OR myfriends.friend_id1 = friends.friend_id)
         WHERE (myfriends.friend_id1 = $userId OR myfriends.friend_id2 = $userId)
            AND friends.friend_id != $userId
         ORDER BY friends.profile_name";

$result2 = $conn->query($sql2);

//Create Unfriend function
function delete_friend($friendsId)
{
    global $conn, $userId, $numOfFriend;
    $deleteFriend = "DELETE FROM myfriends WHERE (friend_id1 = $userId AND friend_id2 = $friendsId) OR (friend_id1 = $friendsId AND friend_id2 = $userId)";
    if ($conn->query($deleteFriend) === TRUE) {
        // Update friends's number of logged-in user in friends table
        $numOfFriend -= 1;
        $updateFriend = "UPDATE friends SET num_of_friends = $numOfFriend WHERE friend_id = $userId";
        $conn->query($updateFriend);

        //Update the friend's number of deleted friend from table 'friends'
        $deletedFriendNum = "SELECT num_of_friends
                            FROM friends
                            WHERE friend_id = '$friendsId'";
        $deletedFriendNumResult = $conn->query($deletedFriendNum);
        $row = $deletedFriendNumResult->fetch_assoc();
        $newNumOfFriend = $row['num_of_friends'];

        // Update the new friend number of deleted friend 
        $newNumOfFriend -= 1;
        $updateFriendNum = "UPDATE friends
                            SET num_of_friends = '$newNumOfFriend' WHERE friend_id = '$friendsId'";
        $conn->query($updateFriendNum);
    }
}

//Check if logged-in user click the Unfriend button
if (isset($_POST['unfriend'])) {
    delete_friend($_POST["friendsId"]);
    // Reset friendlist page after unfriend someone
    header("Location: friendlist.php");
    exit();
  }
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
  <h1 class="header"><?php echo htmlspecialchars($profileName); ?>'s Friend List Page</h2>
  <h1 class="header">Total number of friends is: <?php echo($numOfFriend); ?></h2>

    <table>
        <thead>
            <tr>
                <th>Your Friend</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result2->num_rows > 0) {
                    while ($row = $result2->fetch_assoc()) {
                        $friendsId = $row["friend_id"];
                        echo "<tr>";
                        echo "<td>". $row['profile_name']. "</td>";
                        echo "<td>
                        <form method='POST' action='friendlist.php'>
                            <input type='hidden' name='friendsId' value='{$friendsId}'>
                            <input class='unfriendButton' type='submit' name='unfriend' value='Unfriend'>
                        </form></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<p style=color:#c21807><strong>You do not have any friends! Let's add friend first!</strong></p>";
                }
            ?>
        </tbody>
    </table>
  <div class="Ta1">
      <a href="friendadd.php?page=1" class="clearButton">Add Friends</a>
      <a href="logout.php" class="clearButton">Log Out</a>
  </div>

</body>

</html>