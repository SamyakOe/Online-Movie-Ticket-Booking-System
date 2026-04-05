<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<?php
// Fetch all movies with their upcoming show count
$movies = get_all_rows($db_server,
    "SELECT m.*,
            COUNT(s.showtime_id)                            AS total_shows,
            SUM(TIMESTAMP(s.show_date, s.show_time) > NOW()) AS upcoming_shows
     FROM movies m
     LEFT JOIN showtime s ON m.movie_id = s.movie_id
     GROUP BY m.movie_id
     ORDER BY m.title ASC"
);
?>

<div class="management-container">
    <div class="management-body">

        <div class="management-head">
            <p>Movies &amp; Shows</p>
            <div class="button add" onclick="openModel('admin_add_movie.php')">
                <i class="fa-solid fa-plus"></i>
                <p>Add Movie</p>
            </div>
        </div>

        <div class="management-content">
            <table class="admin-content-table" id="movies-table">
                <tr>
                    <th style="width:2rem;"></th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Duration</th>
                    <th>Shows</th>
                    <th class="action">Actions</th>
                </tr>

                <?php foreach ($movies as $movie): ?>

                    <!-- ── Movie row ── -->
                    <tr class="movie-row" onclick="toggleShows(<?= $movie['movie_id'] ?>)">
                        <td>
                            <span class="chevron" id="chevron-<?= $movie['movie_id'] ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($movie['title']) ?></td>
                        <td><?= htmlspecialchars($movie['genre']) ?></td>
                        <td><?= $movie['duration'] ?> mins</td>
                        <td>
                            <?php if ($movie['upcoming_shows'] > 0): ?>
                                <span class="show-badge upcoming"><?= $movie['upcoming_shows'] ?> upcoming</span>
                            <?php endif; ?>
                            <?php
                                $past = $movie['total_shows'] - $movie['upcoming_shows'];
                                if ($past > 0):
                            ?>
                                <span class="show-badge past"><?= $past ?> past</span>
                            <?php endif; ?>
                            <?php if ($movie['total_shows'] == 0): ?>
                                <span class="show-badge none">No shows</span>
                            <?php endif; ?>
                        </td>
                        <td class="action" onclick="event.stopPropagation()">
                            <i class="fa-solid fa-pen-to-square action-edit"
                               title="Edit movie"
                               onclick="openModel('admin_edit_movie.php?id=<?= $movie['movie_id'] ?>')"></i>
                            <a href="admin_delete_movie.php?id=<?= $movie['movie_id'] ?>"
                               onclick="return confirm('Delete <?= addslashes(htmlspecialchars($movie['title'])) ?>? This will also remove all its shows.')">
                                <i class="fa-solid fa-trash action-delete" title="Delete movie"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- ── Shows sub-rows (hidden by default) ── -->
                    <tr class="shows-container" id="shows-<?= $movie['movie_id'] ?>">
                        <td colspan="6" style="padding:0;">
                            <div class="shows-inner">

                                <?php
                                $shows = get_all_rows($db_server,
                                    "SELECT * FROM showtime
                                     WHERE movie_id = ?
                                     ORDER BY show_date ASC, show_time ASC",
                                    [$movie['movie_id']], "i"
                                );
                                ?>

                                <!-- Shows sub-header -->
                                <div class="shows-subhead">
                                    <span><i class="fa-solid fa-calendar-days"></i> Showtimes</span>
                                    <div class="button add add-show-btn"
                                         onclick="openModel('admin_add_show.php?movie_id=<?= $movie['movie_id'] ?>')">
                                        <i class="fa-solid fa-plus"></i>
                                        <p>Add Show</p>
                                    </div>
                                </div>

                                <?php if (empty($shows)): ?>
                                    <p class="no-shows-msg">
                                        <i class="fa-solid fa-circle-info"></i>
                                        No shows scheduled yet.
                                    </p>
                                <?php else: ?>
                                    <table class="shows-table">
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th class="action">Actions</th>
                                        </tr>
                                        <?php foreach ($shows as $show):
                                            $isPast = strtotime($show['show_date'] . ' ' . $show['show_time']) < time();
                                        ?>
                                            <tr class="<?= $isPast ? 'show-past' : 'show-upcoming' ?>">
                                                <td><?= date("M d, Y", strtotime($show['show_date'])) ?></td>
                                                <td><?= date("h:i A", strtotime($show['show_time'])) ?></td>
                                                <td>
                                                    <?php if ($isPast): ?>
                                                        <span class="show-badge past">Past</span>
                                                    <?php else: ?>
                                                        <span class="show-badge upcoming">Upcoming</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action">
                                                    <i class="fa-solid fa-pen-to-square action-edit"
                                                       title="Edit show"
                                                       onclick="openModel('admin_edit_show.php?id=<?= $show['showtime_id'] ?>')"></i>
                                                    <a href="admin_delete_show.php?id=<?= $show['showtime_id'] ?>"
                                                       onclick="return confirm('Delete this show?')">
                                                        <i class="fa-solid fa-trash action-delete" title="Delete show"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>
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

