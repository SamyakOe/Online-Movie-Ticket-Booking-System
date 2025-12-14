<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

if (isset($_POST['add_movie'])) {
    $title = mysqli_real_escape_string($db_server, $_POST['title']);

    $query = "INSERT INTO movies (title, genre, language, director, cast, duration, release_date, description, poster)
              VALUES (?,?,?,?,?,?,?,?,?)";
    $params = array($title, $genres_string, $language, $director, $cast, $duration, $release_date, $description, $poster);
    if (execute_query($db_server, $query, $params, "sssssisss")) {
        echo "<script>alert('Movie added successfully!');</script>";
    } else {
        echo "Error: " . mysqli_error($db_server);
    }
}
?>

<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php
$movies = get_all_rows($db_server, "SELECT movie_id, title FROM movies") ?>

<form method="POST" enctype="multipart/form-data" class="form">
    <p>Add Show</p>
    <label for="title">Title</label>
    <select name="movie" id="movie" onchange="displayShowContainer()">
        <option value="" selected hidden>Choose a Movie</option>
        <?php foreach ($movies as $movie) { ?>
            <option value="<?= $movie['title'] ?>"><?= $movie['title'] ?></option>
        <?php } ?>
    </select>

    <div class="showContainer" id="showContainer">
        <label>
            Showtimes
            <button type="button" class="button add addShowtime" onclick="addShowDate()"><i class="fa-solid fa-plus"></i> Add Date</button>

        </label>
        <div id="showdateContainer">

        </div>

    </div>

    <button type="submit" name="add_show" class="button add" style="text-align: center;">Add Show</button>
</form>
<script>
    let dateIndex = 0;

    function addShowDate() {
        const container = document.getElementById("showdateContainer");
        const div = document.createElement("div");
        div.innerHTML = `
        <div class="dates">
            <label>Show Date:</label>
            <input type="date" name="new_show_date[${dateIndex}]" required>
            <label>
                Show Times:
            </label>
            <div class="showtimeContainer">
                <input type="time" name="new_show_time[${dateIndex}][]" required>
            </div>
        </div>
    `;
        container.appendChild(div);
        dateIndex++;
    }

    function displayShowContainer() {
        const container = document.getElementById("showContainer");
        container.style.display = "flex";
    }
</script>