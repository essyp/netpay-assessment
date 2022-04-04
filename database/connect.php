<?php 

require_once ("constants.php");

$dbcon = mysqli_connect(DBHOST,DBUSER,DBPASS) or die(mysqli_error($dbcon)." at line ".__LINE__);
mysqli_select_db($dbcon,DBNAME);

?>