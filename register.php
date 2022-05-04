<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";

    //connect to SQL server
    $conn = sql_connect();

    //create variables for each field
    $username = "";
    $email = "";
    $pw = "";
    $profile = "";
    $city = "";
    $state = "";
    $country = "";

    //error message variables
    $nameErr = "";
    $emailErr = "";
    $pwErr = "";

    //boolean to see if all forms are filled correctly
    $valid = true;

    //if forms are submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        //USERNAME CHECKS
        //ensure username is not empty
        if (empty($_POST["username"])) {
            $nameErr = "Username is required";
            $valid = false;
        //username should be at least 3 characters long
        } else if(strlen($_POST["username"])<3){
            $nameErr = "Username must be at least 3 characters long";
            $valid = false;
        } else {
            $username = input_validation($_POST["username"]);
            //check if username is only alphanumerics
            if (!ctype_alnum($username)) {
                $nameErr = "No special characters are allowed";
                $valid = false;
            }
        }

        //EMAIL CHECKS
        //email cannot be empty
        if (empty($_POST["email"])){
            $emailErr = "email is required";
            $valid = false;
        } else {
            $email = input_validation($_POST["email"]);
            //check if this is actually an email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $valid = false;
            }
        }

        //PASSWORD CHECKS
        //pw cannot be empty
        if (empty($_POST["pw"])){
            $pwErr = "Password is required";
            $valid = false;
        }
        //pw must be 6 characters or longer 
        else if (strlen($_POST["pw"])<6){
            $pwErr = "Password must be at least 6 characters long";
            $valid = false;
        } else {
            $pw = input_validation($_POST["pw"]);
        }

        //PROFILE CHECKS
        if(empty($_POST["profile"])){
            $profile = "";
        } else {
            $profile = input_validation($_POST["profile"]);
        }

        //CITY
        if(empty($_POST["city"])){
            $city = "";
        } else {
            $city = input_validation($_POST["city"]);
        }

        //STATE
        if(empty($_POST["state"])){
            $state = "";
        } else {
            $state = input_validation($_POST["state"]);
        }

        //CITY
        if(empty($_POST["country"])){
            $country = "";
        } else {
            $country = input_validation($_POST["country"]);
        }

        //if forms are all filled correctly
        if($valid){
            //query to see if username is taken
            $sql = "SELECT * FROM Users WHERE username='".$username."'";
            $result = $conn->query($sql);

            //if this username is taken, give error message
            if($result->num_rows > 0){
                $nameErr = "username is taken";
            } 
            //create account
            else {
                console_debug("creating account...");
                $sql = "INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES 
                    ('".$username."', '".$pw."', '".$email."', '".$city."', '".$state."', '".$country."', '".$profile."')";
                $conn->query($sql);

                //DEBUG: check if actually inserted
                $sql = "SELECT * FROM Users WHERE username='".$username."'";
                $result = $conn->query($sql);

                if($result->num_rows > 0){
                    console_debug("created successfully");
                } else {
                    console_debug("account creation failed! Please try again");
                }
                
                //DEBUG: list all usernames
                $sql = "SELECT username FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        console_debug($row['username']);
                    }
                }

                //set session user
                $_SESSION["user"] = $username;

                //redirect to home page
                header("Location: /index.php");
            }
        }
    }
?>
<html>
<body>
    <style>
        .error {color: #FF0000;}
    </style>
    <h1>CREATE ACCOUNT</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Userame: <input type="text" name="username">
        <span class="error">* <?php echo $nameErr;?></span><br>

        Email Address: <input type="text" name="email">
        <span class="error">* <?php echo $emailErr;?></span><br>

        Password: <input type="text" name="pw">
        <span class="error">* <?php echo $pwErr;?></span><br>
        
        Profile: <textarea name="profile" rows="5" cols="40"></textarea><br>

        City: <input type="text" name="city"><br>

        State: <input type="text" name="state"><br>

        Country: <input type="text" name="country"><br>

        <br><span class="error">* required</span><br><br>

        <input type="submit">
    </form>
    <?php
        echo "<br><br><a href='login.php'>Already have an account? Login here</a><br>";
    ?>
  </body>
</html>