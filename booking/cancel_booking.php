<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify booking belongs to user and is confirmed
    $booking = get_one_row($db_server, 
        "SELECT * FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'confirmed'",
        [$booking_id, $user_id], "ii");
    
    if ($booking) {
        // Update booking status to cancelled
        $query = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?";
        if (execute_query($db_server, $query, [$booking_id], "i")) {
            echo "<script>alert('Booking cancelled successfully'); window.location.href='../pages/mybookings.php';</script>";
        }
    } else {
        echo "<script>alert('Booking not found or already cancelled'); window.location.href='../pages/mybookings.php';</script>";
    }
}
?>