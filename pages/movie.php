<?php
session_start();
include("../includes/connection.php");
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
   <?php include("../includes/header.php");?>
    <?php
    $id = $_GET['id'];
    $result = mysqli_query($db_server, "Select * from movies where movie_id=$id");
    $movie = mysqli_fetch_assoc($result);
    ?>
    <main class="app">
        <section class="body-container">
            <article class="inner-poster">
                <img src="../assets/image/<?= $movie["poster"]; ?>" alt="<?= $movie["title"]; ?>">
                <div class="inner-poster-details">
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Director</p>
                        <p class="inner-poster-details-content"><?= $movie["director"]; ?></p>
                    </div>
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Cast</p>
                        <p class="inner-poster-details-content"><?= $movie["cast"]; ?></p>
                    </div>
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Genre</p>
                        <p class="inner-poster-details-content"><?= $movie["genre"]; ?></p>
                    </div>

                </div>
            </article>
            <article class="inner-details-body">
                <div class="inner-details-block">

                    <p class="inner-details-title"><?= $movie["title"] ?></p>
                    <div class="inner_details-sub_detail">
                        <span>
                            <i class="fa-solid fa-clock"></i>
                            <?= $movie["duration"] ?> mins
                        </span>
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            <?= $movie["release_date"] ?> mins
                        </span>

                    </div>
                    <div class="inner_details_description">
                        <?= $movie["description"] ?>
                    </div>
                </div>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Date</p>
                    <div class="showdate-container">
                        <div class="showdate-card">
                            <p class="showdate month">Nov</p>
                            <p class="showdate date">4</p>
                        </div>
                    </div>
                </div>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Time</p>
                    <div class="showtime-container">
                        <div class="showtime-card">
                            <p class="showtime">9:00 AM</p>
                        </div>
                    </div>
                </div>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Seat</p>
                    <div class="screen-container">
                        <div class="screen"></div>
                        <p>Screen</p>
                    </div>
                    <div class="seat-container">
                        <div class="seat-label">
                            <span>A</span>
                            <span>B</span>
                            <span>C</span>
                            <span>D</span>
                            <span>E</span>
                            <span>F</span>
                            <span>G</span>
                            <span>H</span>
                        </div>
                        <div class="seat-map">
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                            <div class="seat"></div>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </main>
    <?php include("../includes/footer.php");?>
</body>

</html>