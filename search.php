<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);
?>
<html>
  <body>
    <h1>Search</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="get">
        <input type="text" name="keywords">
        <input type="submit">
    </form>
    <?php
        //connect to SQL server
        $conn = sql_connect();

        if($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET["keywords"])) {
            //change keywords from string to array of substrings dilineated by a space
            console_debug("Searching for keywords " . $_GET["keywords"] . "...");
            $keywordSearch = explode(" ", $_GET["keywords"]);

            //create temporary table with each keyword
            $sql = "CREATE TEMPORARY TABLE keywords(word varchar(24));";
            $conn->query($sql);
            $stmt = $conn->prepare("INSERT INTO keywords (word) VALUES (?)");
            $stmt->bind_param("s", $keyword);

            foreach($keywordSearch as $keyword){
                $stmt->execute();
            }

            //search for matching questions sorted by number of keyword matches
            $sql = "Select c.qid, title, count(aid) as numA
                from answers right join (
                    SELECT title, qid, count(qid) as numQ
                    FROM Questions JOIN Categories USING(qid), keywords
                    WHERE LOCATE(keywords.word, questions.title) > 0
                    group by qid
                    ) as c on Answers.qid = c.qid
                group by c.qid
                order by numQ desc, numA desc;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $text = $row["title"] . " | " . $row["numA"] . " answers";
                    echo "<a href='question.php?qid=" . $row["qid"] . "'>$text</a><br>";
                }
            } else {
                echo "No results";
            }
        }
    ?>
  </body>
</html>