<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

$user_id = $_SESSION['user_id'];

$query = "SELECT b.*, m.title, m.poster, m.genre, s.show_date, s.show_time
          FROM bookings b
          INNER JOIN movies   m ON b.movie_id    = m.movie_id
          INNER JOIN showtime s ON b.showtime_id = s.showtime_id
          WHERE b.user_id = ?
          ORDER BY b.booking_date DESC";
$bookings = get_all_rows($db_server, $query, [$user_id], "i");

// Split into upcoming and past
$upcoming = [];
$past     = [];
foreach ($bookings as $b) {
    $dt = strtotime($b['show_date'] . ' ' . $b['show_time']);
    if ($dt >= time() && $b['status'] === 'confirmed') {
        $upcoming[] = $b;
    } else {
        $past[] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>My Bookings — MovieBook</title>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="app">
        <div class="bookings-header-block">
            <p class="inner-details-title">My Bookings</p>
            <p class="inner_details_description">View and manage all your movie bookings</p>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-ticket"></i>
                <h2>No Bookings Yet</h2>
                <p>You haven't booked any movies yet. Start exploring and book your first show!</p>
                <a href="../index.php">
                    <button class="button bookings-browse-btn">
                        <span class="material-symbols-outlined">movie</span>Browse Movies
                    </button>
                </a>
            </div>

        <?php else: ?>

            <?php
            // Render a group of booking cards
            function renderBookings(array $bookings, $db_server): void {
                foreach ($bookings as $booking):
                    $seats_result = get_all_rows($db_server,
                        "SELECT seat_number FROM booking_seats WHERE booking_id = ?",
                        [$booking['booking_id']], "i"
                    );
                    $seats_string = implode(", ", array_column($seats_result, 'seat_number'));
                    $is_past = strtotime($booking['show_date'] . ' ' . $booking['show_time']) < time();
            ?>
                <div class="booking-card <?= $is_past ? 'booking-card-past' : '' ?>">
                    <div class="booking-poster">
                        <img src="../assets/image/<?= htmlspecialchars($booking['poster']) ?>"
                             alt="<?= htmlspecialchars($booking['title']) ?>">
                    </div>
                    <div class="booking-info">
                        <p class="booking-title"><?= htmlspecialchars($booking['title']) ?></p>
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
                            <span>Seats: <?= $seats_string ?: '—' ?></span>
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
                            <button class="button booking-view-btn">
                                <span class="material-symbols-outlined">visibility</span>View Details
                            </button>
                        </a>
                        <?php if ($booking['status'] === 'confirmed' && !$is_past): ?>
                            <button class="button booking-cancel-btn"
                                    onclick="confirmCancel(<?= $booking['booking_id'] ?>)">
                                <span class="material-symbols-outlined">cancel</span>Cancel Booking
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach;
            }
            ?>

            <!-- Upcoming -->
            <?php if (!empty($upcoming)): ?>
                <div class="bookings-group-label">
                    <i class="fa-solid fa-calendar-days"></i> Upcoming
                    <span class="bookings-group-count"><?= count($upcoming) ?></span>
                </div>
                <?php renderBookings($upcoming, $db_server); ?>
            <?php endif; ?>

            <!-- Past / cancelled -->
            <?php if (!empty($past)): ?>
                <div class="bookings-group-label bookings-group-label-past">
                    <i class="fa-solid fa-clock-rotate-left"></i> Past &amp; Cancelled
                    <span class="bookings-group-count"><?= count($past) ?></span>
                </div>
                <?php renderBookings($past, $db_server); ?>
            <?php endif; ?>

        <?php endif; ?>
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