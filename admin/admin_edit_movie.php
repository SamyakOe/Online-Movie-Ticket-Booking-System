<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$movie_id = (int)$_GET['id'];
$movie    = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$movie_id], "i");

if (!$movie) {
    echo "<script>alert('Movie not found.'); window.location.href='admin_movies.php';</script>";
    exit;
}

$errors  = [];
$success = false;

if (isset($_POST['edit_movie'])) {
    $title        = trim($_POST['title']);
    $genres       = isset($_POST['genre']) ? $_POST['genre'] : [];
    $language     = trim($_POST['language']);
    $director     = trim($_POST['director']);
    $cast         = trim($_POST['cast']);
    $duration     = trim($_POST['duration']);
    $release_date = trim($_POST['release_date']);
    $description  = trim($_POST['description']);

    // Validation
    if (empty($title))    $errors[] = "Title is required.";
    if (empty($genres))   $errors[] = "Please select at least one genre.";
    if (empty($language)) $errors[] = "Language is required.";
    if (empty($director)) $errors[] = "Director is required.";
    if (empty($cast))     $errors[] = "Cast is required.";
    if (empty($duration) || !is_numeric($duration) || $duration <= 0)
                          $errors[] = "Duration must be a positive number.";
    if (empty($release_date)) $errors[] = "Release date is required.";
    if (empty($description))  $errors[] = "Description is required.";

    // Poster: optional on edit, validate only if a new file is provided
    $new_poster = null;
    if (!empty($_FILES['poster']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $max_size      = 5 * 1024 * 1024;
        if (!in_array($_FILES['poster']['type'], $allowed_types)) {
            $errors[] = "Poster must be a JPG, PNG, WEBP, or GIF image.";
        } elseif ($_FILES['poster']['size'] > $max_size) {
            $errors[] = "Poster must be smaller than 5 MB.";
        } else {
            $ext        = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $new_poster = uniqid('movie_') . '.' . $ext;
        }
    }

    if (empty($errors)) {
        $genres_string = implode(", ", $genres);
        $poster_value  = $new_poster ?? $movie['poster'];

        // Move new file only after validation passes
        if ($new_poster) {
            move_uploaded_file($_FILES['poster']['tmp_name'], "../assets/image/" . $new_poster);
        }

        // Single atomic UPDATE covering all fields including poster
        $query = "UPDATE movies
                  SET title=?, genre=?, language=?, director=?, cast=?,
                      duration=?, release_date=?, description=?, poster=?
                  WHERE movie_id=?";
        $params = [$title, $genres_string, $language, $director, $cast,
                   $duration, $release_date, $description, $poster_value, $movie_id];

        if (execute_query($db_server, $query, $params, "sssssisssi")) {
            $success = true;
            // Refresh local $movie so the form shows updated values
            $movie = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$movie_id], "i");
        } else {
            $errors[] = "Database error: " . mysqli_error($db_server);
        }
    }
}

$selected_genres = explode(", ", $movie['genre']);
$all_genres = ["Comedy","Horror","Adventure","Drama","Action","Romance",
               "Sci-Fi","Thriller","Fantasy","Mystery","Documentary",
               "Animation","Musical","Crime","Historical"];
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php if (!empty($errors)): ?>
    <div class="form-errors">
        <?php foreach ($errors as $e): ?>
            <p class="form-error-item"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form">
    <p class="form-title">Edit Movie</p>

    <label for="title">Title</label>
    <input type="text" name="title" id="title"
           value="<?= htmlspecialchars($movie['title']) ?>" required>

    <label>Select Genre <span class="form-required">*</span></label>
    <div class="genre-selection">
        <?php foreach ($all_genres as $g):
            $checked = in_array($g, $selected_genres);
        ?>
            <div class="genre-item <?= $checked ? 'genre-item-checked' : '' ?>"
                 onclick="toggleGenre(this)">
                <input type="checkbox" name="genre[]" value="<?= $g ?>"
                       <?= $checked ? 'checked' : '' ?> style="display:none">
                <?= $g ?>
            </div>
        <?php endforeach; ?>
    </div>

    <label for="language">Language</label>
    <input type="text" name="language" id="language"
           value="<?= htmlspecialchars($movie['language']) ?>" required>

    <label for="director">Director</label>
    <input type="text" name="director" id="director"
           value="<?= htmlspecialchars($movie['director']) ?>" required>

    <label for="cast">Cast</label>
    <input type="text" name="cast" id="cast"
           value="<?= htmlspecialchars($movie['cast']) ?>" required>

    <label for="duration">Duration (mins)</label>
    <input type="number" name="duration" id="duration" min="1"
           value="<?= htmlspecialchars($movie['duration']) ?>" required>

    <label for="description">Description</label>
    <textarea name="description" id="description" required><?= htmlspecialchars($movie['description']) ?></textarea>

    <label for="release_date">Release Date</label>
    <input type="date" name="release_date" id="release_date"
           value="<?= htmlspecialchars($movie['release_date']) ?>" required>

    <label>Current Poster</label>
    <img src="../assets/image/<?= htmlspecialchars($movie['poster']) ?>"
         alt="<?= htmlspecialchars($movie['title']) ?>"
         class="current-poster-img">

    <label for="poster">
        Change Poster
        <span class="form-hint">Optional · JPG / PNG / WEBP · max 5 MB</span>
    </label>
    <input type="file" name="poster" id="poster" accept="image/*">
    <div class="poster-preview-wrap" id="posterPreview"></div>

    <button type="submit" name="edit_movie" class="button add form-submit-btn">
        <i class="fa-solid fa-floppy-disk"></i> Save Changes
    </button>
</form>

<?php if ($success): ?>
<script>
    alert('Movie updated successfully!');
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
.form-required { color: var(--color3); }
.form-hint {
    font-size: 0.75rem;
    font-weight: 400;
    color: var(--color4);
}
.genre-item {
    display: flex;
    font-size: 1rem;
    width: 100%;
    align-items: center;
    border-radius: 2rem;
    padding: 0.5rem 1rem;
    background-color: var(--color4);
    box-shadow: inset 1px 1px 3px 0px #000;
    gap: 0.5rem;
    cursor: pointer;
    transition: background-color 90ms ease-in-out;
    color: white;
    user-select: none;
}
.genre-item-checked {
    background-color: var(--color3) !important;
    color: white;
}
.current-poster-img {
    width: 35%;
    border-radius: 0.5rem;
    margin: 0.25rem 0;
}
.poster-preview-wrap img {
    margin-top: 0.5rem;
    width: 40%;
    border-radius: 0.5rem;
}
.form-submit-btn {
    text-align: center;
    margin-top: 0.5rem;
}
</style>

<script>
function toggleGenre(div) {
    const cb = div.querySelector('input[type="checkbox"]');
    cb.checked = !cb.checked;
    div.classList.toggle('genre-item-checked', cb.checked);
}

document.getElementById('poster').addEventListener('change', function () {
    const preview = document.getElementById('posterPreview');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(this.files[0]);
        preview.appendChild(img);
    }
});
</script>