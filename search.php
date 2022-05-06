<!DOCTYPE html>
<?php
    //takes keywords via get
    
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
    <h4>Search</h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="get">
        <input type="text" name="keywords" style="font-size:22px;width:500px">
        <input type="submit" style="font-size:22px;">

        <br><br>Categories:<br>
        <?php
        //query for high level categories
        $sql="SELECT cat FROM Topics WHERE cat=subcat";
        $result = $conn->query($sql);
        // $_GET["category"] Becomes category array from input checkboxes
        while ($row = $result->fetch_assoc()) {
            //echo a checkbox for the high level topic
            echo "<select name=\"category[]\" multiple size = 6>";
            echo "<option style=\"font-size: 21px;\" value=\"" .$row["cat"]. "\">" .$row["cat"]. "</option>";

            //query for sub-categories
            $sql_sub="SELECT subcat FROM Topics WHERE cat!=subcat AND cat='{$row["cat"]}'";
            $res = $conn->query($sql_sub);

            if($res->num_rows>0){
                while($r = $res->fetch_assoc()){
                    //echo a checkbox for each low-level topic
                    echo "<option style=\"font-size: 18px;\" value=\"" .$r["subcat"]. "\">" .$r["subcat"]. "</option>";
                }
            }
            echo "</select> ";
        }
        ?>
    </form>
    <br>
    <?php
    //connect to SQL server
//    $conn = sql_connect();

    if(!empty($_GET["keywords"]) or isset($_GET["category"])) {
        //change keywords from string to array of substrings dilineated by a space
        console_debug("Searching for keywords " . $_GET["keywords"] . "...");
        $keywordSearch = explode(" ", $_GET["keywords"]);

        $categorySearch = "";
        if (isset($_GET["category"])) {
            $categorySearch = "where subcat=\"" . implode("\" or subcat=\"", $_GET["category"]) . "\";";
            console_debug("where subcat=\"$categorySearch\"");
        }

        //create temporary table with each keyword
        $sql = "CREATE TEMPORARY TABLE keywords(word varchar(24));";
        $conn->query($sql);
        $stmt = $conn->prepare("INSERT INTO keywords (word) VALUES (?)");
        $stmt->bind_param("s", $keyword);

        foreach($keywordSearch as $keyword){
            $stmt->execute();
        }

        //search for matching questions sorted by number of keyword matches
        $sql = "Select qid, resolved, numA, title, t, categories.cat, username
                From categories join(
                Select resolved, c.username, c.t, c.qid, title, count(aid) as numA
                from answers right join (
                    SELECT resolved, username, t, title, qid, count(qid) as numQ
                    FROM Questions JOIN Categories USING(qid), keywords
                    WHERE LOCATE(keywords.word, questions.title) > 0
                    group by qid
                    ) as c on Answers.qid = c.qid
                group by c.qid
                order by numQ desc, resolved desc, numA desc) as d using(qid)
                    left join Topics on categories.cat = subcat
                $categorySearch;";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            //arrays for categories output
            $questions = array();
            $questionsText = array();
            while($row = $result->fetch_assoc()) {
                if(!in_array($row["qid"], $questions)){
                    array_push($questions, $row["qid"]);

                    //consider if this question is resolved
                    if($row["resolved"] == 1){
                        array_push($questionsText, 
                        "<br>------------------------------------------------------------<br>" .
                        "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                        " | " . $row["numA"] . " answers | Resolved<br>posted by 
                        <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>" . 
                        "<a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>"
                        );
                    } else {
                        array_push($questionsText, 
                            "<br>------------------------------------------------------------<br>" .
                            "<a href='question.php?qid={$row["qid"]}&title={$row["title"]}'>{$row["title"]}</a> " . 
                            " | " . $row["numA"] . " answers | Unresolved<br>posted by 
                            <a href='profile.php?u={$row["username"]}'>{$row["username"]}</a> at {$row["t"]}<br>" . 
                            "<a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>"
                        );
                    }
                } else {
                    $key = array_search($row["qid"], $questions);
                    $questionsText[$key] =  $questionsText[$key] .
                        " and <a href='browse.php?cat={$row['cat']}'>{$row['cat']}</a>";
                }
            }
            foreach($questionsText as $text){
                echo $text;
            }
        } else {
            echo "No results";
        }
    }
    ?>
</div>
</html>