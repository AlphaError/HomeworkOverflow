<?php
    //helper file to resolve questions
    session_start();
    include "functions.php";
    $conn = sql_connect();
    
    //query for this qid
    $sql = "SELECT resolved FROM Questions WHERE qid='{$_GET["qid"]}'";
    $result = $conn->query($sql)->fetch_assoc();

    //swap resolved boolean
    if($result["resolved"] == 0){
        $sql = "UPDATE Questions SET resolved='1' WHERE qid='{$_GET["qid"]}'";
    } else {
        $sql = "UPDATE Questions SET resolved='0' WHERE qid='{$_GET["qid"]}'";
    }
    $conn->query($sql);

    header("location: question.php?qid={$_GET["qid"]}&title={$_GET["title"]}");
?>