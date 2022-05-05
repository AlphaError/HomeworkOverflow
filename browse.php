<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);
    
    $conn = sql_connect();
    
    $cat = $_GET["cat"];
    $sub = $_GET["sub"];
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
    <h4>Browse</h4>
    <?php
    //if cat is selected and sub is not selected, show sub-categories under that category and then all questions under the category
    if($cat != "" && $sub == ""){
        echo $cat . "<br>Choose a sub-category:<br><br>";

        //query for sub-categories under this category
        $sql = "SELECT subcat FROM Topics WHERE cat!=subcat AND cat='{$cat}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                console_debug($row["subcat"]);
                echo "<a href='browse.php?cat={$cat}&sub={$row["subcat"]}'>{$row["subcat"]}</a><br>";
            }
        }

        echo "<br>Questions under the category {$cat}:<br><br>";

        //query for questions under that category
        $sql = "SELECT * FROM Questions JOIN Categories USING(qid) WHERE cat='{$cat}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                console_debug($row["title"]);
                echo "------------------------------------------------------------<br>";
                echo "<a href='question.php?qid={$row["qid"]}'&title={$row["title"]}>{$row["title"]}</a>";
                echo " posted by <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>";
            }
        } else {
            echo "<a href='post.php'>Be the first to post a question under this category!</a>";
        }
    }
    //if cat and sub are both selected, show questions under that category
    else if($cat != "" && $sub != ""){
        //How do you tab?
        echo $cat . "<br>----" . $sub . "<br><br>";
        
        echo "Questions under the sub-category {$sub}:<br>";

        //query for questions under that subcategory
        $sql = "SELECT * FROM Questions JOIN Categories USING(qid) WHERE cat='{$sub}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                console_debug($row["title"]);
                echo "------------------------------------------------------------<br>";
                echo "<a href='question.php?qid={$row["qid"]}'&title={$row["title"]}>{$row["title"]}</a>";
                echo " posted by <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>";
            }
        } else {
            echo "<a href='post.php'>Be the first to post a question under this category!</a>";
        }
    }
    //if no cat is selected, give options to click and browse through
    else {
        echo "Choose a category:<br><br>";
        
        //query for high level categories
        $sql = "SELECT cat FROM Topics WHERE cat=subcat";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                console_debug($row["cat"]);
                echo "<a href='browse.php?cat={$row["cat"]}&sub='>{$row["cat"]}</a><br>";
            }
        }
    }
    ?>
</div>
</html>
