<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$movie_id = $_GET['id'];

$movie = get_one_row($db_server, "SELECT * FROM movies WHERE movie_id=?", [$movie_id], "i");


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
        execute_query($db_server, "UPDATE movies SET poster='$poster' WHERE movie_id=?", [$movie_id], "i");
    }

    $movie_query = "UPDATE movies 
                SET title= ?, 
                genre=?, 
                language=?, 
                director=?, 
                cast=?, 
                duration=?, 
                release_date=?, 
                description=?
                WHERE movie_id=?";
    $params = array($title, $genres_string, $language, $director, $cast, $duration, $release_date, $description, $movie_id);
    if (execute_query($db_server, $movie_query, $params, "sssssissi")) {
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
    

    <label>Current Poster:</label>
    <img src="../assets/image/<?= $movie["poster"] ?>" alt="<?= $movie["title"] ?>" width="25%">
    <label for="poster">Change Poster:</label>
    <input type="file" name="poster" accept="image/*"><br>

    <button type="submit" name="edit_movie" class="button add" style="text-align: center;">Edit Movie</button>
</form>