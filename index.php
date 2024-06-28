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
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Log In</a></li>
                <li><a href="about.php">About this assignment</a></li>
            </ul>
        </nav>
       
    </div>
  </header>

  <h1 class="header">My Friend System</h1>
  <h1 class="header">Assignment Homepage</h1>
  

  <div class="homepage_container">
        <div class="information">
            <h2>Name: Bui Thai Anh</h2>
            <h2>Student ID: 104221643</h2>
            <h3>Email: <a href="104221643@student.swin.edu.au">104221643@student.swin.edu.au</a></h3>
            <p><em>I declare that this assignment is my individual work. I have not worked collaboratively, nor have I
                copied from any other student’s work or from any other source</em></p>
          <?php
              require_once('settings.php');

              $conn = new mysqli($host, $user, $pwd, $dbnm);
              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              }
              
              //Create the table 'friends' if it is not exist
              $sql1 = "CREATE TABLE IF NOT EXISTS friends (
                friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                friend_email VARCHAR(50) NOT NULL,
                password VARCHAR(20) NOT NULL,
                profile_name VARCHAR(30) NOT NULL,
                date_started DATE NOT NULL,
                num_of_friends INT UNSIGNED)";
              
              //Create table 'myfriends' if it is not exist
              $sql2 = "CREATE TABLE IF NOT EXISTS myfriends (
                friend_id1 INT NOT NULL,
                friend_id2 INT NOT NULL,
                FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
                FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
                PRIMARY KEY (friend_id1, friend_id2))";

              //Check query of creating ''friends' and 'myfriends' table -> If successful -> Insert data to these tables
              if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE){
                $sql3 = "SELECT * FROM friends";
                $sql4 = "SELECT * FROM myfriends";
                $result1 = mysqli_query($conn, $sql3);
                $result2 = mysqli_query($conn, $sql4);
                if ((mysqli_num_rows($result1) > 0) && (mysqli_num_rows($result2) > 0)) {
                  echo "<p style=color:red>Table 'friends' and 'myfriends' already has records.</p>";
                } else {
                  //Populate table 'friends' with 10 sample records + table 'myfriends' with 20 records
                  $sql5 = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends)
                  VALUES
                    ('messi@gmail.com', 'password1', 'Lionel Messi', '2021-01-26', 4),
                    ('ronaldo@gmail.com', 'password2', 'Cristiano Ronaldo', '2022-02-26', 4),
                    ('neymar@gmail.com', 'password3', 'Neymar JR', '2023-03-26', 4),
                    ('kubo@gmail.com', 'password4', 'Takefusa Kubo', '2021-04-26', 4),
                    ('leekangin@gmail.com', 'password5', 'Lee Kang In', '2022-05-26', 4),
                    ('chanathip@gmail.com', 'password6', 'Chanathip Songkrasin', '2023-06-26', 4),
                    ('shintaeyoung@gmail.com', 'password7', 'Shin Tae Young', '2021-07-26', 4),
                    ('trausier@gmail.com', 'password8', 'Phillipe Trausier', '2022-08-26', 4),
                    ('parkhangseo@gmail.com', 'password9', 'Park Hang Seo', '2023-09-26', 4),
                    ('beckham@gmail.com', 'password10', 'David Beckham', '2023-10-26', 4)";
                  
                  $sql6 = "INSERT INTO myfriends (friend_id1, friend_id2)
                            VALUES
                              (1, 2),
                              (2, 3),
                              (3, 4),
                              (4, 5),
                              (5, 6),
                              (6, 7),
                              (7, 8),
                              (8, 9),
                              (9, 10),
                              (10, 1),
                              (1, 3),
                              (2, 4),
                              (3, 5),
                              (4, 6),
                              (5, 7),
                              (6, 8),
                              (7, 9),
                              (8, 10),
                              (9, 1),
                              (10, 2)";

                  //Check if inserting queries successful
                  if (($conn->query($sql5) === TRUE) && ($conn->query($sql6) === TRUE)){
                    echo "<p style=color:green>Table successfully created and populated.</p>";
                  }
                }
              }else{
                echo "<p style=color:red>Error creating tables: " . $conn->error . "</p>";
              }
              // Close database connection
              $conn->close();
          ?>
        </div>
    
  </div>
  

  
</body>

</html>