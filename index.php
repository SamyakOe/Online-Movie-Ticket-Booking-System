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
    // Only fetch movies that have at least one upcoming showtime.
    // For each movie, grab the nearest upcoming show_date and show_time
    // so we can display it on the card and sort by it.
    $movies = get_all_rows($db_server,
      "SELECT m.*,
              MIN(TIMESTAMP(s.show_date, s.show_time)) AS next_show
       FROM movies m
       INNER JOIN showtime s ON m.movie_id = s.movie_id
       WHERE TIMESTAMP(s.show_date, s.show_time) > NOW()
       GROUP BY m.movie_id
       ORDER BY next_show ASC"
    );

    if (empty($movies)) { ?>
      <p style="color:white; grid-column: 1/-1; text-align:center; font-size:1.2rem; padding: 3rem 0;">
        No upcoming shows at the moment. Check back soon!
      </p>
    <?php } else {
      foreach ($movies as $movie) { ?>
        <div class="movie-card">
          <a href="pages/movie.php?id=<?= $movie['movie_id'] ?>">
            <img src="assets/image/<?= htmlspecialchars($movie['poster']) ?>"
                 alt="<?= htmlspecialchars($movie['title']) ?>" />
            <div class="details">
              <p class="movie-name"><?= htmlspecialchars($movie['title']) ?></p>
              <span class="movie-details">
                <?= htmlspecialchars($movie['language']) ?> | <?= htmlspecialchars($movie['genre']) ?>
              </span>

            </div>
          </a>
        </div>
      <?php }
    } ?>
  </main>

  <?php include("includes/footer.php"); ?>


</body>

</html>