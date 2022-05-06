<?php
    session_start();
    include "functions.php";
    $conn = sql_connect();
    
    //if already liked, remove like; otherwise add like
    $sql = "SELECT * FROM Likes WHERE aid='{$_GET["aid"]}' AND username='{$_SESSION["user"]}'";
    $result = $conn->query($sql);

    //if already liked
    if($result->num_rows > 0){
        //remove like from db
        $sql = "DELETE FROM Likes WHERE aid='{$_GET["aid"]}' AND username='{$_SESSION["user"]}'";
    } else {
        //add like to db
        $sql = "INSERT INTO Likes(aid, username) VALUES ('{$_GET["aid"]}', '{$_SESSION["user"]}')";
    }
    //execute sql
    $conn->query($sql);

    console_debug("location: question.php?qid={$_GET["qid"]}&title={$_GET["title"]}");
    header("location: question.php?qid={$_GET["qid"]}&title={$_GET["title"]}");
?>