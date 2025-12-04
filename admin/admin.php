<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
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

  $movie_count = get_one_row($db_server, "SELECT COUNT(*) AS total_movies FROM movies");
  $users_count = get_one_row($db_server, "SELECT COUNT(*) AS total_users FROM users");
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

        <div class="button add" onclick="openModel('admin_add_movie.php')"><i class="fa-solid fa-plus"></i>
          <p>Add Movie</p>
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
          $movies = get_all_rows($db_server, "SELECT * FROM movies");
          foreach ($movies as $row) {
          ?>
            <tr>
              <td><?= $row["title"] ?></td>
              <td><?= $row["genre"] ?></td>
              <td><?= $row["duration"] ?> mins</td>
              <td class="action">
                <i class="fa-solid fa-pen-to-square action-edit" onclick="openModel('admin_edit_movie.php?id=<?= $row["movie_id"] ?>')"></i>
                <a href="admin_delete_movie.php?id=<?= $row["movie_id"] ?>" onclick="return confirm('Are you sure you want to delete this movie?')">
                  <i class="fa-solid fa-trash action-delete"></i>
                </a>
              </td>
            </tr>
          <?php
          }

          ?>
        </table>
      </div>
    </div>
  </div>

  <?php include("../includes/footer.php"); ?>

  <div class="model" id="model">
    <div class="model-content">
      <span class="close"><i class="fa-solid fa-xmark" onclick="closeModel()"></i></span>
      <iframe src="" frameborder="0" height="100%" width="100%" id="model-frame"></iframe>
    </div>
  </div>
</body>
<script>
  function openModel(url) {
    const model = document.getElementById("model");
    const frame = document.getElementById("model-frame");
    frame.src = url;
    model.style.display = "flex"
  }

  function closeModel() {
    const model = document.getElementById("model");
    const frame = document.getElementById("model-frame");
    model.style.display = "none"
    frame.src = "";
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