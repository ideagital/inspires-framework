<?php
$connect = mysqli_connect("Your database host","Your database username","Your database password","Your database name");

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

mysqli_set_charset($connect,"utf8");
?>
