<!DOCTYPE html>
<?php
    //login page takes no inputs
    
    session_start();
    include "functions.php";

    //connect to SQL server
    $conn = sql_connect();

    //create variables for each field
    $username = "";
    $password = "";

    //login error message
    $loginErr = "";

    //if forms are submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        //validate data
        $username = input_validation($_POST["username"]);
        $password = input_validation($_POST["password"]);

        //query for any answers with this username/pw combo
        $sql = "SELECT * FROM Users WHERE username='".$username."' AND pw='".$password."'";
        $result = $conn->query($sql);

        //if this username/pw combo exists in the DB, create session variable username and go to home page
        if ($result->num_rows > 0) {
            $_SESSION["user"] = $username;
            header("Location: /index.php");
        }
        //if no results, give error message
        else {
            $loginErr = "There was something wrong with your username or password.";
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
    <h4>LOGIN:</h4>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Userame: <input type="text" name="username"><br>
        Password: <input type="text" name="password"><br>
        <span class="error"><?php echo $loginErr;?></span><br>
        <input type="submit" style="font-size:30px;height:50px;width:140px">
    </form>
</div>
</html>