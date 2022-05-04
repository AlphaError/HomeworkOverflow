<?php
    //connect to SQL server
    function sql_connect(){
        $servername = "localhost";
        $username = "root";
        $password = "11006959";
        // Create connection
        $conn = new mysqli($servername, $username, $password, "sys");
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        console_debug("Connected successfully");

        return $conn;
    }

    //remove whitespace, backslashes, and prevent cross-site scripting attacks with htmlspecialchars
    function input_validation($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }



    //logs to console for debugging purposes
    function console_debug($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug: " . $output . "' );</script>";
    }
?>