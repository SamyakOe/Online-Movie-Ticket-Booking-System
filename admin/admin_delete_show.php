<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];

    // Check if there are any confirmed bookings for this showtime
    $booking_check = get_one_row(
        $db_server,
        "SELECT COUNT(*) as count FROM bookings WHERE showtime_id=? AND status='confirmed'",
        [$delete_id],
        "i"
    );

    if ($booking_check['count'] > 0) {
        echo "<script>alert('Cannot delete show with existing confirmed bookings!'); window.location.href='admin_shows.php';</script>";
        exit;
    }

    $query = "DELETE FROM showtime WHERE showtime_id=?";
    if (execute_query($db_server, $query, [$delete_id], "i")) {
        echo "<script>alert('Show deleted successfully!'); window.location.href='admin_shows.php';</script>";
    } else {
        echo "<script>alert('Error deleting show.'); window.location.href='admin_shows.php';</script>";
    }
} else {
    echo "<script>alert('No show ID provided.'); window.location.href='admin_shows.php';</script>";
}