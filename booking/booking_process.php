<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];
    $showtime_id = $_POST['showtime_id'];
    $seats = $_POST['seats'];
    $total_amount = $_POST['total_amount'];

    // Start transaction
    mysqli_begin_transaction($db_server);

    try {
        // Insert booking
        $booking_query = "INSERT INTO bookings (user_id, movie_id, showtime_id, total_amount, status) VALUES (?, ?, ?, ?, 'confirmed')";
        if (execute_query($db_server, $booking_query, [$user_id, $movie_id, $showtime_id, $total_amount], "iiii")) {

            // Insert individual seats
            $booking_id = mysqli_insert_id($db_server);
            $seats_array = explode(", ", $seats);
            foreach ($seats_array as $seat) {
                $seat_query = "INSERT INTO booking_seats (booking_id, seat_number) VALUES (?, ?)";
                execute_query($db_server, $seat_query, [$booking_id, $seat], "is");
            }

            mysqli_commit($db_server);
            header("Location: booking_success.php?booking_id=" . $booking_id);
            exit;
        } else {
            throw new Exception("Failed to create booking");
        }
    } catch (Exception $e) {
        mysqli_rollback($db_server);
        echo "Booking failed: " . $e->getMessage();
    }
}
