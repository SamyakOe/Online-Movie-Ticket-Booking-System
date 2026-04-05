<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

if (!isset($_GET['id'])) {
    echo "<script>alert('No show ID provided.'); window.location.href='admin_movies.php';</script>";
    exit;
}

$showtime_id = (int)$_GET['id'];

$showtime = get_one_row($db_server,
    "SELECT s.*, m.title FROM showtime s
     INNER JOIN movies m ON s.movie_id = m.movie_id
     WHERE s.showtime_id = ?",
    [$showtime_id], "i"
);

if (!$showtime) {
    echo "<script>alert('Show not found.'); window.location.href='admin_movies.php';</script>";
    exit;
}

$errors  = [];
$success = false;

if (isset($_POST['edit_show'])) {
    $show_date = trim($_POST['show_date']);
    $show_time = trim($_POST['show_time']);

    if (empty($show_date)) $errors[] = "Show date is required.";
    if (empty($show_time)) $errors[] = "Show time is required.";

    if (empty($errors)) {
        // Block edit if confirmed bookings exist
        $booking_check = get_one_row($db_server,
            "SELECT COUNT(*) AS count FROM bookings
             WHERE showtime_id = ? AND status = 'confirmed'",
            [$showtime_id], "i"
        );

        if ($booking_check['count'] > 0) {
            $errors[] = "Cannot edit a show that has existing confirmed bookings.";
        } else {
            $query = "UPDATE showtime SET show_date = ?, show_time = ? WHERE showtime_id = ?";
            if (execute_query($db_server, $query, [$show_date, $show_time, $showtime_id], "ssi")) {
                $success = true;
                // Refresh local data
                $showtime['show_date'] = $show_date;
                $showtime['show_time'] = $show_time;
            } else {
                $errors[] = "Database error: " . mysqli_error($db_server);
            }
        }
    }
}

// Format show_time as HH:MM for the time input (strip seconds if present)
$time_value = substr($showtime['show_time'], 0, 5);
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php if (!empty($errors)): ?>
    <div class="form-errors">
        <?php foreach ($errors as $e): ?>
            <p class="form-error-item">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?>
            </p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" class="form">
    <p class="form-title">Edit Show</p>

    <label>Movie</label>
    <input type="text" value="<?= htmlspecialchars($showtime['title']) ?>" disabled>

    <label for="show_date">Show Date</label>
    <input type="date" name="show_date" id="show_date"
           value="<?= htmlspecialchars($showtime['show_date']) ?>" required>

    <label for="show_time">Show Time</label>
    <input type="time" name="show_time" id="show_time"
           value="<?= htmlspecialchars($time_value) ?>" required>

    <div class="note-box">
        <p class="note-text">
            <i class="fa-solid fa-circle-info"></i>
            Shows with existing confirmed bookings cannot be edited.
        </p>
    </div>

    <button type="submit" name="edit_show" class="button add update-btn">
        <i class="fa-solid fa-floppy-disk"></i> Update Show
    </button>
</form>

<?php if ($success): ?>
<script>
    alert('Show updated successfully!');
    window.parent.location.reload();
</script>
<?php endif; ?>

<style>
.form-errors {
    background-color: var(--error);
    border-radius: 0.5rem;
    padding: 0.8rem 1rem;
    margin: 0.5rem 1rem 0;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.form-error-item {
    color: white;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.form-error-item i { color: white; font-size: 0.85rem; }
</style>