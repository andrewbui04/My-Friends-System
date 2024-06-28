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
                <li class="active"><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Log In</a></li>
                <li><a href="about.php">About this assignment</a></li>
            </ul>
        </nav>
       
    </div>
  </header>

  <h1 class="header">My Friend System</h1>
  <h1 class="header">Registration Page</h1>
  <?php
  session_start();

  require_once('settings.php');
  $conn = new mysqli($host, $user, $pwd, $dbnm);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  function sanitize_input($input)
  {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
  }

  //Regex for email, profile name, and password
  $emailRegex = "/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/";
  $profileNameRegex = "/^[a-zA-Z ]+$/";
  $passwordRegex = "/^[a-zA-Z0-9]+$/";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    if (isset($_POST['email']) && isset($_POST['profile_name']) && isset($_POST['password']) && isset($_POST['cf_password']) &&
    !empty($_POST['email']) && !empty($_POST['profile_name']) && !empty($_POST['password']) && !empty($_POST['cf_password'])) 
    {
        $email = sanitize_input($_POST['email']);
        $profileName =sanitize_input($_POST['profile_name']);
        $password = sanitize_input($_POST['password']);
        $confirmPassword = sanitize_input($_POST['cf_password']);

        //Store all the error in an array
        $errors = array();

        if (!preg_match($emailRegex, $email)) {
            $errors[] = "Unaccepted email format";
        } else {
            // Check if email already exists in the 'friends' table
            $sql = "SELECT * FROM friends WHERE friend_email='$email'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $errors[] = "Email already exists";
            }
        }

        if (!preg_match($profileNameRegex, $profileName)) {
          $errors[] = "Profile name must contain only letters and spaces";
        }

        if (!preg_match($passwordRegex, $password)) {
          $errors[] = "Password must contain only letters and numbers";
        }

        if ($password != $confirmPassword) {
          $errors[] = "Passwords do not match";
        }
        
        if (empty($errors)) {
            // Add password and server date to 'friends' table
            $startedDate = date("Y-m-d");

            $sql2 = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES ('$email', '$password', '$profileName', '$startedDate', 0)";
            if ($conn->query($sql2) === TRUE) {
                // Set session variable for successful sign-up
                $_SESSION['loginStatus'] = true;
                $_SESSION['email'] = $email;
                header("Location: friendadd.php"); 
                exit();
            } else {
                echo "Inserting error: " . $conn->error;
            }
        } else {
            // Display errors as a message
            echo '<div class="error-box">';
            echo "<p>Errors include:</p>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "</div>";
          
        }
    } else {
        echo "<p style=color:red><strong>All fields are required</strong></p>";
    }
  }  
  ?>


  <div class="Ta1">
    <!-- Form for register account -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <label for="email">Email:</label>
      <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null; ?>"><br><br>

      <label for="profile_name">Profile Name:</label>
      <input type="text" id="profile_name" name="profile_name" value="<?php echo isset($_POST['profile_name']) ? htmlspecialchars($_POST['profile_name']) : null; ?>"><br><br>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password"><br><br>

      <label for="cf_password">Confirm Password:</label>
      <input type="password" id="cf_password" name="cf_password"><br><br>
     
      <input type="submit" value="Register">
      <a href="signup.php" class="clearButton">Clear</a>
    </form>

    <!-- Link to return to Home page -->
    <p><a href="index.php">Return to Home Page</a></p>
        
  </div>
  
</body>

</html>