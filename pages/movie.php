<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");

$id = $_GET['id'] ?? null;

// AJAX: booked seats for a showtime
if (isset($_GET['get_booked_seats']) && isset($_GET['showTimeId'])) {
    $showtime_id = $_GET['showTimeId'];
    $query = "SELECT bs.seat_number
              FROM booking_seats bs
              INNER JOIN bookings b ON bs.booking_id = b.booking_id
              WHERE b.showtime_id = ? AND b.status = 'confirmed'";
    $booked_seats = get_all_rows($db_server, $query, [$showtime_id], "i");
    header('Content-Type: application/json');
    echo json_encode(array_column($booked_seats, 'seat_number'));
    exit;
}

// AJAX: showtimes for a date
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $selected_showDate = $_GET["show_date"];
    $showtime_data = get_all_rows($db_server,
        "SELECT * FROM showtime WHERE movie_id = ? AND show_date = ?",
        [$id, $selected_showDate], "is"
    );
    if (empty($showtime_data)) {
        echo '<p class="no-showtime-msg">No showtimes available for this date.</p>';
    } else {
        foreach ($showtime_data as $showtime_row) { ?>
            <div class="showtime-card" data-showtimeid="<?= $showtime_row['showtime_id'] ?>" onclick="selectShowTime(this)">
                <p class="showtime"><?= date("h:i A", strtotime($showtime_row["show_time"])) ?></p>
            </div>
        <?php }
    }
    exit;
}

$movie = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$id], "i");

$query = "SELECT * FROM showtime
          WHERE movie_id = ? AND TIMESTAMP(show_date, show_time) > NOW()
          GROUP BY show_date
          ORDER BY show_date";
