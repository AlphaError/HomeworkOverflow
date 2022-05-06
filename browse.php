<!DOCTYPE html>
<?php
    //mak this take only cat, not cat + sub
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);
    
    $conn = sql_connect();
    
    $cat = $_GET["cat"];

    //query for top-level categories and store them in an array
    $categories = array();
    $sql = "SELECT DISTINCT cat FROM Topics WHERE cat=subcat";
    $result = $conn->query($sql);
    while($row=$result->fetch_assoc()){
        array_push($categories, $row["cat"]);
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
    <h4>Browse</h4>
    <?php
        //if cat is not selected
        if($cat == ""){
            echo "Choose a category: <br>";

            //list high level categories
            foreach($categories as $val){
                echo "<a href='browse.php?cat={$val}'>$val</a><br>";
            }
        }
        //if cat is selected
        else {
            //if cat is high level
            if(in_array($cat, $categories)){
                //print the chosen high level category
                echo $cat . "<br><br>Choose a sub-category:<br>";
                
                //query and list subcategories
                $sql = "SELECT subcat FROM Topics WHERE cat='{$cat}' AND subcat!='{$cat}'";
                $result = $conn->query($sql);
                while($row=$result->fetch_assoc()){
                    echo "<a href='browse.php?cat={$row["subcat"]}'>{$row["subcat"]}</a><br>";
                }
                
                //query and list questions under the high level category
                $sql = "select *
                    from questions join (
                        select qid
                        from topics right join categories on topics.subcat = categories.cat
                        where topics.cat='{$cat}'
                        order by qid) as c using(qid)";
                $result = $conn->query($sql);
                if($result->num_rows > 0){
                    echo "<br><br>Questions under the category " . $cat . "<br><br>";
                    while($row=$result->fetch_assoc()){
                        echo "------------------------------------------------------------<br>" .
                            "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                            " answers<br>posted by <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>";
                    }
                } else {
                    echo "<br>No questions under this category have been posted yet.";
                }
            }
            //if cat is a subcategory
            else {
                //print the high level category
                $sql = "SELECT cat FROM Topics WHERE subcat='{$cat}'";
                echo $conn->query($sql)->fetch_assoc()["cat"];

                //print the sub-category
                echo "<br>" . $cat;

                //query and list questions under this sub-category
                $sql = "SELECT * FROM Questions JOIN Categories USING(qid) WHERE cat='{$cat}'";
                $result = $conn->query($sql);
                if($result->num_rows > 0){
                    echo  "<br><br>Questions under the sub-category " . $cat . ":<br>";
                    while($row=$result->fetch_assoc()){
                        echo "------------------------------------------------------------<br>" .
                            "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                            " answers<br>posted by <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>";
                    }
                } else {
                    echo "<br>No questions under this category have been posted yet.";
                }
            }
        }
    ?>
</div>
</html>
