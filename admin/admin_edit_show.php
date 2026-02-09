<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$showtime_id = $_GET['id'];

// Get showtime details with movie info
$query = "SELECT s.*, m.title FROM showtime s 
     INNER JOIN movies m ON s.movie_id = m.movie_id 
     WHERE s.showtime_id=?";
$showtime = get_one_row(
    $db_server,
    $query,
    [$showtime_id],
    "i"
);

if (!$showtime) {
    echo "<script>alert('Show not found'); window.location.href='admin_shows.php';</script>";
    exit;
}

if (isset($_POST['edit_show'])) {
    $show_date = mysqli_real_escape_string($db_server, $_POST['show_date']);
    $show_time = mysqli_real_escape_string($db_server, $_POST['show_time']);

    // Check if there are any confirmed bookings for this showtime
    $booking_check = get_one_row(
        $db_server,
        "SELECT COUNT(*) as count FROM bookings WHERE showtime_id=? AND status='confirmed'",
        [$showtime_id],
        "i"
    );

    if ($booking_check['count'] > 0) {
        echo "<script>alert('Cannot edit show with existing bookings!');</script>";
    } else {
        $query = "UPDATE showtime SET show_date=?, show_time=? WHERE showtime_id=?";
        $params = array($show_date, $show_time, $showtime_id);

        if (execute_query($db_server, $query, $params, "ssi")) {
            echo "<script>alert('Show updated successfully!'); window.parent.location.reload();</script>";
        } else {
            echo "<script>alert('Error updating show: " . mysqli_error($db_server) . "');</script>";
        }
    }
}
?>

<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<form method="POST" class="form">
    <p class="form-title">Edit Show</p>

    <label for="movie">Movie</label>
    <input type="text" value="<?= $showtime['title'] ?>" disabled>

    <label for="show_date">Show Date</label>
    <input type="date" name="show_date" value="<?= $showtime['show_date'] ?>" required>

    <label for="show_time">Show Time</label>
    <input type="time" name="show_time" value="<?= $showtime['show_time'] ?>" required>

    <div class="note-box">
        <p class="note-text">
            <i class="fa-solid fa-circle-info"></i>
            Note: Shows with existing bookings cannot be edited.
        </p>
    </div>

    <button type="submit" name="edit_show" class="button add update-btn" >Update Show</button>
</form>