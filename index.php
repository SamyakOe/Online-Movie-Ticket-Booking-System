<?php
session_start();
include("includes/connection.php");
include("includes/db_helper.php");
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
    $movies = get_all_rows($db_server, "SELECT * from movies");

    foreach ($movies as $movie) {
    ?>
      <div class="movie-card">
        <a href="pages/movie.php?id=<?= $movie['movie_id'] ?>">

          <img src="assets/image/<?= $movie["poster"]; ?>" alt="Movie" />
          <div class="details">
            <p class="movie-name"><?= $movie["title"]; ?></p>
            <span class="movie-details"><?= $movie["language"]; ?> | <?= $movie["genre"]; ?></span>

          </div>
        </a>
      </div>
    <?php
    }
    ?>

  </main>
  <?php include("includes/footer.php"); ?>
</body>

</html>