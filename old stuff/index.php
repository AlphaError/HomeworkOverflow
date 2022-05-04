<?php
$host="localhost";
$port=3306;
$socket="";
$user="root";
$password=getenv('SQLSERVER_PW');  // private passcode
$dbname="db_project";

// Create connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully\n";


// GLOBALS
$USER_TIERS = array("basic" => 20, "intermediate" => 50, "advanced" => 100);


// Queries
//Question 3:
// $sql = "SELECT * FROM Posts";
// $result = $conn->query($sql);
 
// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//     $num_posts = $row['num_posts'];
//     echo "<br> ", $row['username'], " ", $row['num_posts'], " ";
//     if($num_posts < $USER_TIERS["basic"]){
//       echo "basic";
//     } else if($num_posts < $USER_TIERS["intermediate"]){
//       echo "intermediate";
//     } else {
//       echo "expert";
//     }
//     echo " </br>\n";
//   }
// } else {
//   echo "0 results";
// }

// Question 6:
$sql = "CREATE TEMPORARY TABLE keywords(word varchar(24));";
$conn->query($sql);
 
 
$stmt = $conn->prepare("INSERT INTO keywords (word) VALUES (?)");
$stmt->bind_param("s", $keyword);
 
$keywordSearch = array("math", "hard", "is");
 
 
foreach($keywordSearch as $keyword){
  $stmt->execute();
}
 
 
$sql = "
Select c.qid, count(aid) as numA, numQ
from answers right join (
  SELECT title, qid, count(qid) as numQ
  FROM Questions JOIN Categories USING(qid), keywords
  where LOCATE(keywords.word, questions.title) = 1
  group by qid
) as c on Answers.qid = c.qid
group by c.qid
order by numQ desc, numA desc;";
$result = $conn->query($sql);
 
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "qid: ", $row["qid"], "; keyword matches: ",
      $row["numQ"], "; number of Answers: ", $row["numA"],"<br\n";
    }
  } else {
    echo "no results";
  }

echo "\n";
?>