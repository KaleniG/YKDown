<?php
function get_mysqli_connection(): mysqli|false
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "ykdown";

  return mysqli_connect($servername, $username, $password, $database);
}
?>