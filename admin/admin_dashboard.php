<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$user_id=$_SESSION["user_id"];
$movie_count = get_one_row($db_server, "SELECT COUNT(*) AS total_movies FROM movies");
$users_count = get_one_row($db_server, "SELECT COUNT(*) AS total_users FROM users");
$bookings_count=get_one_row($db_server, "SELECT COUNT(*) AS total_bookings FROM bookings WHERE status='confirmed'");
?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<div class="indicators-menu">
    <div class="indicator">
        <div class="indicator-text">
            Total Movies
            <p class="number"><?= $movie_count["total_movies"]; ?></p>
        </div>
        <i class="fa-solid fa-film indicator-icon"></i>
    </div>
    <div class="indicator">
        <div class="indicator-text">
            Total Bookings
            <p class="number"><?= $bookings_count["total_bookings"]; ?></p>
        </div>
        <i class="fa-solid fa-ticket indicator-icon"></i></i>
    </div>
    <div class="indicator">
        <div class="indicator-text">
            Total Users
            <p class="number"><?= $users_count["total_users"]; ?></p>
        </div>
        <i class="fa-solid fa-users indicator-icon"></i></i>
    </div>
</div>