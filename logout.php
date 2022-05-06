<?php
  //helper file to log out a user

  session_start();
  include "functions.php";
  
  $_SESSION["user"] = "";
  header("Location: /index.php");
?>