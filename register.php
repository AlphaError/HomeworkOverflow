<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";

    //connect to SQL server
    $conn = sql_connect();

    //create variables for each field
    $username = "";
    $email = "";
    $pw = "";
    $profile = "";
    $city = "";
    $state = "";
    $country = "";

    //error message variables
    $nameErr = "";
    $emailErr = "";
    $pwErr = "";

    //boolean to see if all forms are filled correctly
    $valid = true;

    //if forms are submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        //USERNAME CHECKS
        //ensure username is not empty
        if (empty($_POST["username"])) {
            $nameErr = "Username is required";
            $valid = false;
        //username should be at least 3 characters long
        } else if(strlen($_POST["username"])<3){
            $nameErr = "Username must be at least 3 characters long";
            $valid = false;
        } else {
            $username = input_validation($_POST["username"]);
            //check if username is only alphanumerics
            if (!ctype_alnum($username)) {
                $nameErr = "No special characters are allowed";
                $valid = false;
            }
        }

        //EMAIL CHECKS
        //email cannot be empty
        if (empty($_POST["email"])){
            $emailErr = "email is required";
            $valid = false;
        } else {
            $email = input_validation($_POST["email"]);
            //check if this is actually an email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $valid = false;
            }
        }

        //PASSWORD CHECKS
        //pw cannot be empty
        if (empty($_POST["pw"])){
            $pwErr = "Password is required";
            $valid = false;
        }
        //pw must be 6 characters or longer 
        else if (strlen($_POST["pw"])<6){
            $pwErr = "Password must be at least 6 characters long";
            $valid = false;
        } else {
            $pw = input_validation($_POST["pw"]);
        }

        //PROFILE CHECKS
        if(empty($_POST["profile"])){
            $profile = "";
        } else {
            $profile = input_validation($_POST["profile"]);
        }

        //CITY
        if(empty($_POST["city"])){
            $city = "";
        } else {
            $city = input_validation($_POST["city"]);
        }

        //STATE
        if(empty($_POST["state"])){
            $state = "";
        } else {
            $state = input_validation($_POST["state"]);
        }

        //CITY
        if(empty($_POST["country"])){
            $country = "";
        } else {
            $country = input_validation($_POST["country"]);
        }

        //if forms are all filled correctly
        if($valid){
            //query to see if username is taken
            $sql = "SELECT * FROM Users WHERE username='".$username."'";
            $result = $conn->query($sql);

            //if this username is taken, give error message
            if($result->num_rows > 0){
                $nameErr = "username is taken";
            } 
            //create account
            else {
                console_debug("creating account...");
                $sql = "INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES 
                    ('".$username."', '".$pw."', '".$email."', '".$city."', '".$state."', '".$country."', '".$profile."')";
                $conn->query($sql);

                //DEBUG: check if actually inserted
                $sql = "SELECT * FROM Users WHERE username='".$username."'";
                $result = $conn->query($sql);

                if($result->num_rows > 0){
                    console_debug("created successfully");
                } else {
                    console_debug("account creation failed! Please try again");
                }
                
                //DEBUG: list all usernames
                $sql = "SELECT username FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        console_debug($row['username']);
                    }
                }

                //set session user
                $_SESSION["user"] = $username;

                //redirect to home page
                header("Location: /index.php");
            }
        }
    }
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: "Lato", sans-serif;
        }
        .sidenav {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 20px;
        }
        .sidenav a {
            padding: 6px 8px 6px 16px;
            text-decoration: none;
            font-size: 24px;
            color: #818181;
            display: block;
        }
        .sidenav a:hover {
            color: #f1f1f1;
        }
        .main {
            margin-left: 210px; /* Same as the width of the sidenav */
            font-size: 28px; /* Increased text to enable scrolling */
            padding: 0px 10px;
        }
        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
            .sidenav a {font-size: 18px;}
        }
    </style>
</head>
<div class="sidenav">
    <?php
    echo "<br><a href=index.php>Home</a><br>";
    echo "<a href='browse.php?cat='>Browse</a><br>";
    echo "<a href='search.php?keywords='>Search</a><br>";
    if($_SESSION["user"] == ""){
        echo "<a href='login.php'>Login</a><br>";
        echo "<a href='register.php'>Create Account</a>";
    } else {
        echo "<a href='profile.php?u=". $_SESSION["user"] ."'>View Profile</a><br>";
        echo "<a href='logout.php'>Logout</a><br>";
    }
    ?>
</div>

<div class="main">
    <h1>Homework Overflow</h1>
    <style>
        .error {color: #FF0000;}
    </style>
    <h4>CREATE ACCOUNT</h4>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Userame: <input type="text" name="username">
        <span class="error">* <?php echo $nameErr;?></span><br>

        Email Address: <input type="text" name="email">
        <span class="error">* <?php echo $emailErr;?></span><br>

        Password: <input type="text" name="pw">
        <span class="error">* <?php echo $pwErr;?></span><br>

        Profile: <textarea name="profile" rows="5" cols="40"></textarea><br>

        City: <input type="text" name="city"><br>

        State: <input type="text" name="state"><br>

        Country: <input type="text" name="country"><br>

        <br><span class="error">* required</span><br><br>

        <input type="submit" style="font-size:30px;height:50px;width:140px">
    </form>
</div>
</html

