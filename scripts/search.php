<?php
require('./functions.php');

$data = mysqli_real_escape_string($dbcon, $_GET["search"]);
$search = new search();
$result = $search->query($data);
var_dump($result);
?>