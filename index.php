<?php
session_start();
include("includes/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>Online Movie Ticketing System</title>
</head>

<body>

  <?php include("includes/header.php"); ?>
  <header class="hero">
    Now Showing
  </header>
  <main class="movie-grid">
    <?php
    $result = mysqli_query($db_server, "SELECT * from movies");
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <div class="movie-card">
          <a href="pages/movie.php?id=<?= $row['movie_id'] ?>">

            <img src="assets/image/<?= $row["poster"]; ?>" alt="Movie" />
            <div class="details">
              <p class="movie-name"><?= $row["title"]; ?></p>
              <span class="movie-details"><?= $row["language"]; ?> | <?= $row["genre"]; ?></span>

            </div>
          </a>
        </div>
    <?php
      }
    }
    ?>

  </main>
  <?php include("includes/footer.php"); ?>
</body>

</html>