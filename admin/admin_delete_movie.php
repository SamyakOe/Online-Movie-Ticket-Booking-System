<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $query = "DELETE FROM movies WHERE movie_id=?";
    if (execute_query($db_server, $query, [$delete_id], "i")) {
        echo "<script>alert('Movie deleted successfully!'); window.location.href='admin_movies.php';</script>";
    } else {
        echo "Error deleting movie: " . mysqli_error($db_server);
    }
} else {
    echo "No movie ID provided.";
}