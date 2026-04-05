<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

if (!isset($_GET['booking_id'])) {
    header("Location: ../index.php");
    exit;
}

$booking_id = $_GET['booking_id'];
$query = "SELECT b.*, m.title, m.poster, m.genre, m.duration, m.language,
                 s.show_date, s.show_time,
                 u.name AS user_name, u.email, u.mobile_no
          FROM bookings b
          INNER JOIN movies   m ON b.movie_id    = m.movie_id
          INNER JOIN showtime s ON b.showtime_id = s.showtime_id
          INNER JOIN users    u ON b.user_id     = u.id
          WHERE b.booking_id = ? AND b.user_id = ?";
$booking = get_one_row($db_server, $query, [$booking_id, $_SESSION['user_id']], "ii");

if (!$booking) {
    echo "<script>alert('Booking not found'); window.location.href='../index.php';</script>";
    exit;
}

$seats_result = get_all_rows($db_server,
    "SELECT seat_number FROM booking_seats WHERE booking_id = ?", [$booking_id], "i");
$seats        = array_column($seats_result, 'seat_number');
$seats_string = implode(", ", $seats);
$seat_count   = count($seats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Booking Confirmed — MovieBook</title>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <main class="app">
        <section class="confirm-body-container">

            <div class="inner-details-block success-message">
                <i class="fa-solid fa-circle-check"></i>
                <h2>Booking Confirmed Successfully!</h2>
                <p>Your tickets have been booked. Please save this information.</p>
            </div>

            <div class="inner-details-block">
                <p class="inner-details-sub-title">Booking Reference</p>
                <div class="booking-reference">
                    BOOKING #<?= str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT) ?>
                </div>
                <p class="booked-on-text">
                    Booked on: <?= date("F d, Y h:i A", strtotime($booking['booking_date'])) ?>
                </p>
            </div>

            <div class="confirm-inner-body">
                <article class="inner-poster">
                    <img src="../assets/image/<?= htmlspecialchars($booking['poster']) ?>"
                         alt="<?= htmlspecialchars($booking['title']) ?>">
                </article>

                <article class="inner-details-body">

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Movie Details</p>
                        <table class="confirm-table">
                            <tr><th>Movie</th>    <td><?= htmlspecialchars($booking['title'])    ?></td></tr>
                            <tr><th>Genre</th>    <td><?= htmlspecialchars($booking['genre'])    ?></td></tr>
                            <tr><th>Duration</th> <td><?= $booking['duration'] ?> mins</td></tr>
                            <tr><th>Language</th> <td><?= htmlspecialchars($booking['language']) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Show Details</p>
                        <table class="confirm-table">
                            <tr><th>Date</th> <td><?= date("l, F d, Y", strtotime($booking['show_date'])) ?></td></tr>
                            <tr><th>Time</th> <td><?= date("h:i A",      strtotime($booking['show_time'])) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Seat Details</p>
                        <table class="confirm-table">
                            <tr><th>Booked Seats</th>    <td class="confirm-seats"><?= htmlspecialchars($seats_string) ?></td></tr>
                            <tr><th>Number of Seats</th> <td><?= $seat_count ?></td></tr>
                            <tr><th>Price per Seat</th>  <td>NPR 200</td></tr>
                            <tr class="confirm-total-row">
                                <th>Total Amount Paid</th>
                                <th class="confirm-total-amount">NPR <?= number_format($booking['total_amount'], 2) ?></th>
                            </tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Customer Details</p>
                        <table class="confirm-table">
                            <tr><th>Name</th>   <td><?= htmlspecialchars($booking['user_name']) ?></td></tr>
                            <tr><th>Email</th>  <td><?= htmlspecialchars($booking['email'])     ?></td></tr>
                            <tr><th>Mobile</th> <td><?= htmlspecialchars($booking['mobile_no']) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block success-action-row">
                        <a href="../pages/mybookings.php" class="success-action-link">
                            <button class="button success-btn-primary">
                                <span class="material-symbols-outlined">receipt_long</span>View All Bookings
                            </button>
                        </a>
                        <a href="../index.php" class="success-action-link">
                            <button class="button success-btn-home">
                                <span class="material-symbols-outlined">home</span>Back to Home
                            </button>
                        </a>
                    </div>

                    <div class="inner-details-block info-block">
                        <p class="info-block-title">
                            <i class="fa-solid fa-circle-info"></i> Important Information
                        </p>
                        <ul class="info-block-list">
                            <li>Please arrive at least 15 minutes before showtime</li>
                            <li>Carry a valid ID proof for verification</li>
                            <li>Show this booking reference at the counter</li>
                            <li>Outside food and beverages are not allowed</li>
                        </ul>
                    </div>

                </article>
            </div>
        </section>
    </main>
    <?php include("../includes/footer.php"); ?>
</body>
</html>