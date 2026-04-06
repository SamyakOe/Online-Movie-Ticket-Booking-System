<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

// Admins are not allowed to book movies
if ((int)$_SESSION['user_role'] === 1) {
    header("Location: ../admin/admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$movie_id    = (int)$_POST['movie_id'];
$showtime_id = (int)$_POST['showtime_id'];
$seats_raw   = trim($_POST['seats']);

$seats_array  = array_filter(array_map('trim', explode(',', $seats_raw)));
$seat_count   = count($seats_array);
$total_amount = $seat_count * 200; // recalculated server-side

if ($seat_count === 0 || !$showtime_id || !$movie_id) {
    header("Location: ../index.php");
    exit;
}

mysqli_begin_transaction($db_server);

try {
    // ── Check if any selected seats are already booked ──────────────────
    $placeholders = implode(',', array_fill(0, $seat_count, '?'));
    $check_sql = "SELECT bs.seat_number
                  FROM booking_seats bs
                  INNER JOIN bookings b ON bs.booking_id = b.booking_id
                  WHERE b.showtime_id = ?
                    AND b.status = 'confirmed'
                    AND bs.seat_number IN ($placeholders)";

    $stmt = mysqli_prepare($db_server, $check_sql);
    $types = 'i' . str_repeat('s', $seat_count);
    $params = array_merge([$showtime_id], array_values($seats_array));
    $bind_args = array_merge([$stmt, $types], $params);
    $refs = [];
    foreach ($bind_args as $k => $v) {
        $refs[$k] = &$bind_args[$k];
    }
    call_user_func_array('mysqli_stmt_bind_param', $refs);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $already_booked = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $already_booked[] = $row['seat_number'];
    }
    mysqli_stmt_close($stmt);

    // ── If any seats are taken, stop and alert ───────────────────────────
    if (!empty($already_booked)) {
        mysqli_rollback($db_server);
        $seats_list = implode(', ', $already_booked);
        echo "<script>
            alert('Sorry! Seat(s) " . addslashes($seats_list) . " were just booked by someone else.\\nPlease go back and choose different seats.');
            window.location.href='../pages/movie.php?id=" . $movie_id . "';
        </script>";
        exit;
    }

    // ── Insert the booking ───────────────────────────────────────────────
    $booking_sql = "INSERT INTO bookings (user_id, movie_id, showtime_id, total_amount, status)
                    VALUES (?, ?, ?, ?, 'confirmed')";
    if (!execute_query($db_server, $booking_sql, [$user_id, $movie_id, $showtime_id, $total_amount], 'iiid')) {
        throw new Exception("Failed to create booking.");
    }
    $booking_id = mysqli_insert_id($db_server);

    // ── Insert each seat ─────────────────────────────────────────────────
    foreach ($seats_array as $seat) {
        if (!execute_query(
            $db_server,
            "INSERT INTO booking_seats (booking_id, seat_number) VALUES (?, ?)",
            [$booking_id, trim($seat)],
            'is'
        )) {
            throw new Exception("Failed to insert seat $seat.");
        }
    }

    mysqli_commit($db_server);
    header("Location: booking_success.php?booking_id=" . $booking_id);
    exit;
} catch (Exception $e) {
    mysqli_rollback($db_server);
    echo "<script>
        alert('Booking failed. Please try again.');
        window.location.href='../pages/movie.php?id=" . $movie_id . "';
    </script>";
}