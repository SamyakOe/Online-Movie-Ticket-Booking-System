<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

// Server-side guard: redirect back if no seats submitted
if (empty($_POST['seats']) || empty($_POST['showtime_id'])) {
    header("Location: ../index.php");
    exit;
}

$movie_id    = $_POST['movie_id'];
$showtime_id = $_POST['showtime_id'];
$seats       = $_POST['seats'];

// Always calculate amount server-side — never trust POST
$seats_array  = array_filter(array_map('trim', explode(',', $seats)));
$seat_count   = count($seats_array);
$total_amount = $seat_count * 200;

$movie    = get_one_row($db_server, "SELECT * FROM movies   WHERE movie_id    = ?", [$movie_id],    "i");
$showtime = get_one_row($db_server, "SELECT * FROM showtime WHERE showtime_id = ?", [$showtime_id], "i");
$user     = get_one_row($db_server, "SELECT * FROM users    WHERE id          = ?", [$_SESSION['user_id']], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Confirm Booking — MovieBook</title>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <main class="app">
        <section class="confirm-body-container">

            <div class="inner-details-block">
                <p class="inner-details-title">Confirm Your Booking</p>
                <p class="inner_details_confirm_description">
                    Please review your booking details before confirming
                </p>
            </div>

            <div class="confirm-inner-body">
                <article class="inner-poster">
                    <img src="../assets/image/<?= htmlspecialchars($movie['poster']) ?>"
                         alt="<?= htmlspecialchars($movie['title']) ?>">
                </article>

                <article class="inner-details-body">

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Movie Details</p>
                        <table class="confirm-table">
                            <tr><th>Movie</th>    <td><?= htmlspecialchars($movie['title'])    ?></td></tr>
                            <tr><th>Genre</th>    <td><?= htmlspecialchars($movie['genre'])    ?></td></tr>
                            <tr><th>Duration</th> <td><?= $movie['duration'] ?> mins</td></tr>
                            <tr><th>Language</th> <td><?= htmlspecialchars($movie['language']) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Show Details</p>
                        <table class="confirm-table">
                            <tr><th>Date</th> <td><?= date("l, F d, Y", strtotime($showtime['show_date'])) ?></td></tr>
                            <tr><th>Time</th> <td><?= date("h:i A",      strtotime($showtime['show_time'])) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Seat Details</p>
                        <table class="confirm-table">
                            <tr><th>Selected Seats</th>  <td class="confirm-seats"><?= htmlspecialchars($seats) ?></td></tr>
                            <tr><th>Number of Seats</th> <td><?= $seat_count ?></td></tr>
                            <tr><th>Price per Seat</th>  <td>NPR 200</td></tr>
                            <tr class="confirm-total-row">
                                <th>Total Amount</th>
                                <th class="confirm-total-amount">NPR <?= $total_amount ?></th>
                            </tr>
                        </table>
                    </div>

                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Customer Details</p>
                        <table class="confirm-table">
                            <tr><th>Name</th>   <td><?= htmlspecialchars($user['name'])      ?></td></tr>
                            <tr><th>Email</th>  <td><?= htmlspecialchars($user['email'])     ?></td></tr>
                            <tr><th>Mobile</th> <td><?= htmlspecialchars($user['mobile_no']) ?></td></tr>
                        </table>
                    </div>

                    <div class="inner-details-block confirm-cancel">
                        <form action="booking_process.php" method="POST" class="confirm-form">
                            <input type="hidden" name="movie_id"     value="<?= $movie_id ?>">
                            <input type="hidden" name="showtime_id"  value="<?= $showtime_id ?>">
                            <input type="hidden" name="seats"        value="<?= htmlspecialchars($seats) ?>">
                            <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
                            <button type="submit" class="button confirm-btn-confirm">
                                <span class="material-symbols-outlined">check</span> Confirm Booking
                            </button>
                        </form>
                        <a href="../pages/movie.php?id=<?= $movie_id ?>" class="confirm-form">
                            <button class="button confirm-btn-cancel">
                                <span class="material-symbols-outlined">close</span> Cancel
                            </button>
                        </a>
                    </div>

                </article>
            </div>
        </section>
    </main>
    <?php include("../includes/footer.php"); ?>
</body>
</html>