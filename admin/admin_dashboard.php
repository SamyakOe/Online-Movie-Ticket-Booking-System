<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

// ── Summary cards ────────────────────────────────────────────────────────────
$total_revenue = get_one_row($db_server,
    "SELECT COALESCE(SUM(total_amount), 0) AS r FROM bookings WHERE status = 'confirmed'"
)['r'];

$bookings_today = get_one_row($db_server,
    "SELECT COUNT(*) AS c FROM bookings WHERE DATE(booking_date) = CURDATE()"
)['c'];

$bookings_yesterday = get_one_row($db_server,
    "SELECT COUNT(*) AS c FROM bookings WHERE DATE(booking_date) = CURDATE() - INTERVAL 1 DAY"
)['c'];

$total_bookings = get_one_row($db_server,
    "SELECT COUNT(*) AS c FROM bookings"
)['c'];

$cancelled_bookings = get_one_row($db_server,
    "SELECT COUNT(*) AS c FROM bookings WHERE status = 'cancelled'"
)['c'];

$cancellation_rate = $total_bookings > 0
    ? round(($cancelled_bookings / $total_bookings) * 100)
    : 0;

$seats_today = get_one_row($db_server,
    "SELECT COUNT(bs.id) AS c
     FROM booking_seats bs
     INNER JOIN bookings b ON bs.booking_id = b.booking_id
     WHERE DATE(b.booking_date) = CURDATE() AND b.status = 'confirmed'"
)['c'];

$today_diff = $bookings_today - $bookings_yesterday;

// ── Most booked movies ───────────────────────────────────────────────────────
$top_movies = get_all_rows($db_server,
    "SELECT m.title, COUNT(b.booking_id) AS total
     FROM bookings b
     INNER JOIN movies m ON b.movie_id = m.movie_id
     WHERE b.status = 'confirmed'
     GROUP BY b.movie_id
     ORDER BY total DESC
     LIMIT 5"
);
$max_bookings = !empty($top_movies) ? $top_movies[0]['total'] : 1;

// ── Upcoming shows today ─────────────────────────────────────────────────────
$shows_today = get_all_rows($db_server,
    "SELECT m.title, s.show_time, s.showtime_id,
            COUNT(DISTINCT CASE WHEN b.status='confirmed' THEN bs.id END) AS seats_booked
     FROM showtime s
     INNER JOIN movies m ON s.movie_id = m.movie_id
     LEFT  JOIN bookings b  ON b.showtime_id = s.showtime_id
     LEFT  JOIN booking_seats bs ON bs.booking_id = b.booking_id
     WHERE s.show_date = CURDATE()
       AND s.show_time > CURTIME()
     GROUP BY s.showtime_id
     ORDER BY s.show_time ASC
     LIMIT 6"
);
$total_seats = 80; // your theatre capacity