$showdate_data = get_all_rows($db_server, $query, [$id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <title><?= htmlspecialchars($movie['title']) ?> — MovieBook</title>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <main class="app">
        <section class="body-container">

            <!-- Poster sidebar -->
            <article class="inner-poster">
                <img src="../assets/image/<?= htmlspecialchars($movie['poster']) ?>"
                     alt="<?= htmlspecialchars($movie['title']) ?>">
                <div class="inner-poster-details">
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Director</p>
                        <p class="inner-poster-details-content"><?= htmlspecialchars($movie['director']) ?></p>
                    </div>
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Cast</p>
                        <p class="inner-poster-details-content"><?= htmlspecialchars($movie['cast']) ?></p>
                    </div>
                    <div class="inner-poster-details-list">
                        <p class="inner-poster-details-title">Genre</p>
                        <p class="inner-poster-details-content"><?= htmlspecialchars($movie['genre']) ?></p>
                    </div>
                </div>
            </article>

            <!-- Main details -->
            <article class="inner-details-body">

                <div class="inner-details-block">
                    <p class="inner-details-title"><?= htmlspecialchars($movie['title']) ?></p>
                    <div class="inner_details-sub_detail">
                        <span><i class="fa-solid fa-clock"></i><?= $movie['duration'] ?> mins</span>
                        <span><i class="fa-solid fa-calendar-days"></i><?= $movie['release_date'] ?></span>
                    </div>
                    <div class="inner_details_description"><?= htmlspecialchars($movie['description']) ?></div>
                </div>

                <!-- Date selection -->
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Date</p>
                    <?php if (empty($showdate_data)): ?>
                        <p class="no-shows-notice">
                            <i class="fa-solid fa-circle-info"></i>
                            No upcoming shows scheduled for this movie.
                        </p>
                    <?php else: ?>
                        <div class="showdate-container">
                            <?php foreach ($showdate_data as $showdate_row): ?>
                                <div class="showdate-card"
                                     onclick="showShowTime(this,'<?= $showdate_row['show_date'] ?>')">
                                    <p class="showdate month"><?= date("M", strtotime($showdate_row['show_date'])) ?></p>
                                    <p class="showdate date"><?= date("d",  strtotime($showdate_row['show_date'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Time selection -->
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Time</p>
                    <div class="showtime-container">
                        <?php if (!empty($showdate_data)): ?>
                            <p class="select-date-hint">
                                <i class="fa-solid fa-arrow-up"></i> Pick a date above first
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seat selection -->
                <div class="inner-details-block">
                    <p class="inner-details-sub-title">Select Seat</p>
                    <div class="seatSelectionContainer">
                        <div class="screen-container">
                            <div class="screen"></div>
                            <p>Screen</p>
                        </div>
                        <div class="seat-container">
                            <div class="seat-label">
                                <?php foreach (['A','B','C','D','E','F','G','H'] as $r): ?>
                                    <span><?= $r ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="seat-map">
                                <?php for ($i = 0; $i < 80; $i++):
                                    $row = chr(65 + floor($i / 10));
                                    $col = ($i % 10) + 1;
                                ?>
                                    <div class="seat" data-seatlabel="<?= $row . $col ?>"></div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="seat-indicator">
                            <div class="seat-available"></div><span>Available</span>
                            <div class="seat-booked"></div><span>Booked</span>
                            <div class="seat-selected"></div><span>Selected</span>
                        </div>
                        <p class="select-seat-hint" id="select-seat-hint">
                            <i class="fa-solid fa-arrow-up"></i> Select a showtime first
                        </p>
                    </div>
                </div>

                <!-- Booking summary + submit -->
                <div class="inner-details-block seat-counter">
                    <p class="inner-details-sub-title">
                        <span>Selected Seats</span>
                        <span>Total Amount</span>
                    </p>
                    <div class="seat-data">
                        <span class="selected-seats"></span>
                        <span class="total-seat-amount"></span>
                    </div>
                    <form action="../booking/booking_confirm.php" method="POST" id="bookingForm">
                        <input type="hidden" name="movie_id"    value="<?= $id ?>">
                        <input type="hidden" name="showtime_id" id="showtime_id"          value="">
                        <input type="hidden" name="seats"       id="selected_seats_input" value="">
                        <input type="hidden" name="total_amount" id="total_amount_input"  value="">
                        <button type="submit" class="button confirm-booking">Confirm Booking</button>
                    </form>
                </div>

            </article>
        </section>
    </main>

    <?php include("../includes/footer.php"); ?>

<script>
    let selectedShowTimeId = null;

    function showShowTime(card, showDate) {
        selectedCard(card);
        resetSeats();
        // Reset time container to loading state
        document.querySelector('.showtime-container').innerHTML =
            '<p class="select-date-hint"><i class="fa-solid fa-spinner fa-spin"></i> Loading times…</p>';

        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "movie.php?id=<?= $id ?>&show_date=" + showDate + "&ajax=1", true);
        xhttp.onload = function () {
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

    function loadBookedSeats(showtimeId) {
        // Show the seat map, hide the "select showtime first" hint
        document.querySelector('.seatSelectionContainer').style.display = 'block';
        const hint = document.getElementById('select-seat-hint');
        if (hint) hint.style.display = 'none';
        resetSeats();

        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "movie.php?get_booked_seats=1&showTimeId=" + showtimeId, true);
        xhttp.onload = function () {
            try {
                const bookedSeats = JSON.parse(xhttp.responseText);
                bookedSeats.forEach(seatNumber => {
                    const el = document.querySelector(`.seat[data-seatlabel="${seatNumber}"]`);
                    if (el) {
                        el.classList.add('seat-booked');
                        el.style.pointerEvents = 'none';
                    }
                });
            } catch (e) {
                console.error('Error parsing booked seats:', e);
            }
        };
        xhttp.send();
    }

    document.querySelectorAll('.seat').forEach(seat => {
        seat.addEventListener('click', function () {
            if (seat.classList.contains('seat-booked')) return;
            seat.classList.toggle('seat-selected');
            updateSelectedSeats();
        });
    });

    function updateSelectedSeats() {
        const selectedSeats = document.querySelectorAll('.seat.seat-selected');
        const price  = 200;
        const amount = selectedSeats.length * price;
        const labels = Array.from(selectedSeats).map(s => s.dataset.seatlabel);

        document.querySelector('.seat-counter').style.display =
            selectedSeats.length > 0 ? 'flex' : 'none';

        document.querySelector('.selected-seats').textContent    = labels.join(', ');
        document.querySelector('.total-seat-amount').textContent = 'NPR ' + amount;
        document.getElementById('showtime_id').value             = selectedShowTimeId;
        document.getElementById('selected_seats_input').value    = labels.join(', ');
        document.getElementById('total_amount_input').value      = amount;
    }

    function resetSeats() {
        document.querySelectorAll('.seat').forEach(seat => {
            seat.classList.remove('seat-selected', 'seat-booked');
            seat.style.pointerEvents = 'auto';
        });
        updateSelectedSeats();
    }

    // Guard: block form submit if no seats or no showtime selected
    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            e.preventDefault();
            window.location.href = '../auth/login.php';
            return;
        <?php endif; ?>

        if (!selectedShowTimeId) {
            e.preventDefault();
            alert('Please select a showtime before booking.');
            return;
        }
        const selected = document.querySelectorAll('.seat.seat-selected');
        if (selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one seat before booking.');
        }
    });
</script>
</body>
</html>