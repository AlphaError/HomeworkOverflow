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
  <body>
    <h1>
        <?php echo $title; ?>
    </h1>
    <?php 
        //echo rest of the question
        echo $result["body"] . "<br><br>";
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
        }
    ?>
  </body>
</html>