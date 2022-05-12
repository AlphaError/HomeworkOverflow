<!DOCTYPE html>
<?php
    //account creation page takes no inputs

    session_start();
    include "functions.php";

    //connect to SQL server
    $conn = sql_connect();

    //query for the current user info
    $sql = "SELECT * FROM Users WHERE username='{$_GET["u"]}'";
    $result = $conn->query($sql)->fetch_assoc();

    $profile = $result["pf"];
    $email = $result["email"];
    $city = $result["city"];
    $state = $result["state"];
    $country = $result["country"];

    //error messages
    $emailErr = "";

    //if new info is submitted, aka variables are present in URL
    if(isset($_GET["email"])){

        //if new email provided, update email in DB
        if(!empty($_GET["email"])){
            $email = input_validation($_GET["email"]);
            //check if this is actually an email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            }
            //SQL query
            $sql = "UPDATE Users SET email='{$email}' WHERE username='{$_GET["u"]}'";
            $conn->query($sql);
        }

        //if new profile info provided, update profile in DB
        if(!empty($_GET['profile'])){
            $profile = input_validation($_GET["profile"]);
            //SQL query
            $sql = "UPDATE Users SET pf='{$profile}' WHERE username='{$_GET["u"]}'";
            $conn->query($sql);
        }

        //if new city info provided, update profile in DB
        if(!empty($_GET['city'])){
            $city = input_validation($_GET["city"]);
            //SQL query
            $sql = "UPDATE Users SET city='{$city}' WHERE username='{$_GET["u"]}'";
            $conn->query($sql);
        }

        //if new state info provided, update profile in DB
        if(!empty($_GET['state'])){
            $state = input_validation($_GET["state"]);
            //SQL query
            $sql = "UPDATE Users SET state='{$state}' WHERE username='{$_GET["u"]}'";
            $conn->query($sql);
        }

        //if new country info provided, update profile in DB
        if(!empty($_GET['country'])){
            $country = input_validation($_GET["country"]);
            //SQL query
            $sql = "UPDATE Users SET country='{$country}' WHERE username='{$_GET["u"]}'";
            $conn->query($sql);
        }

        //link back to profile page
        header("location: profile.php?u={$_GET['u']}");
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
            width: 220px;
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
            margin-left: 230px; /* Same as the width of the sidenav */
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
        echo "<a href='post.php'>Post a Question</a><br>";
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
    <h4>Edit Profile Information</h4>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

        <input type="hidden" name="u" value="<?php echo $_GET["u"];?>">

        Email Address: <input type="text" name="email" placeholder="<?php echo $email;?>">
        <span class="error"><?php echo $emailErr;?></span><br>

        Profile: <textarea name="profile" rows="5" cols="40"  placeholder="<?php echo $profile;?>"></textarea><br>

        City: <input type="text" name="city" placeholder="<?php echo $city;?>"><br>

        State: <input type="text" name="state" placeholder="<?php echo $state;?>"><br>

        Country: <input type="text" name="country" placeholder="<?php echo $country;?>"><br>

        <input type="submit" style="font-size:30px;height:50px;width:140px">
    </form>
</div>
</html

