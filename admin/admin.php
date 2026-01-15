<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
require("../auth/checkAuth.php");
include("../auth/checkAdmin.php");


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <title>Online Movie Ticketing System</title>
</head>

<body>
  <?php include("../includes/header.php"); ?>
  <div class="admin_container">
    <div class="sidebar">
      <ul>
        <li><a href="admin_dashboard.php" target="adminContent">
            <span class="material-symbols-outlined">dashboard</span>Dashboard
          </a></li>
        <li><a href="admin_movies.php" target="adminContent">
            <span class="material-symbols-outlined">movie</span>Movies
          </a></li>
        <li> <a href="admin_shows.php" target="adminContent">
            <span class="material-symbols-outlined">calendar_month</span>Shows
          </a></li>
      </ul>
    </div>
    <main class="admin_main">
      <iframe name="adminContent" src="admin_dashboard.php" class="adminContent"></iframe>
    </main>
  </div>
</body>
</html>