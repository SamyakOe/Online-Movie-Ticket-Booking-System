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
    <?php
    $id = $_GET['id'];

    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $selected_showDate = $_GET["show_date"];
        $showtime = mysqli_query($db_server, "SELECT * FROM showtime WHERE movie_id='$id' AND show_date='$selected_showDate'");
        $showtime_data = [];
        while ($row = mysqli_fetch_assoc($showtime)) {
            $showtime_data[] = $row;
        }

        foreach ($showtime_data as $showtime_row) { ?>
            <div class="showtime-card" onclick="selectShowTime(this)">
                <p class="showtime"><?= date("h:i A", strtotime($showtime_row["show_time"])) ?></p>
            </div>
    <?php }
        exit;
    }

    $result = mysqli_query($db_server, "Select * from movies where movie_id=$id");
    $movie = mysqli_fetch_assoc($result);
    include("../includes/header.php"); ?>

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
                            <?= $movie["release_date"] ?>
                        </span>

                    </div>
                    <div class="inner_details_description">
                        <?= $movie["description"] ?>
                    </div>
                </div>
                <?php

                $showdate = mysqli_query($db_server, "SELECT * FROM showtime WHERE movie_id = $id GROUP BY show_date ORDER BY show_date");
                $showdate_data = [];
                while ($row = mysqli_fetch_assoc($showdate)) {
                    $showdate_data[] = $row;
                }


                ?>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Date</p>
                    <div class="showdate-container">
                        <?php foreach ($showdate_data as $showdate_row) { ?>
                            <div class="showdate-card" onclick="showShowTime(this,'<?= $showdate_row["show_date"] ?>')">
                                <p class="showdate month"><?= date("M", strtotime($showdate_row["show_date"])) ?></p>
                                <p class="showdate date"><?= date("d", strtotime($showdate_row["show_date"])) ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Time</p>
                    <div class="showtime-container">


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
                            <?php for ($i = 0; $i < 80; $i++) { ?>
                                <div class="seat"></div>

                            <?php } ?>

                        </div>
                    </div>
                </div>
            </article>
        </section>
    </main>
    <?php include("../includes/footer.php"); ?>
</body>
<script>
    function showShowTime(card, showDate) {
        selectedCard(card);
        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "movie.php?id=" + <?= $id ?> + "&show_date=" + showDate + "&ajax=1", true);
        xhttp.onload = function() {
            document.querySelector('.showtime-container').innerHTML = xhttp.responseText;
        };
        xhttp.send();
    }
    function selectShowTime(card){
        document.querySelectorAll('.showtime-card').forEach(c => c.classList.remove('selected-card'));
    
        card.classList.add('selected-card');

    }

    function selectedCard(card) {
        document.querySelectorAll('.showdate-card').forEach(c => c.classList.remove('selected-card'));

        card.classList.add('selected-card');
    }
</script>

</html>