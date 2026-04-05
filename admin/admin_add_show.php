<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$errors  = [];
$success = false;

if (isset($_POST['add_show'])) {
    $movie_id   = (int)$_POST['movie'];
    $show_dates = isset($_POST['new_show_date']) ? $_POST['new_show_date'] : [];
    $show_times = isset($_POST['new_show_time']) ? $_POST['new_show_time'] : [];

    if (!$movie_id) {
        $errors[] = "Please select a movie.";
    }
    if (empty($show_dates)) {
        $errors[] = "Please add at least one show date.";
    }

    foreach ($show_dates as $index => $date) {
        if (empty($date)) {
            $errors[] = "Show date #" . ($index + 1) . " is empty.";
            continue;
        }
        if (empty($show_times[$index])) {
            $errors[] = "Please add at least one time for: $date.";
        }
    }

    if (empty($errors)) {
        foreach ($show_dates as $index => $date) {
            if (empty($show_times[$index])) continue;
            foreach ($show_times[$index] as $time) {
                if (empty($time)) continue;
                execute_query(
                    $db_server,
                    "INSERT INTO showtime (movie_id, show_date, show_time) VALUES (?, ?, ?)",
                    [$movie_id, $date, $time],
                    "iss"
                );
            }
        }
        $success = true;
    }
}

$preselect_movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
$movies = get_all_rows($db_server, "SELECT movie_id, title FROM movies ORDER BY title");
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
    <p class="form-title">Add Show</p>

    <label for="movie">Movie</label>
    <select name="movie" id="movie" onchange="displayShowContainer()">
        <option value="" hidden <?= !$preselect_movie_id ? 'selected' : '' ?>>Choose a Movie</option>
        <?php foreach ($movies as $m): ?>
            <option value="<?= $m['movie_id'] ?>"
                <?= $preselect_movie_id === $m['movie_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="showContainer" id="showContainer"
        <?= $preselect_movie_id ? 'style="display:flex;flex-direction:column;"' : '' ?>>
        <label>
            Showtimes
            <button type="button" class="button add addShowtime" onclick="addShowDate()">
                <i class="fa-solid fa-plus"></i> Add Date
            </button>
        </label>
        <div id="showdateContainer"></div>
    </div>

    <button type="submit" name="add_show" class="button add show-submit-btn">Add Show</button>
</form>

<?php if ($success): ?>
    <script>
        alert('Show added successfully!');
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

    .form-error-item i {
        color: white;
        font-size: 0.85rem;
    }

    .show-submit-btn {
        text-align: center;
    }

    .dates-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .show-times-list {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        margin-top: 0.4rem;
    }

    .show-time-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .show-time-row input[type="time"] {
        flex: 1;
        margin: 0;
    }

    .remove-time-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.3rem;
    }

    .remove-time-btn i {
        color: var(--error);
        font-size: 0.9rem;
    }

    .remove-date-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.3rem;
    }

    .remove-date-btn i {
        color: var(--error);
        font-size: 0.9rem;
    }

    .add-time-btn {
        font-size: 0.8rem !important;
        padding: 0.25rem 0.75rem !important;
        align-self: flex-start;
        margin-top: 0.25rem;
    }

    .add-time-btn p {
        font-size: 0.8rem !important;
    }
</style>

<script>
    let dateIndex = 0;

    function addShowDate() {
        const container = document.getElementById('showdateContainer');
        const idx = dateIndex++;
        const div = document.createElement('div');
        div.className = 'dates';
        div.id = 'date-block-' + idx;
        div.innerHTML = `
        <div class="dates-header">
            <label>Show Date</label>
            <button type="button" class="remove-date-btn" onclick="document.getElementById('date-block-${idx}').remove()" title="Remove date">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
        <input type="date" name="new_show_date[${idx}]" required>
        <label style="margin-top:0.5rem;">
            Show Times
            <button type="button" class="button add add-time-btn" onclick="addTimeRow(${idx})">
                <i class="fa-solid fa-plus"></i><p>Add Time</p>
            </button>
        </label>
        <div class="show-times-list" id="times-${idx}">
            <div class="show-time-row">
                <input type="time" name="new_show_time[${idx}][]" required>
            </div>
        </div>
    `;
        container.appendChild(div);
    }

    function addTimeRow(idx) {
        const list = document.getElementById('times-' + idx);
        const row = document.createElement('div');
        row.className = 'show-time-row';
        row.innerHTML = `
        <input type="time" name="new_show_time[${idx}][]" required>
        <button type="button" class="remove-time-btn" onclick="this.parentElement.remove()" title="Remove time">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;
        list.appendChild(row);
    }

    function displayShowContainer() {
        const c = document.getElementById('showContainer');
        c.style.display = 'flex';
        c.style.flexDirection = 'column';
    }

    <?php if ($preselect_movie_id): ?>
        addShowDate();
    <?php endif; ?>
</script>