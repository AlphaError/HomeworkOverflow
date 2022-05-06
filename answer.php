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
        <?php echo "Title: " . $title; ?>
    </h3>
    <?php
        //query for the question
        $sql = "SELECT * FROM Questions join Categories using(qid) WHERE qid='{$qid}'";
        $result = $conn->query($sql);

        //array of all categories
        $cats = array();
        $count = 0;

        while($row = $result->fetch_assoc()){
            array_push($cats, $row["cat"]);
            if($count == 0){
                //echo the question
                echo "Body: " . $row["body"] . "<br><br>";
                echo "Posted by <a href='profile.php?u=" . $row["username"] . "'> {$row["username"]} </a>" . " at " . $row["t"] . " under the category ";
            }
            $count++;
        }

        //echo categories
        $count = 0;
        foreach($cats as $cat){
            if($count == 0){
                echo "<a href='browse.php?cat={$cat}'> {$cat} </a>";
            } else {
                echo " and <a href='browse.php?cat={$cat}'> {$cat} </a>";
            }
            $count++;
        }
        echo "<br><br><br>";

        //input variables
        $answer = "";
        $answerErr = "";

        //if forms are submitted
        if(!empty($_GET["answer"])) {

            $answer = input_validation($_GET["answer"]);

            //get new aid for this answer
            $sql = "SELECT MAX(aid) as aid FROM Answers";
            $aid = strval(intval($conn->query($sql)->fetch_assoc()["aid"]) + 1);

            //get time
            $t = date("Y-m-d H:i:s");

            //create new answer entry
            $sql = "INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('{$aid}', '{$qid}', '{$answer}', 0, '{$t}', '{$_SESSION["user"]}')";
            $conn->query($sql);

            //link back to the question page
            header("Location: question.php?qid={$qid}&title={$title}");
        }
    ?>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="qid" value = "<?php echo $qid;?>">
        <input type="hidden" name="title" value = "<?php echo $title;?>">
        Answer: <textarea name="answer" rows="5" cols="40"></textarea><br><br>
        <span class="error"><?php echo $answerErr;?></span><br>
        <input type="submit" style="font-size:30px;height:50px;width:140px">
    </form>
</div>
</html>