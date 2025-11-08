<?php
$db_hostname = "localhost";
$db_database = "movie_booking_system";
$db_username = "root";
$db_password = "";

$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
if (!$db_server) {
    die("Unable to connect: " . mysqli_connect_error());
}
?>