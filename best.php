<?php
    session_start();
    include "functions.php";
    $conn = sql_connect();
    
    //resets all best answers for the question this answer is under and mark this answer as best
    $sql = "SELECT * FROM Answers WHERE qid='{$_GET["qid"]}'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            if($row["best"] == 1){
                //update appropriate tuple best to 0
                $sql_update = "UPDATE Answers SET best='0' WHERE aid='{$row["aid"]}'";
                $conn->query($sql_update);
            }
        }
    }

    //set new best answer
    $sql = "UPDATE Answers SET best='1' WHERE aid='{$_GET["aid"]}'";
    $conn->query($sql);

    header("location: question.php?qid={$_GET["qid"]}&title={$_GET["title"]}");
?>