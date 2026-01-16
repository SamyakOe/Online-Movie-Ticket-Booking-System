<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

$user_id = $_SESSION['user_id'];

// Get all bookings for the user
$query = "SELECT b.*, m.title, m.poster, m.genre, s.show_date, s.show_time
          FROM bookings b
          INNER JOIN movies m ON b.movie_id = m.movie_id
          INNER JOIN showtime s ON b.showtime_id = s.showtime_id
          WHERE b.user_id = ?
          ORDER BY b.booking_date DESC";

$bookings = get_all_rows($db_server, $query, [$user_id], "i");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>My Bookings - MovieBook</title>
    <style>

    </style>
</head>

<body>
    <?php include("../includes/header.php"); ?>

    <main class="app">
        <div class="inner-details-block" style="margin-bottom: 2rem;">
            <p class="inner-details-title">My Bookings</p>
            <p class="inner_details_description">View and manage all your movie bookings</p>
        </div>

        <?php if (empty($bookings)) { ?>
            <div class="empty-state">
                <i class="fa-solid fa-ticket"></i>
                <h2>No Bookings Yet</h2>
                <p>You haven't booked any movies yet. Start exploring and book your first show!</p>
                <a href="../index.php">
                    <button class="button" style="margin-top: 1.5rem;">
                        <span class="material-symbols-outlined">movie</span>
                        Browse Movies
                    </button>
                </a>
            </div>
        <?php } else { ?>
            <?php foreach ($bookings as $booking) {
                // Get seats for this booking
                $seats_query = "SELECT seat_number FROM booking_seats WHERE booking_id = ?";
                $seats_result = get_all_rows($db_server, $seats_query, [$booking['booking_id']], "i");
                $seats = array_column($seats_result, 'seat_number');
                $seats_string = implode(", ", $seats);

                // Check if show is in the past
                $show_datetime = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
                $is_past = $show_datetime < time();
            ?>
                <div class="booking-card">
                    <div class="booking-poster">
                        <img src="../assets/image/<?= $booking['poster'] ?>" alt="<?= $booking['title'] ?>">
                    </div>

                    <div class="booking-info">
                        <p class="booking-title"><?= $booking['title'] ?></p>
                        <div class="booking-detail">
                            <i class="fa-solid fa-calendar"></i>
                            <span><?= date("l, F d, Y", strtotime($booking['show_date'])) ?></span>
                        </div>
                        <div class="booking-detail">
                            <i class="fa-solid fa-clock"></i>
                            <span><?= date("h:i A", strtotime($booking['show_time'])) ?></span>
                        </div>
                        <div class="booking-detail">
                            <i class="fa-solid fa-couch"></i>
                            <span>Seats: <?= $seats_string ?></span>
                        </div>
                        <div class="booking-detail">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Booking ID: #<?= str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <div class="booking-detail">
                            <i class="fa-solid fa-money-bill"></i>
                            <span>Total: NPR <?= number_format($booking['total_amount'], 2) ?></span>
                        </div>
                    </div>

                    <div class="booking-actions">
                        <div class="status-badge status-<?= $booking['status'] ?>">
                            <?= ucfirst($booking['status']) ?>
                        </div>

                        <a href="../booking/booking_success.php?booking_id=<?= $booking['booking_id'] ?>">
                            <button class="button" style="width: 100%;">
                                <span class="material-symbols-outlined">visibility</span>
                                View Details
                            </button>
                        </a>

                        <?php if ($booking['status'] === 'confirmed' && !$is_past) { ?>
                            <button class="button" onclick="confirmCancel(<?= $booking['booking_id'] ?>)"
                                style="width: 100%; background-color: var(--error);">
                                <span class="material-symbols-outlined">cancel</span>
                                Cancel Booking
                            </button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </main>

    <?php include("../includes/footer.php"); ?>

    <script>
        function confirmCancel(bookingId) {
            if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                window.location.href = '../booking/cancel_booking.php?booking_id=' + bookingId;
            }
        }
    </script>
</body>

</html>