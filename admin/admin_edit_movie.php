<?php
session_start();
include("../includes/connection.php");
include("../auth/checkAuth.php");
$movie_id = $_GET['id'];

$result = mysqli_query($db_server, "Select * from movies where movie_id='$movie_id'");
$movie = mysqli_fetch_assoc($result);

$showtimes = mysqli_query($db_server, "
  SELECT showtime_id, show_date, show_time 
  FROM showtime 
  WHERE movie_id='$movie_id' 
  ORDER BY show_date, show_time
");


if (isset($_POST['edit_movie'])) {
    $title = mysqli_real_escape_string($db_server, $_POST['title']);
    $genres = $_POST["genre"];
    $genres_string = implode(", ", $genres);
    $language = mysqli_real_escape_string($db_server, $_POST["language"]);
    $director = mysqli_real_escape_string($db_server, $_POST["director"]);
    $cast = mysqli_real_escape_string($db_server, $_POST["cast"]);
    $duration = mysqli_real_escape_string($db_server, $_POST["duration"]);
    $release_date = mysqli_real_escape_string($db_server, $_POST['release_date']);
    $description = mysqli_real_escape_string($db_server, $_POST['description']);



    if ($poster = $_FILES['poster']['name']) {
        $temp = $_FILES['poster']['tmp_name'];
        move_uploaded_file($temp, "../assets/image/" . $poster);
        mysqli_query($db_server, "UPDATE movies set poster='$poster' where movie_id='$movie_id'");
    }

    $movie_query = "UPDATE movies 
                set title='$title', 
                genre='$genres_string', 
                language='$language', 
                director='$director', 
                cast='$cast', 
                duration='$duration', 
                release_date='$release_date', 
                description='$description'
                where movie_id='$movie_id'";

    if (!empty($_POST['show_date']) && is_array($_POST['show_date'])) {
        foreach ($_POST['show_date'] as $id => $date) {
            $time = $_POST['show_time'][$id];
            $stmt = $db_server->prepare("UPDATE showtime SET show_date=?, show_time=? WHERE showtime_id=?");
            $stmt->bind_param("ssi", $date, $time, $id);
            $stmt->execute();
        }
    }


    if (!empty($_POST['new_show_date'])) {
        foreach ($_POST['new_show_date'] as $i => $date) {
            if (!empty($_POST['new_show_time'][$i])) {
                foreach ($_POST['new_show_time'][$i] as $time) {
                    mysqli_query(
                        $db_server,
                        "INSERT INTO showtime (movie_id, show_date, show_time) 
                     VALUES ('$movie_id', '$date', '$time')"
                    );
                }
            }
        }
    }




    if (mysqli_query($db_server, $movie_query)) {
        echo "<script>alert('Movie edited successfully!');</script>";
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

<form method="POST" enctype="multipart/form-data" class="form">
    <p>Edit Movie</p>
    <label for="title">Title</label>
    <input type="text" name="title" value="<?= $movie["title"] ?>" required>

    <label for="genre[]">Select Genre:</label>
    <div class="genre-selection">
        <?php
        $all_genres = [
            "Comedy",
            "Horror",
            "Adventure",
            "Drama",
            "Action",
            "Romance",
            "Sci-Fi",
            "Thriller",
            "Fantasy",
            "Mystery",
            "Documentary",
            "Animation",
            "Musical",
            "Crime",
            "Historical"
        ];
        $selected_genres = explode(", ", $movie['genre']);
        foreach ($all_genres as $g) {
        ?>
            <div><input type="checkbox" name="genre[]" value="<?= $g; ?>" <?php if (in_array($g, $selected_genres)) echo "checked" ?>> <?= $g; ?></div>
        <?php } ?>

    </div>

    <label for="language">Language</label>
    <input type="text" name="language" value="<?= $movie["language"] ?>" required>

    <label for="director">Director</label>
    <input type="text" name="director" value="<?= $movie["director"] ?>" required>

    <label for="cast">Cast</label>
    <input type="text" name="cast" value="<?= $movie["cast"] ?>" required>

    <label for="duration">Duration (in mins)</label>
    <input type="number" name="duration" value="<?= $movie["duration"] ?>" required>

    <label for="description">Description</label>
    <textarea name="description" required><?= $movie["description"] ?></textarea>

    <label for="release_date">Released Date:</label>
    <input type="date" name="release_date" value="<?= $movie["release_date"] ?>" required>

    <label>
        Showtimes
        <button type="button" class="button add addShowtime" onclick="addShowDate()"><i class="fa-solid fa-plus"></i> Add Date</button>

    </label>
    <div id="showdateContainer">
        <?php while ($row = mysqli_fetch_assoc($showtimes)) { ?>
            <div class="dates">
                <label>Show Date:</label>
                <input type="date" name="show_date[<?= $row['showtime_id']; ?>]" value="<?= $row['show_date']; ?>" required>

                <label>Show Time:</label>
                <input type="time" name="show_time[<?= $row['showtime_id']; ?>]" value="<?= $row['show_time']; ?>" required>
            </div>
        <?php } ?>

    </div>

    <label>Current Poster:</label>
    <img src="../assets/image/<?= $movie["poster"] ?>" alt="<?= $movie["title"] ?>" width="25%">
    <label for="poster">Change Poster:</label>
    <input type="file" name="poster" accept="image/*"><br>

    <button type="submit" name="edit_movie" class="button add" style="text-align: center;">Edit Movie</button>
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
</script>