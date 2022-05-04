<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);

    //connect to SQL server
    $conn = sql_connect();

    $qid = strval($_GET["qid"]);

    //query for the question
    $sql = "SELECT * FROM Questions  join Categories using(qid) WHERE qid='{$qid}'";
    $result = $conn->query($sql)->fetch_assoc();

    $title = $result["title"];
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
    <h3>
        <?php echo "Title: " . $title; ?>
    </h3>
    <?php
    //echo rest of the question
    echo "Body: " . $result["body"] . "<br><br>";
    echo "Posted by <a href='profile.php?u=" . $result["username"] . "'> {$result["username"]} </a>" . " at " . $result["t"] . " under the category ";
    echo "<a href='browse.php?cat=" . $result["cat"] . "'> {$result["cat"]} </a>" . "<br><br><br>";

    //query for answers
    $sql = "SELECT * FROM Answers WHERE qid='{$qid}'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "------------------------------------------------------------<br>";
            echo $row["body"] . "<br> Posted by ";
            echo "<a href='profile.php?u=" . $row["username"] . "'> {$row["username"]} </a>";
            echo " at " . $row["t"] . "<br><br>";
        }
    } else {
        echo "Be the first to post an answer!";
        // TODO: add ability to answer
    }
    ?>
</div>
</html>