// ── Recent bookings ──────────────────────────────────────────────────────────
$recent_bookings = get_all_rows($db_server,
    "SELECT b.booking_id, b.total_amount, b.status, b.booking_date,
            u.name AS user_name,
            m.title AS movie_title,
            GROUP_CONCAT(bs.seat_number ORDER BY bs.seat_number SEPARATOR ', ') AS seats
     FROM bookings b
     INNER JOIN users u  ON b.user_id  = u.id
     INNER JOIN movies m ON b.movie_id = m.movie_id
     LEFT  JOIN booking_seats bs ON bs.booking_id = b.booking_id
     GROUP BY b.booking_id
     ORDER BY b.booking_date DESC
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Dashboard</title>
</head>
<body>
<div class="dash-wrap">

    <!-- ── Row 1: summary cards ───────────────────────────────────────────── -->
    <div class="dash-section-label">Overview</div>
    <div class="dash-cards">

        <div class="dash-card">
            <div class="dash-card-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="dash-card-body">
                <p class="dash-card-label">Total revenue</p>
                <p class="dash-card-value">NPR <?= number_format($total_revenue, 0) ?></p>
                <p class="dash-card-sub">confirmed bookings only</p>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-icon"><i class="fa-solid fa-ticket"></i></div>
            <div class="dash-card-body">
                <p class="dash-card-label">Bookings today</p>
                <p class="dash-card-value"><?= $bookings_today ?></p>
                <p class="dash-card-sub <?= $today_diff >= 0 ? 'positive' : 'negative' ?>">
                    <?php if ($today_diff > 0): ?>
                        <i class="fa-solid fa-arrow-up"></i> <?= $today_diff ?> vs yesterday
                    <?php elseif ($today_diff < 0): ?>
                        <i class="fa-solid fa-arrow-down"></i> <?= abs($today_diff) ?> vs yesterday
                    <?php else: ?>
                        Same as yesterday
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-icon"><i class="fa-solid fa-ban"></i></div>
            <div class="dash-card-body">
                <p class="dash-card-label">Cancellation rate</p>
                <p class="dash-card-value"><?= $cancellation_rate ?>%</p>
                <p class="dash-card-sub"><?= $cancelled_bookings ?> of <?= $total_bookings ?> bookings</p>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-icon"><i class="fa-solid fa-couch"></i></div>
            <div class="dash-card-body">
                <p class="dash-card-label">Seats sold today</p>
                <p class="dash-card-value"><?= $seats_today ?></p>
                <p class="dash-card-sub">confirmed bookings</p>
            </div>
        </div>

    </div>

    <!-- ── Row 2: bar chart + today's shows ──────────────────────────────── -->
    <div class="dash-two-col">

        <!-- Most booked movies -->
        <div>
            <div class="dash-section-label">Most booked movies</div>
            <div class="dash-panel">
                <?php if (empty($top_movies)): ?>
                    <p class="dash-empty">No confirmed bookings yet.</p>
                <?php else: foreach ($top_movies as $row):
                    $pct = $max_bookings > 0 ? round(($row['total'] / $max_bookings) * 100) : 0;
                ?>
                    <div class="bar-row">
                        <span class="bar-label" title="<?= htmlspecialchars($row['title']) ?>">
                            <?= htmlspecialchars($row['title']) ?>
                        </span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:<?= $pct ?>%"></div>
                        </div>
                        <span class="bar-count"><?= $row['total'] ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Upcoming shows today -->
        <div>
            <div class="dash-section-label">Upcoming shows today</div>
            <div class="dash-panel">
                <?php if (empty($shows_today)): ?>
                    <p class="dash-empty">No more shows today.</p>
                <?php else: foreach ($shows_today as $show):
                    $pct_full  = round(($show['seats_booked'] / $total_seats) * 100);
                    if ($pct_full >= 80)      { $badge = 'full';    $label = 'filling fast'; }
                    elseif ($pct_full >= 40)  { $badge = 'filling'; $label = 'filling';      }
                    else                      { $badge = 'open';    $label = 'open';         }
                ?>
                    <div class="show-row">
                        <span class="show-title" title="<?= htmlspecialchars($show['title']) ?>">
                            <?= htmlspecialchars($show['title']) ?>
                        </span>
                        <span class="show-time"><?= date("h:i A", strtotime($show['show_time'])) ?></span>
                        <span class="show-badge badge-<?= $badge ?>"><?= $label ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

    </div>

    <!-- ── Row 3: recent bookings ────────────────────────────────────────── -->
    <div>
        <div class="dash-section-label">Recent bookings</div>
        <div class="dash-panel">
            <?php if (empty($recent_bookings)): ?>
                <p class="dash-empty">No bookings yet.</p>
            <?php else: foreach ($recent_bookings as $b):
                // Build initials from name
                $words    = explode(' ', trim($b['user_name']));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            ?>
                <div class="booking-row">
                    <div class="avatar"><?= $initials ?></div>
                    <div class="booking-info">
                        <p class="booking-name"><?= htmlspecialchars($b['user_name']) ?></p>
                        <p class="booking-meta">
                            <?= htmlspecialchars($b['movie_title']) ?>
                            <?php if ($b['seats']): ?> &middot; <?= htmlspecialchars($b['seats']) ?><?php endif; ?>
                        </p>
                    </div>
                    <div class="booking-right">
                        <p class="booking-amount">NPR <?= number_format($b['total_amount'], 0) ?></p>
                        <span class="status-badge status-<?= $b['status'] ?>">
                            <?= ucfirst($b['status']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

</div>

<style>
/* ── Wrapper ─────────────────────────────────────────────────────────────── */
.dash-wrap {
    padding: 2rem 3rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.dash-section-label {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--color4);
    margin-bottom: 0.6rem;
}

/* ── Summary cards ───────────────────────────────────────────────────────── */
.dash-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.dash-card {
    background-color: var(--color2);
    border-radius: 1rem;
    padding: 1.2rem 1.4rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    box-shadow: 1px 1px 19px -10px #000;
}
.dash-card-icon {
    background-color: var(--color1);
    border-radius: 0.6rem;
    width: 2.4rem;
    height: 2.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.dash-card-icon i {
    color: var(--color3);
    font-size: 1rem;
}
.dash-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}
.dash-card-label {
    font-size: 0.78rem;
    color: var(--color4);
    font-weight: 400;
}
.dash-card-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: white;
    line-height: 1.2;
}
.dash-card-sub {
    font-size: 0.72rem;
    color: var(--color4);
}
.dash-card-sub.positive { color: var(--success); }
.dash-card-sub.negative { color: var(--error); }
.dash-card-sub i { font-size: 0.65rem; }

/* ── Two-column row ──────────────────────────────────────────────────────── */
.dash-two-col {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 1.2rem;
    align-items: start;
}

/* ── Shared panel ────────────────────────────────────────────────────────── */
.dash-panel {
    background-color: var(--color2);
    border-radius: 1rem;
    padding: 1.2rem 1.4rem;
    box-shadow: 1px 1px 19px -10px #000;
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}
.dash-empty {
    color: var(--color4);
    font-size: 0.9rem;
    padding: 0.5rem 0;
}

/* ── Bar chart ───────────────────────────────────────────────────────────── */
.bar-row {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.35rem 0;
}
.bar-label {
    font-size: 0.82rem;
    color: white;
    width: 130px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 0;
}
.bar-track {
    flex: 1;
    height: 7px;
    background-color: var(--color1);
    border-radius: 4px;
    overflow: hidden;
}
.bar-fill {
    height: 100%;
    background-color: var(--color3);
    border-radius: 4px;
    transition: width 0.4s ease;
}
.bar-count {
    font-size: 0.82rem;
    color: var(--color4);
    width: 1.5rem;
    text-align: right;
    flex-shrink: 0;
}

/* ── Today's shows ───────────────────────────────────────────────────────── */
.show-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.55rem 0;
    border-bottom: 1px solid var(--color1);
}
.show-row:last-child { border-bottom: none; }
.show-title {
    font-size: 0.85rem;
    color: white;
    font-weight: 600;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.show-time {
    font-size: 0.8rem;
    color: var(--color4);
    flex-shrink: 0;
}
.show-badge {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 2rem;
    flex-shrink: 0;
}
.badge-open    { background-color: var(--success); color: white; }
.badge-filling { background-color: #e6a817;        color: white; }
.badge-full    { background-color: var(--error);   color: white; }

/* ── Recent bookings ─────────────────────────────────────────────────────── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--color1);
}
.booking-row:last-child { border-bottom: none; }
.avatar {
    width: 2.2rem;
    height: 2.2rem;
    border-radius: 50%;
    background-color: var(--color3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}
.booking-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.booking-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.booking-meta {
    font-size: 0.75rem;
    color: var(--color4);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.booking-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    flex-shrink: 0;
}
.booking-amount {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--color3);
}

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 1024px) {
    .dash-wrap { padding: 1.5rem 1.5rem; }
    .dash-cards { grid-template-columns: repeat(2, 1fr); }
    .dash-two-col { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .dash-cards { grid-template-columns: 1fr; }
    .dash-wrap  { padding: 1rem; }
}
</style>
</body>
</html>