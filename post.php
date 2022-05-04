<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";

    $conn = sql_connect();

    console_debug("session id: " . $_SESSION["user"]);

    //create variables for each field
    $title = "";
    $body = "";

    //error message variables
    $titleErr = "";

    //boolean
    $valid = TRUE;

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
    }
?>

<html>
    <body>
    <style>
        .error {color: #FF0000;}
    </style>
    <h1>Post a New Question</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Title: <input type="text" name="title">
        <span class="error">* <?php echo $titleErr;?></span><br>

        Question: <input type="text" name="body"><br>

        Category: <select cat="cat">
            <option value="select">Select</option>
            <?php
                //sql query to find all categories
                $sql = "SELECT DISTINCT cat FROM Categories";
                $result = $conn->query($sql);
                
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    console_debug($row['cat']);
                }
                
            ?>
        </select>

        <br><span class="error">* required</span><br><br>

        <input type="submit">
    </form>
    </body>
</html>