<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";

    //connect to SQL server
    $conn = sql_connect();

    //create variables for each field
    $username = "";
    $password = "";

    //login error message
    $loginErr = "";

    //if forms are submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        //validate data
        $username = input_validation($_POST["username"]);
        $password = input_validation($_POST["password"]);

        //query for any uswers with this username/pw combo
        $sql = "SELECT * FROM Users WHERE username='".$username."' AND pw='".$password."'";
        $result = $conn->query($sql);

        //if this username/pw combo exists in the DB, create session variable username and go to home page
        if ($result->num_rows > 0) {
            $_SESSION["user"] = $username;
            header("Location: /index.php");
        }
        //if no results, give error message
        else {
            $loginErr = "There was something wrong with your username or password.";
        }
    }


?>
<html>
<body>
    <style>
        .error {color: #FF0000;}
    </style>
    <h1>LOGIN</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Userame: <input type="text" name="username"><br>
        Password: <input type="text" name="password"><br>
        <span class="error"><?php echo $loginErr;?></span><br>
        <input type="submit">
    </form>
    <?php    
        //link to account creation page
        echo "<a href='register.php'>create account</a>";
    ?>
  </body>
</html>