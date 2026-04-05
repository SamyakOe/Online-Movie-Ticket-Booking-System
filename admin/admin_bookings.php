<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

// Optional filters from GET
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_movie  = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;

// Build WHERE clause dynamically
$where  = "WHERE 1=1";
$params = [];
$types  = "";

if ($filter_status === 'confirmed' || $filter_status === 'cancelled') {
    $where  .= " AND b.status = ?";
    $params[] = $filter_status;
    $types   .= "s";
}
if ($filter_movie > 0) {
    $where  .= " AND b.movie_id = ?";
    $params[] = $filter_movie;
    $types   .= "i";
}

$query = "SELECT b.booking_id, b.status, b.total_amount, b.booking_date,
                 m.title      AS movie_title,
                 u.name       AS user_name,
                 u.email,
                 s.show_date,
                 s.show_time,
                 COUNT(bs.id) AS seat_count
          FROM bookings b
          INNER JOIN movies   m  ON b.movie_id    = m.movie_id
          INNER JOIN users    u  ON b.user_id     = u.id
          INNER JOIN showtime s  ON b.showtime_id = s.showtime_id
          LEFT  JOIN booking_seats bs ON bs.booking_id = b.booking_id
          $where
          GROUP BY b.booking_id
          ORDER BY b.booking_date DESC";

$bookings = empty($params)
    ? get_all_rows($db_server, $query)
    : get_all_rows($db_server, $query, $params, $types);

// Summary counts
$total_confirmed  = get_one_row($db_server, "SELECT COUNT(*) AS c FROM bookings WHERE status='confirmed'")['c'];
$total_cancelled  = get_one_row($db_server, "SELECT COUNT(*) AS c FROM bookings WHERE status='cancelled'")['c'];
$total_revenue    = get_one_row($db_server, "SELECT COALESCE(SUM(total_amount),0) AS r FROM bookings WHERE status='confirmed'")['r'];

// Movie list for the filter dropdown
$movies = get_all_rows($db_server, "SELECT movie_id, title FROM movies ORDER BY title");
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<!-- Summary cards -->
<div class="indicators-menu" style="grid-template-columns: repeat(3,1fr); padding: 2rem 5rem 0;">
    <div class="indicator">
        <div class="indicator-text">
            Confirmed
            <p class="number"><?= $total_confirmed ?></p>
        </div>
        <i class="fa-solid fa-circle-check indicator-icon" style="color:var(--success);"></i>
    </div>
    <div class="indicator">
        <div class="indicator-text">
            Cancelled
            <p class="number"><?= $total_cancelled ?></p>
        </div>
        <i class="fa-solid fa-circle-xmark indicator-icon" style="color:var(--error);"></i>
    </div>
    <div class="indicator">
        <div class="indicator-text">
            Total Revenue
            <p class="number">NPR <?= number_format($total_revenue, 0) ?></p>
        </div>
        <i class="fa-solid fa-money-bill-wave indicator-icon"></i>
    </div>
</div>

<div class="management-container">
    <div class="management-body">

        <div class="management-head">
            <p>Bookings</p>
        </div>

        <!-- Filters -->
        <form method="GET" class="booking-filters">
            <select name="movie_id" onchange="this.form.submit()">
                <option value="0">All Movies</option>
                <?php foreach ($movies as $m) { ?>
                    <option value="<?= $m['movie_id'] ?>"
                        <?= $filter_movie === $m['movie_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['title']) ?>
                    </option>
                <?php } ?>
            </select>

            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="confirmed" <?= $filter_status === 'confirmed'  ? 'selected' : '' ?>>Confirmed</option>
                <option value="cancelled" <?= $filter_status === 'cancelled'  ? 'selected' : '' ?>>Cancelled</option>
            </select>

            <?php if ($filter_status || $filter_movie) { ?>
                <a href="admin_bookings.php" class="button" style="font-size:0.9rem; padding:0.4rem 1rem;">
                    Clear Filters
                </a>
            <?php } ?>
        </form>

        <!-- Table -->
        <div class="management-content">
            <table class="admin-content-table">
                <tr>
                    <th>#</th>
                    <th>Movie</th>
                    <th>Customer</th>
                    <th>Show Date</th>
                    <th>Show Time</th>
                    <th>Seats</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="action">Actions</th>
                </tr>

                <?php if (empty($bookings)) { ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:2rem; color:white;">
                            No bookings found.
                        </td>
                    </tr>
                    <?php } else {
                    foreach ($bookings as $b) { ?>
                        <tr>
                            <td>#<?= str_pad($b['booking_id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($b['movie_title']) ?></td>
                            <td>
                                <?= htmlspecialchars($b['user_name']) ?><br>
                                <small style="color:var(--color4);"><?= htmlspecialchars($b['email']) ?></small>
                            </td>
                            <td><?= date("M d, Y", strtotime($b['show_date'])) ?></td>
                            <td><?= date("h:i A", strtotime($b['show_time'])) ?></td>
                            <td style="text-align:center;"><?= $b['seat_count'] ?></td>
                            <td>NPR <?= number_format($b['total_amount'], 0) ?></td>
                            <td>
                                <span class="status-badge status-<?= $b['status'] ?>">
                                    <?= ucfirst($b['status']) ?>
                                </span>
                            </td>
                            <td class="action">
                                <i class="fa-solid fa-eye action-edit"
                                    title="View Details"
                                    onclick="openModel('admin_booking_detail.php?id=<?= $b['booking_id'] ?>')"></i>
                            </td>
                        </tr>
                <?php }
                } ?>
            </table>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="model" id="model">
    <div class="model-content">
        <span class="close"><i class="fa-solid fa-xmark" onclick="closeModel()"></i></span>
        <iframe src="" frameborder="0" height="100%" width="100%" id="model-frame"></iframe>
    </div>
</div>

<script src="../assets/js/modelToggle.js"></script>

<style>
    /* Filters row */
    .booking-filters {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }

    .booking-filters select {
        background-color: var(--color4);
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        cursor: pointer;
    }

    /* Make the eye icon blue-ish to distinguish from edit */
    .fa-eye {
        color: var(--color3);
    }
</style>