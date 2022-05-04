<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);


    $user = $_SESSION["user"];
    $u = $_GET["u"];

    $mod = $user == $u;

    //connect to SQL server
    $conn = sql_connect();

    //query for the user
    $sql = "SELECT * FROM Users WHERE username='{$u}'";
    $result = $conn->query($sql)->fetch_assoc();
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
    <h4>Profile Info</h4>
    <?php
    // get calculate rank
    $rank = "Beginner";
    //    $USER_TIERS = array("Beginner" => 20, "Intermediate" => 50, "Expert" => 100);
    //
    //    $sql = "SELECT * FROM Posts where username=$u";
    //    $result = $conn->query($sql);
    //    if ($result->num_rows > 0) {
    //        // output data of each row
    //        while($row = $result->fetch_assoc()) {
    //            if($row['num_posts'] < $USER_TIERS["Beginner"]){
    //                $rank = "Beginner";
    //            } else if($row['num_posts'] < $USER_TIERS["Intermediate"]){
    //                $rank = "Intermediate";
    //            } else {
    //                $rank = "Expert";
    //            }
    //            $rank = "*Error*";
    //        }
    //    }
    echo "Name: " . $result["username"] . "<br> Rank: $rank<br>";
    echo "Your Info: <br> Description:" . $result["pf"] . "<br> City: " . $result["city"] . "<br>";
    echo "State: ". $result["state"] . "<br> Country: " . $result["country"] . "<br><br><br>";
    //if logged in user is looking at own profile
    if($mod){
        console_debug("looking at own profile");
    }

    //query for questions asked by the user
    $sql = "SELECT * FROM Questions join Categories using(qid) WHERE username='{$u}'";
    $result = $conn->query($sql);
    echo "Questions Asked:<br>";
    if($result->num_rows > 0){
        //to prevent repeating a question if it has multiple arrays
        $questions = array();
        $questionsText = array();
        $categories = "";
        $in_arr = false;
        while($row = $result->fetch_assoc()){
            //if this qid is not already present, add it to the list of questions to be printed
            if(!in_array($row["qid"], $questions)){
                array_push($questions, $row["qid"]);
                array_push($questionsText,
                    "<br>------------------------------------------------------------<br>" .
                    "<a href='question.php?qid=" . $row["qid"] . "&title=" . $row["title"] . "'>{$row["title"]}</a><br>" .
                    $row['body'] . "<br>" .
                    "posted at " . $row["t"] . " under the category <a href='browse.php?cat=" . $row["cat"] . "'> {$row["cat"]} </a>"
                );
            }
            //if this qid is already present, add the category to the relevant text
            else {
                $index = array_search($row["qid"], $questions);
                $questionsText[$index] = $questionsText[$index] . "and " . "<a href='browse.php?cat=" . $row["cat"] . "'> {$row["cat"]} </a>";
            }
        }
        foreach($questionsText as $value){
            echo $value . "<br>";
        }
    } else {
        echo "...<br>";
    }

    //query for answers given by the user along with the question
    $sql = "SELECT Answers.aid, Answers.body, Answers.t, Questions.title, Questions.qid FROM Answers join Questions using(qid) where Answers.username = '{$u}'";
    $result = $conn->query($sql);
    echo "<br>Answers Given:<br>";
    if($result->num_rows > 0){
        echo "<br><br>Answers Given:<br>";
        while($row = $result->fetch_assoc()){
//BROKEN          //query for likes
            $num_likes = 0;
            echo "------------------------------------------------------------<br>";
            echo $row["body"] . "<br> received " . $num_likes . " likes<br>";
            echo "in response to the question <a href='question.php?qid=" . $row["qid"] . "&title=" . $row["title"] . "'>{$row["title"]}</a><br>" . " posted at " . $row["t"] . "<br>";
        }
    } else {
        echo "...<br>";
    }
    ?>
</div>
</html>