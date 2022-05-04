<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);


    $user = $_SESSION["user"];
    $u = $_GET["u"];

    $mod = $user == $u;

    //connect to SQL server
    $conn = sql_connect();

    //query for the user
    $sql = "SELECT * FROM Users WHERE username='{$u}'";
    $result = $conn->query($sql)->fetch_assoc();
?>

<html>
  <body>
    <h1>Profile</h1>
    <?php 
      echo $result["username"] . "<br>" . $result["pf"] . "<br>" . $result["city"] . ", " . $result["state"] . "<br>" . $result["country"] . "<br><br><br>";
      //if logged in user is looking at own profile
      if($mod){
        console_debug("looking at own profile");
      }

      //query for questions asked by the user
      $sql = "SELECT * FROM Questions join Categories using(qid) WHERE username='{$u}'";
      $result = $conn->query($sql);
      if($result->num_rows > 0){
        echo "Questions:";
        //to prevent repeating a question if it has multiple arrays
        $questions = array();
        $questionsText = array();
        $categories = "";
        $in_arr = false;
        while($row = $result->fetch_assoc()){
          //if this qid is not already present, add it to the list of questions to be printed
          if(!in_array($row["qid"], $questions)){
            array_push($questions, $row["qid"]);
            array_push($questionsText, 
              "<br>------------------------------------------------------------<br>" .
              "<a href='question.php?qid=" . $row["qid"] . "&title=" . $row["title"] . "'>{$row["title"]}</a><br>" .
              $row['body'] . "<br>" .
              "posted at " . $row["t"] . " under the category <a href='browse.php?cat=" . $row["cat"] . "'> {$row["cat"]} </a>"
            );
          } 
          //if this qid is already present, add the category to the relevant text
          else {
            $index = array_search($row["qid"], $questions);
            $questionsText[$index] = $questionsText[$index] . "and " . "<a href='browse.php?cat=" . $row["cat"] . "'> {$row["cat"]} </a>";
          }
        }
        foreach($questionsText as $value){
          echo $value . "<br>";
        }
      } else {
        echo "No questions asked yet <br>";
      }

      //query for answers given by the user along with the question
      $sql = "SELECT Answers.aid, Answers.body, Answers.t, Questions.title, Questions.qid FROM Answers join Questions using(qid) where Answers.username = '{$u}'";
      $result = $conn->query($sql);
      if($result->num_rows > 0){
        echo "<br><br>Answers Given:<br>";
        while($row = $result->fetch_assoc()){
//BROKEN          //query for likes
          $num_likes = 0;
          echo "------------------------------------------------------------<br>";
          echo $row["body"] . "<br> received " . $num_likes . " likes<br>";
          echo "in response to the question <a href='question.php?qid=" . $row["qid"] . "&title=" . $row["title"] . "'>{$row["title"]}</a><br>" . " posted at " . $row["t"] . "<br>";
        }
      } else {
        echo "No answers given yet <br>";
      }
      echo "<br><br><a href='index.php'>home</a>";
    ?>
  </body>
</html>