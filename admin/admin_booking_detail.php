<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

if (!isset($_GET['id'])) {
    echo "<script>alert('No booking ID provided.'); window.location.href='admin_bookings.php';</script>";
    exit;
}

$booking_id = (int)$_GET['id'];

$query = "SELECT b.*,
                 m.title      AS movie_title,
                 m.poster,
                 m.genre,
                 m.duration,
                 m.language,
                 u.name       AS user_name,
                 u.email,
                 u.mobile_no,
                 s.show_date,
                 s.show_time
          FROM bookings b
          INNER JOIN movies   m ON b.movie_id    = m.movie_id
          INNER JOIN users    u ON b.user_id     = u.id
          INNER JOIN showtime s ON b.showtime_id = s.showtime_id
          WHERE b.booking_id = ?";

$booking = get_one_row($db_server, $query, [$booking_id], "i");

if (!$booking) {
    echo "<script>alert('Booking not found.'); window.location.href='admin_bookings.php';</script>";
    exit;
}

// Get seats
$seats_rows = get_all_rows(
    $db_server,
    "SELECT seat_number FROM booking_seats WHERE booking_id = ?",
    [$booking_id],
    "i"
);
$seats = implode(", ", array_column($seats_rows, 'seat_number'));
$seat_count = count($seats_rows);
?>

<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<div class="booking-detail-wrap">

    <!-- Header -->
    <div class="booking-detail-header">
        <div>
            <p class="form-title" style="margin-bottom:0.25rem;">
                Booking #<?= str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT) ?>
            </p>
            <small style="color:var(--color4);">
                Booked on <?= date("M d, Y h:i A", strtotime($booking['booking_date'])) ?>
            </small>
        </div>
        <span class="status-badge status-<?= $booking['status'] ?>" style="height:fit-content;">
            <?= ucfirst($booking['status']) ?>
        </span>
    </div>

    <!-- Poster + details side by side -->
    <div class="booking-detail-body">

        <!-- Poster -->
        <img src="../assets/image/<?= htmlspecialchars($booking['poster']) ?>"
            alt="<?= htmlspecialchars($booking['movie_title']) ?>"
            class="booking-detail-poster">

        <!-- Info tables -->
        <div class="booking-detail-info">

            <div class="detail-section">
                <p class="detail-section-title"><i class="fa-solid fa-film"></i> Movie</p>
                <table class="detail-table">
                    <tr>
                        <th>Title</th>
                        <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                    </tr>
                    <tr>
                        <th>Genre</th>
                        <td><?= htmlspecialchars($booking['genre']) ?></td>
                    </tr>
                    <tr>
                        <th>Language</th>
                        <td><?= htmlspecialchars($booking['language']) ?></td>
                    </tr>
                    <tr>
                        <th>Duration</th>
                        <td><?= $booking['duration'] ?> mins</td>
                    </tr>
                </table>
            </div>

            <div class="detail-section">
                <p class="detail-section-title"><i class="fa-solid fa-calendar-days"></i> Show</p>
                <table class="detail-table">
                    <tr>
                        <th>Date</th>
                        <td><?= date("l, F d, Y", strtotime($booking['show_date'])) ?></td>
                    </tr>
                    <tr>
                        <th>Time</th>
                        <td><?= date("h:i A",      strtotime($booking['show_time'])) ?></td>
                    </tr>
                </table>
            </div>

            <div class="detail-section">
                <p class="detail-section-title"><i class="fa-solid fa-couch"></i> Seats</p>
                <table class="detail-table">
                    <tr>
                        <th>Seats</th>
                        <td><?= htmlspecialchars($seats) ?></td>
                    </tr>
                    <tr>
                        <th>Count</th>
                        <td><?= $seat_count ?></td>
                    </tr>
                    <tr>
                        <th>Price / seat</th>
                        <td>NPR 200</td>
                    </tr>
                    <tr>
                        <th style="color:var(--color3);">Total</th>
                        <td style="color:var(--color3); font-weight:700;">
                            NPR <?= number_format($booking['total_amount'], 2) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="detail-section">
                <p class="detail-section-title"><i class="fa-solid fa-user"></i> Customer</p>
                <table class="detail-table">
                    <tr>
                        <th>Name</th>
                        <td><?= htmlspecialchars($booking['user_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($booking['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Mobile</th>
                        <td><?= htmlspecialchars($booking['mobile_no']) ?></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
    .booking-detail-wrap {
        padding: 1rem 1.2rem;
        color: white;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .booking-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .booking-detail-body {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 1.2rem;
    }

    .booking-detail-poster {
        width: 140px;
        border-radius: 0.5rem;
        object-fit: cover;
        align-self: start;
    }

    .booking-detail-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detail-section {
        background-color: var(--color1);
        border-radius: 0.75rem;
        padding: 0.8rem 1rem;
    }

    .detail-section-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--color3);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .detail-section-title i {
        color: var(--color3);
        font-size: 0.9rem;
    }

    .detail-table {
        width: 100%;
        font-size: 0.9rem;
    }

    .detail-table th {
        color: var(--color4);
        font-weight: 600;
        width: 38%;
        padding: 0.2rem 0.4rem 0.2rem 0;
        vertical-align: top;
    }

    .detail-table td {
        color: white;
        padding: 0.2rem 0;
        vertical-align: top;
    }
</style>