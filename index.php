<!DOCTYPE html>
<?php
  session_start();
  include "functions.php";
  console_debug("session id: " . $_SESSION["user"]);

  $conn = sql_connect();
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
            if(isset($_SESSION["user"])){
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
            } else {
                $_SESSION["user"] = "";
            }
        ?>
    </div>

    <div class="main">
        <h1>Homework Overflow</h1>
        <h3>   aka Kora and Michael's amazing database project that helps you get homework answers!</h3>
        Most recent questions asked:
        <?php
            $sql="SELECT * FROM Questions JOIN Categories USING(qid) ORDER BY t DESC";
            $result = $conn->query($sql);

            //number of most recent questions to show here
            $count = 10;

            //arrays to manage multiple categories
            $questions = array();
            $questionsText = array();

            while($row = $result->fetch_assoc()){
                if(count($questions) < $count){
                    if(!in_array($row["qid"], $questions)){
                        array_push($questions, $row["qid"]);
                        //consider if this question is resolved
                        if($row["resolved"] == 1){
                            array_push($questionsText, 
                            "<br>------------------------------------------------------------<br>" .
                            "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                            " | Resolved<br>posted by 
                            <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>" . 
                            "<a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>"
                            );
                        } else {
                            array_push($questionsText, 
                                "<br>------------------------------------------------------------<br>" .
                                "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                                " | Unresolved<br>posted by 
                                <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>" . 
                                "<a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>"
                            );
                        }
                    } else {
                        $key = array_search($row["qid"], $questions);
                        $questionsText[$key] =  $questionsText[$key] . " and <a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>";
                    }
                }
            }
            //actually output each question
            foreach($questionsText as $text){
                echo $text;
            }
        ?>
    </div>
</html>
