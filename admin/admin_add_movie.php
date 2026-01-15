<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

if (isset($_POST['add_movie'])) {
    $title = mysqli_real_escape_string($db_server, $_POST['title']);
    $genres = $_POST["genre"];
    $genres_string = implode(", ", $genres);
    $language = mysqli_real_escape_string($db_server, $_POST["language"]);
    $director = mysqli_real_escape_string($db_server, $_POST["director"]);
    $cast = mysqli_real_escape_string($db_server, $_POST["cast"]);
    $duration = mysqli_real_escape_string($db_server, $_POST["duration"]);
    $release_date = mysqli_real_escape_string($db_server, $_POST['release_date']);
    $description = mysqli_real_escape_string($db_server, $_POST['description']);

    $poster = $_FILES['poster']['name'];
    $temp = $_FILES['poster']['tmp_name'];
    move_uploaded_file($temp, "../assets/image/" . $poster);

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

<form method="POST" enctype="multipart/form-data" class="form">
    <p>Add a New Movie</p>
    <label for="title">Title</label>
    <input type="text" name="title" required>

    <label for="genre[]">Select Genre:</label>
    <div class="genre-selection">
        <div><input type="checkbox" name="genre[]" value="Comedy"> Comedy</div>
        <div><input type="checkbox" name="genre[]" value="Horror"> Horror</div>
        <div><input type="checkbox" name="genre[]" value="Adventure"> Adventure</div>
        <div><input type="checkbox" name="genre[]" value="Drama"> Drama</div>
        <div><input type="checkbox" name="genre[]" value="Action"> Action</div>
        <div><input type="checkbox" name="genre[]" value="Romance"> Romance</div>
        <div><input type="checkbox" name="genre[]" value="Sci-Fi"> Sci-Fi</div>
        <div><input type="checkbox" name="genre[]" value="Thriller"> Thriller</div>
        <div><input type="checkbox" name="genre[]" value="Fantasy"> Fantasy</div>
        <div><input type="checkbox" name="genre[]" value="Mystery"> Mystery</div>
        <div><input type="checkbox" name="genre[]" value="Documentary"> Documentary</div>
        <div><input type="checkbox" name="genre[]" value="Animation"> Animation</div>
        <div><input type="checkbox" name="genre[]" value="Musical"> Musical</div>
        <div><input type="checkbox" name="genre[]" value="Crime"> Crime</div>
        <div><input type="checkbox" name="genre[]" value="Historical"> Historical</div>
    </div>

    <label for="language">Language</label>
    <input type="text" name="language" required>

    <label for="director">Director</label>
    <input type="text" name="director" required>

    <label for="cast">Cast</label>
    <input type="text" name="cast" required>

    <label for="duration">Duration (in mins)</label>
    <input type="number" name="duration" required>

    <label for="description">Description</label>
    <textarea name="description" required></textarea>

    <label for="release_date">Released Date:</label>
    <input type="date" name="release_date" required>

    <label for="poster">Poster:</label>
    <input type="file" name="poster" accept="image/*" required><br>

    

    <button type="submit" name="add_movie" class="button add" style="text-align: center;">Add Movie</button>
</form>
