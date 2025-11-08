<?php
session_start();
include("../includes/connection.php");
include("../auth/checkAuth.php");

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
  <title>Online Movie Ticketing System</title>
</head>

<body>
  <?php include("../includes/header.php"); ?>
  <?php

  $movie_count = mysqli_fetch_assoc(mysqli_query($db_server, "Select count(*) as total_movies from movies"));
  $total_movies = $movie_count["total_movies"];
  $users_count = mysqli_fetch_assoc(mysqli_query($db_server, "Select count(*) as total_users from users"));
  $total_users = $users_count["total_users"];
  ?>
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
        <p class="number"><? ?></p>
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

  <div class="management-container">
    <div class="management-body">

      <div class="management-head">
        <p>Movies</p>

        <div class="button add" onclick="openModel()"><i class="fa-solid fa-plus"></i>
          <p>Add Movie</p>
        </div>
        <div class="modal" id="model">
          <div class="modal-content">
            <span class="close"><i class="fa-solid fa-xmark" onclick="closeModel()"></i></span>
            <iframe src="admin_add_movie.php" frameborder="0" height="100%" width="100%"></iframe>
          </div>
        </div>
      </div>
      <div class="management-content">
        <table class="admin-content-table">
          <tr>
            <th>Title</th>
            <th>Genre</th>
            <th>Duration</th>
            <th class="action">Actions</th>

          </tr>
          <?php
          $movies = mysqli_query($db_server, "Select * from movies");
          if (mysqli_num_rows($movies) > 0) {
            while ($row = mysqli_fetch_assoc($movies)) {
          ?>
              <tr>
                <td><?= $row["title"] ?></td>
                <td><?= $row["genre"] ?></td>
                <td><?= $row["duration"] ?> mins</td>
                <td class="action"><i class="fa-solid fa-pen-to-square action-edit"></i><i class="fa-solid fa-trash action-delete"></i></td>
              </tr>
          <?php
            }
          }
          ?>
        </table>
      </div>
    </div>
  </div>

  <?php include("../includes/footer.php"); ?>
</body>
<script>
  function openModel() {
    document.getElementById("model").style.display = "flex"
  }

  function closeModel() {
    document.getElementById("model").style.display = "none"
  }
  document.addEventListener("DOMContentLoaded", function() {
    const model = document.getElementById("model");
    model.addEventListener("click", function(event) {
      if (event.target === model) {
        closeModel();
      }
    })

  })
  document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
      closeModel();
    }
  });
</script>

</html>