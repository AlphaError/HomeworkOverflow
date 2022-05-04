<!DOCTYPE html>
<?php
    session_start();
    include "functions.php";
    console_debug("session id: " . $_SESSION["user"]);
    
    $cat = $_GET["cat"];
?>
<html>
  <body>
    <h1>Browse</h1>
    <?php
      //if cat is selected, show questions under that category
      if($cat != ""){
        echo $cat;
      }
      //if no cat is selected, give options to click and browse through
      else {
        echo "Choose a category:<br>";
      }
    ?>
  </body>
</html>