<!DOCTYPE html>
<?php
    //takes qid and title via get

    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);

    //connect to SQL server
    $conn = sql_connect();

    $qid = strval($_GET["qid"]);
    $title = $_GET["title"];
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
    <h3>
        <?php echo $title; ?>
    </h3>
    <?php
    //query for the question
    $sql = "SELECT * FROM Questions join Categories using(qid) WHERE qid='{$qid}'";
    $result = $conn->query($sql);

    //array of all categories
    $cats = array();
    $count = 0;

    $question_poster = "";

    $resolved = 0;

    while($row = $result->fetch_assoc()){
        array_push($cats, $row["cat"]);
        if($count == 0){
            //echo the question
            echo $row["body"] . "<br><br>";
            echo "Posted by <a href='profile.php?u=" . $row["username"] . "'> {$row["username"]} </a>" . " at " . $row["t"] . " under the category ";
            $question_poster = $row["username"];
            $resolved = $row["resolved"];
        }
        $count++;
    }

    $count = 0;
    foreach($cats as $cat){
        if($count == 0){
            echo "<a href='browse.php?cat={$cat}'> {$cat} </a>";
        } else {
            echo " and <a href='browse.php?cat={$cat}'> {$cat} </a>";
        }
        $count++;
    }
    echo "<br><br>";

    //if poster is logged in, allow to mark resolved/unresolved
    if($_SESSION["user"] == $question_poster){
        if($resolved == 0){
            echo "<a href='resolve.php?qid={$qid}&title={$title}'>Mark as resolved</a>";
        } else {
            echo "<a href='resolve.php?qid={$qid}&title={$title}'>Mark as unresolved</a>";
        }
    } else {
        //if not the question poster, display resolved status
        if($resolved == 1){
            echo "This question has been marked as resolved.";
        } else {
            echo "This question is unresolved.";
        }
    }
    echo "<br><br><b>Answers:</b><br>";


    //query for answers
    $sql = "SELECT * FROM Answers WHERE qid='{$qid}'";
    $result = $conn->query($sql);

    $i = 0;

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $i++;
            echo "------------------------------------------------------------<br>";
            echo $row["body"] . "<br> Posted by ";
            echo "<a href='profile.php?u=" . $row["username"] . "'> {$row["username"]} </a>";
            echo " at " . $row["t"] . "<br>";
            //show best answer
            if($row["best"] == 1){
                echo "Best answer!<br>";
            }
            //best answer if question was posted by the user
            if($_SESSION["user"] == $question_poster && $row["best"] != 1){
                echo "<a href='best.php?aid={$row["aid"]}&qid={$qid}&title={$title}'>Mark as best answer</a><br>";
            }

            //bool tracking if this user has liked this answer
            $liked = 0;
            //query to see all likes for an answer
            $sql_likes = "SELECT Likes.username FROM Likes JOIN Answers USING(aid) WHERE aid='{$row["aid"]}'";
            $res = $conn->query($sql_likes);

            if($res->num_rows > 0){
                while($r = $res->fetch_assoc()){
                    //don't give option to like if this user has already liked it
                    if($_SESSION["user"] == $r["username"]){
                        $liked = 1;
                    }
                }
            }
            
            //display number of likes
            //query for number of likes
            $sql_likes = "select count(aid) as num from likes join answers using(aid) where aid = '{$row["aid"]}' group by aid";
            $res = $conn->query($sql_likes);

            if($res->num_rows > 0){
                while($r = $res->fetch_assoc()){
                    echo $r["num"] . " ";
                }
            } else {
                echo "0 ";
            }

            //if logged in, allow to like
            if($_SESSION["user"] != ""){
                echo "<a href='likes.php?aid={$row["aid"]}&qid={$qid}&title={$title}'>likes</a>";

                if($liked == 1){
                    echo " (already liked)";
                }
            } else {
                echo "likes";
            }


            //new query for likes on this answer
            echo "<br><br>";
        }
        //post an answer
        if($_SESSION["user"] == ""){
            echo "<a href='login.php'>Login</a> to post an answer!";
        } else {
            echo "<a href='answer.php?qid={$qid}&title={$title}'>Post your answer</a>";
        }
    } else {
        //check if logged in to give answer posting permissions
        if($_SESSION["user"] == ""){
            echo "------------------------------------------------------------<br>";
            echo "<a href='login.php'>Login</a> to post an answer!";
        } else {
            echo "------------------------------------------------------------<br>";
            echo "<a href='answer.php?qid={$qid}&title={$title}'>Be the first to post an answer!</a>";
        }
    }

    
    ?>
</div>
</html>