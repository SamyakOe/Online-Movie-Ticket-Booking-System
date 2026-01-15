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

// Get booking details with movie and showtime info
$query = "SELECT b.*, m.title, m.poster, m.genre, m.duration, m.language,
                 s.show_date, s.show_time, u.name as user_name, u.email, u.mobile_no
          FROM bookings b
          INNER JOIN movies m ON b.movie_id = m.movie_id
          INNER JOIN showtime s ON b.showtime_id = s.showtime_id
          INNER JOIN users u ON b.user_id = u.id
          WHERE b.booking_id = ? AND b.user_id = ?";

$booking = get_one_row($db_server, $query, [$booking_id, $_SESSION['user_id']], "ii");

if (!$booking) {
    echo "<script>alert('Booking not found'); window.location.href='../index.php';</script>";
    exit;
}

// Get booked seats
$seats_query = "SELECT seat_number FROM booking_seats WHERE booking_id = ?";
$seats_result = get_all_rows($db_server, $seats_query, [$booking_id], "i");
$seats = array_column($seats_result, 'seat_number');
$seats_string = implode(", ", $seats);
$seat_count = count($seats);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Booking Confirmed - MovieBook</title>
    <style>
        
    </style>
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
                <!-- Movie Poster -->
                <article class="inner-poster">
                    <img src="../assets/image/<?= $booking["poster"]; ?>" alt="<?= $booking["title"]; ?>">
                </article>

                <!-- Booking Details -->
                <article class="inner-details-body">

                    <!-- Movie Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Movie Details</p>
                        <table>
                            <tr>
                                <th>Movie:</th>
                                <td><?= $booking["title"] ?></td>
                            </tr>
                            <tr>
                                <th>Genre:</th>
                                <td><?= $booking["genre"] ?></td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td><?= $booking["duration"] ?> mins</td>
                            </tr>
                            <tr>
                                <th>Language:</th>
                                <td><?= $booking["language"] ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Show Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Show Details</p>
                        <table>
                            <tr>
                                <th>Date:</th>
                                <td><?= date("l, F d, Y", strtotime($booking["show_date"])) ?></td>
                            </tr>
                            <tr>
                                <th>Time:</th>
                                <td><?= date("h:i A", strtotime($booking["show_time"])) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Seat Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Seat Details</p>
                        <table>
                            <tr>
                                <th>Booked Seats:</th>
                                <td style="color: var(--color3); font-weight: 600;"><?= $seats_string ?></td>
                            </tr>
                            <tr>
                                <th>Number of Seats:</th>
                                <td><?= $seat_count ?></td>
                            </tr>
                            <tr>
                                <th>Price per Seat:</th>
                                <td>NPR 200</td>
                            </tr>
                            <tr>
                                <th style="padding-top: 1.5rem; font-size: 1.5rem;">Total Amount Paid:</th>
                                <th style="padding-top: 1.5rem; color: var(--color3); font-size: 1.5rem;">NPR <?= number_format($booking["total_amount"], 2) ?></th>
                            </tr>
                        </table>
                    </div>

                    <!-- Customer Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Customer Details</p>
                        <table>
                            <tr>
                                <th>Name:</th>
                                <td><?= $booking["user_name"] ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $booking["email"] ?></td>
                            </tr>
                            <tr>
                                <th>Mobile:</th>
                                <td><?= $booking["mobile_no"] ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="inner-details-block flex-row-gap">
                        <a href="../pages/my_bookings.php" style="flex: 1;">
                            <button class="button full-width-button">
                                <span class="material-symbols-outlined">receipt_long</span>
                                View All Bookings
                            </button>
                        </a>
                        <a href="../index.php" style="flex: 1;">
                            <button class="button home-button" >
                                <span class="material-symbols-outlined">home</span>
                                Back to Home
                            </button>
                        </a>
                    </div>

                    <!-- Important Note -->
                    <div class="inner-details-block info-block" >
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fa-solid fa-circle-info"></i> Important Information
                        </p>
                        <ul style="margin-left: 1.5rem; line-height: 1.6;">
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