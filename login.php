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
                <li class="active"><a href="login.php">Log In</a></li>
                <li><a href="about.php">About this assignment</a></li>
            </ul>
        </nav>
       
    </div>
  </header>

  <h1 class="header">My Friend System</h1>
  <h1 class="header">Login Page</h1>

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

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    if (isset($_POST['email'])&& isset($_POST['password']))
    {
      $email = sanitize_input($_POST['email']);
      $password = sanitize_input($_POST['password']);
      //Store all the error in an array
      $errors = array();
      if (empty($email) && empty($password)){
        $errors[] = "All fields cannot be empty!";
      }else{
        // Check whether input email exists in the 'friends' table
        $sql = "SELECT * FROM friends WHERE friend_email='$email'";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) {
            $errors[] = "Cannot found your email";
        }else{
          //If email found->Compare the input password with password in database
          $row = $result->fetch_assoc();
          if ($password !== $row['password']) {
              $errors[] = "Password not match!";
          }else{
            $_SESSION['loginStatus'] = true;
            $_SESSION['email'] = $email;
            header("Location: friendlist.php"); 
            exit();
          }
        }
      }  

      if (!empty($errors)){
        echo '<div class="error-box">';
        echo "<p><strong>Errors include:</strong></p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "</div>";
      } 
    }
  }  
  ?>

  <div class="Ta1">
    <!-- Form for login account -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <label for="email">Email:</label>
      <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null; ?>"><br><br>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password"><br><br>
     
      <input type="submit" value="Log in">
      <a href="login.php" class="clearButton">Clear</a>
    </form>

    <!-- Link to return to Home page -->
    <p><a href="index.php">Return to Home Page</a></p>
        
  </div>
  
</body>

</html>