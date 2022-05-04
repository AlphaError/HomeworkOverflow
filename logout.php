<?php
  session_start();
  include "functions.php";
  console_debug("Logging out");
  
  $_SESSION["user"] = "";
  header("Location: /index.php");
?>