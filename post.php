<!DOCTYPE html>
<?php
    //post a new question page takes no input

    session_start();
    include "functions.php";

    $conn = sql_connect();

    console_debug("session id: " . $_SESSION["user"]);

    //create variables for each field
    $title = "";
    $body = "";

    //error message variables
    $titleErr = "";
    $bodyErr = "";
    $catErr = "";

    //boolean
    $valid = TRUE;

    //security check to make sure poster is logged in
    if($_SESSION["user"] == ""){
        $valid = FALSE;
    }

    //if forms are submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //check for title errors
        if (empty($_POST["title"])) {
            $titleErr = "Question title is required";
            $valid = false;
        } else {
            $title = input_validation($_POST["title"]);
            console_debug($title);
        }

        //check for body errors
        if (empty($_POST["body"])) {
            $bodyErr = "Question body is required";
            $valid = false;
        } else {
            $body = input_validation($_POST["body"]);
            console_debug($body);
        }

        //check if a category has been chosen
        if (!isset($_POST["category"])){
            $catErr = "A category is required";
            $valid = false;
        }



        //if everything is valid, add question to the DB
        if($valid){
            //get new qid for this question
            $sql = "SELECT MAX(qid) as qid FROM Questions";
            $qid = strval(intval($conn->query($sql)->fetch_assoc()["qid"]) + 1);

            //get time
            $t = date("Y-m-d H:i:s");

            //add to questions
            $sql = "INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('{$qid}', '{$title}', '{$body}', 0, '{$t}', '{$_SESSION["user"]}')";
            $conn->query($sql);

            //add to categories
            $sql_prepared = $conn->prepare("INSERT INTO Categories(cat, qid) VALUES (?, '{$qid}');");
            $sql_prepared->bind_param("s", $cat);

            foreach($_POST["category"] as $cat){
                $sql_prepared->execute();
            }

            //link to this question page
            header("location: question.php?qid={$qid}&title={$title}");
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
    <h4>POST A NEW QUESTION:</h4>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Title: <input type="text" name="title"><br>
        <span class="error"><?php echo $titleErr;?></span><br>

        Question: <textarea name="body" rows="5" cols="40"></textarea><br>
        <span class="error"><?php echo $bodyErr;?></span><br>

        Category:
        <br>
        <?php
            //query for high level categories
            $sql="SELECT cat FROM Topics WHERE cat=subcat";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()){
                //echo a checkbox for the high level topic
                echo '<input type="checkbox" name="category[]" value="' . $row["cat"] . '" id="' . $row["cat"] . '">
                <label for="' . $row["cat"] . '">' . $row["cat"] . '</for><br>';

                //query for sub-categories
                $sql_sub="SELECT subcat FROM Topics WHERE cat!=subcat AND cat='{$row["cat"]}'";
                $res = $conn->query($sql_sub);

                if($res->num_rows>0){
                    while($r = $res->fetch_assoc()){
                        //echo a checkbox for each low-level topic
                        echo '&emsp;&emsp;<input type="checkbox" name="category[]" value="' . $r["subcat"] . '" id="' . $r["subcat"] . '">
                        <label for="' . $r["subcat"] . '">' . $r["subcat"] . '</for><br>';
                    }
                }
            }
        ?>
        <span class="error"><?php echo $catErr;?></span><br>

        <input type="submit" style="font-size:30px;height:50px;width:140px">
    </form>
</div>
</html>