<script>
function toggleShows(movieId) {
    const container = document.getElementById('shows-' + movieId);
    const chevron   = document.getElementById('chevron-' + movieId);
    const isOpen    = container.classList.contains('open');

    // Close all others
    document.querySelectorAll('.shows-container.open').forEach(el => {
        el.classList.remove('open');
    });
    document.querySelectorAll('.chevron.rotated').forEach(el => {
        el.classList.remove('rotated');
    });
    document.querySelectorAll('.movie-row.active').forEach(el => {
        el.classList.remove('active');
    });

    // Toggle the clicked one
    if (!isOpen) {
        container.classList.add('open');
        chevron.classList.add('rotated');
        document.querySelector(`[onclick="toggleShows(${movieId})"]`).classList.add('active');
    }
}
</script>

<style>
/* ── Movie row ─────────────────────────────────────────── */
.movie-row {
    cursor: pointer;
    transition: background-color 90ms ease-in-out;
}
.movie-row:hover,
.movie-row.active {
    background-color: var(--color1) !important;
}
.movie-row.active td {
    border-bottom: none;
}

/* ── Chevron ─────────────────────────────────────────────*/
.chevron i {
    color: var(--color4);
    font-size: 0.75rem;
    transition: transform 200ms ease-in-out;
    display: inline-block;
}
.chevron.rotated i {
    transform: rotate(90deg);
}

/* ── Shows sub-section ───────────────────────────────────*/
.shows-container {
    display: none;
}
.shows-container.open {
    display: table-row;
}
.shows-inner {
    background-color: var(--color1);
    padding: 1rem 1.5rem 1.5rem 3rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    border-bottom: 2px solid var(--color2);
}
.shows-subhead {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    font-size: 1rem;
    font-weight: 600;
}
.shows-subhead i {
    color: var(--color3);
    font-size: 0.9rem;
}
.add-show-btn {
    font-size: 0.85rem;
    padding: 0.3rem 0.9rem;
}
.add-show-btn p {
    font-size: 0.85rem;
}
.no-shows-msg {
    color: var(--color4);
    font-size: 0.9rem;
    padding: 0.5rem 0;
}
.no-shows-msg i {
    color: var(--color4);
    font-size: 0.9rem;
    margin-right: 0.3rem;
}

/* ── Shows inner table ───────────────────────────────────*/
.shows-table {
    width: 100%;
    border-collapse: collapse;
    color: white;
}
.shows-table th {
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.4rem 0.6rem;
    text-align: left;
    color: var(--color4);
    border-bottom: 1px solid var(--color2);
}
.shows-table td {
    font-size: 0.9rem;
    padding: 0.5rem 0.6rem;
}
.show-past td {
    opacity: 0.5;
}

/* ── Badges ──────────────────────────────────────────────*/
.show-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 2rem;
    margin-right: 0.25rem;
}
.show-badge.upcoming { background-color: var(--success);  color: white; }
.show-badge.past     { background-color: var(--color4);   color: white; }
.show-badge.none     { background-color: var(--color1);   color: var(--color4); }
</style>