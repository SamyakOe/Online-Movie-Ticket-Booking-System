<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");


$movie_id = $_POST['movie_id'];
$showtime_id = $_POST['showtime_id'];
$seats = $_POST['seats'];
$total_amount = $_POST['total_amount'];

// Get movie details
$movie = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$movie_id], "i");

// Get showtime details
$showtime = get_one_row($db_server, "SELECT * FROM showtime WHERE showtime_id=?", [$showtime_id], "i");

// Get user details
$user = get_one_row($db_server, "SELECT * FROM users WHERE id=?", [$_SESSION['user_id']], "i");

// Convert seats string to array for display
$seats_array = explode(",", $seats);
$seat_count = count($seats_array);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Confirm Booking - MovieBook</title>
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
                <!-- Movie Poster -->
                <article class="inner-poster">
                    <img src="../assets/image/<?= $movie["poster"]; ?>" alt="<?= $movie["title"]; ?>">
                </article>

                <!-- Booking Details -->
                <article class="inner-details-body">

                    <!-- Movie Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Movie Details</p>
                        <table>
                            <tr>
                                <th>Movie:</th>
                                <td style=""><?= $movie["title"] ?></td>
                            </tr>
                            <tr>
                                <th>Genre:</th>
                                <td><?= $movie["genre"] ?></td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td><?= $movie["duration"] ?> mins</td>
                            </tr>
                            <tr>
                                <th>Language:</th>
                                <td><?= $movie["language"] ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Show Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Show Details</p>
                        <table>
                            <tr>
                                <th>Date:</th>
                                <td><?= date("l, F d, Y", strtotime($showtime["show_date"])) ?></td>
                            </tr>
                            <tr>
                                <th>Time:</th>
                                <td><?= date("h:i A", strtotime($showtime["show_time"])) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Seat Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Seat Details</p>
                        <table>
                            <tr>
                                <th>Selected Seats:</th>
                                <td style=" var(--color3);"><?= $seats ?></td>
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
                                <th style="padding-top: 1.5rem; font-size: 1.5rem;">Total Amount:</th>
                                <th style="padding-top: 1.5rem;color: var(--color3); font-size: 1.5rem; ">NPR <?= $total_amount ?></th>
                            </tr>
                        </table>
                    </div>

                    <!-- User Information -->
                    <div class="inner-details-block">
                        <p class="inner-details-sub-title">Customer Details</p>
                        <table>
                            <tr>
                                <th>Name:</th>
                                <td><?= $user["name"] ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $user["email"] ?></td>
                            </tr>
                            <tr>
                                <th>Mobile:</th>
                                <td><?= $user["mobile_no"] ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Confirm Buttons -->
                    <div class="inner-details-block confirm-cancel">
                        <form action="booking_process.php" method="POST" style="flex: 1;">
                            <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                            <input type="hidden" name="showtime_id" value="<?= $showtime_id ?>">
                            <input type="hidden" name="seats" value="<?= $seats ?>">
                            <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
                            <button type="submit" class="button" style="width: 100%; background-color: var(--success);">
                                <span class="material-symbols-outlined">
                                    check
                                </span> Confirm Booking
                            </button>
                        </form>
                        <a href="../pages/movie.php?id=<?= $movie_id ?>" style="flex: 1;">
                            <button class="button" style="width: 100%; background-color: var(--error);">
                                <span class="material-symbols-outlined">
                                    close
                                </span> Cancel
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