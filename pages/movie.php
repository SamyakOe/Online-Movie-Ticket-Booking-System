<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
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

    // AJAX request to load showtimes
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $selected_showDate = $_GET["show_date"];

        $params = array($id, $selected_showDate);
        $showtime_data = get_all_rows($db_server, "SELECT * FROM showtime WHERE movie_id= ? AND show_date= ?", $params, "is");

        foreach ($showtime_data as $showtime_row) { ?>
            <div class="showtime-card" data-showtimeid="<?= $showtime_row['showtime_id'] ?>" onclick="selectShowTime(this)">
                <p class="showtime"><?= date("h:i A", strtotime($showtime_row["show_time"])) ?></p>
            </div>
    <?php }
        exit;
    }

    // AJAX request to get booked seats for selected showtime
    if (isset($_GET['get_booked_seats']) && isset($_GET['showTimeId'])) {
        $showtime_id = $_GET['showTimeId'];

        // Get all bookings for this showtime
        $query = "SELECT bs.seat_number 
                  FROM booking_seats bs
                  INNER JOIN bookings b ON bs.booking_id = b.booking_id
                  WHERE b.showtime_id = ? AND b.status = 'confirmed'";

        $booked_seats = get_all_rows($db_server, $query, [$showtime_id], "i");

        // Return as JSON
        $seats_array = array_column($booked_seats, 'seat_number');
        echo json_encode($seats_array);
        exit;
    }

    $movie = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$id], "i");
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

                $query = "SELECT * FROM showtime WHERE movie_id = ? GROUP BY show_date ORDER BY show_date";

                $showdate_data = get_all_rows($db_server, $query, [$id], "i");

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
                    <div class="showtime-container"></div>
                </div>
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Seat</p>
                    <div class="seatSelectionContainer">
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
                                <?php for ($i = 0; $i < 80; $i++) {
                                    $row = chr(65 + floor($i / 10));
                                    $col = ($i % 10) + 1;
                                    $seatLabel = $row . $col;
                                ?>
                                    <div class="seat" data-seatlabel="<?= $seatLabel ?>"></div>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="seat-indicator">
                            <div class="seat-available"></div>
                            <span>Available</span>
                            <div class="seat-booked"></div>
                            <span>Booked</span>
                            <div class="seat-selected"></div>
                            <span>Selected</span>

                        </div>
                    </div>
                </div>
                <div class="inner-details-block seat-counter">
                    <p class="inner-details-sub-title"><span>Selected Seats</span> <span>Total Amount</span></p>
                    <div class="seat-data">
                        <span class="selected-seats"></span><span class="total-seat-amount"></span>
                    </div>
                    <form action="../booking/booking_confirm.php" method="POST" id="bookingForm">
                        <input type="hidden" name="movie_id" value="<?= $id ?>">
                        <input type="hidden" name="showtime_id" id="showtime_id" value="">
                        <input type="hidden" name="seats" id="selected_seats_input" value="">
                        <input type="hidden" name="total_amount" id="total_amount_input" value="">
                        <button type="submit" class="button confirm-booking">Confirm Booking</button>
                    </form>

                </div>
            </article>
        </section>
    </main>
    <?php include("../includes/footer.php"); ?>
</body>
<script>
    let selectedShowTimeId = null;

    function showShowTime(card, showDate) {
        selectedCard(card);
        resetSeats();
        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "movie.php?id=" + <?= $id ?> + "&show_date=" + showDate + "&ajax=1", true);
        xhttp.onload = function() {
            document.querySelector('.showtime-container').innerHTML = xhttp.responseText;
        };
        xhttp.send();
    }

    function selectShowTime(card) {
        document.querySelectorAll('.showtime-card').forEach(c => c.classList.remove('selected-card'));

        card.classList.add('selected-card');
        selectedShowTimeId = card.dataset.showtimeid;
        loadBookedSeats(selectedShowTimeId);
    }

    function selectedCard(card) {
        document.querySelectorAll('.showdate-card').forEach(c => c.classList.remove('selected-card'));

        card.classList.add('selected-card');

    }

    function loadBookedSeats(id) {
        document.querySelector(".seatSelectionContainer").style.display = "block";
        resetSeats();

        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "movie.php?get_booked_seats=1&showTimeId=" + id + "&ajax=1", true);
        xhttp.onload = function() {
            const bookedSeats = JSON.parse(response);
            bookedSeats.forEach(seatNumber => {
                const seatElement = document.querySelector(`.seat[data-seatlabel="${seatNumber}"]`);
                seatElement.classList.add('seat-booked'); // Makes it gray
                seatElement.style.pointerEvents = 'none'; // Can't click it
            })
        };
        xhttp.send();

    }

    document.querySelectorAll(".seat").forEach((seat) => {
        seat.addEventListener("click", function() {

            //Can't clicked booked seats
            if (seat.classList.contains('seat-booked')) {
                return
            }

            //Toogle seat selection
            seat.classList.toggle("seat-selected");
            updateSelectedSeats();
        });
    });

    function updateSelectedSeats() {
        //Get selected seats
        const selectedSeats = document.querySelectorAll(".seat.seat-selected");

        const price = 200
        const amount = selectedSeats.length * price;

        //Get seat labels
        const seatLabels = Array.from(selectedSeats).map(seat => seat.dataset.seatlabel);

        //Show or hide Seat Counter
        if (selectedSeats.length > 0) {
            document.querySelector(".seat-counter").style.display = "flex";
        } else {
            document.querySelector(".seat-counter").style.display = "none";
        }

        //Display Selected Seats and amount
        document.querySelector(".selected-seats").textContent = seatLabels.join(", ");
        document.querySelector(".total-seat-amount").textContent = "NPR " + amount;

        //Update Hidden form values
        document.getElementById('showtime_id').value = selectedShowTimeId;
        document.getElementById('selected_seats_input').value = seatLabels.join(", ");
        document.getElementById('total_amount_input').value = amount;
    }

    function resetSeats() {
        document.querySelectorAll('.seat').forEach(seat => {
            seat.classList.remove('seat-selected');
        });
        updateSelectedSeats();
    }
</script>

</html>