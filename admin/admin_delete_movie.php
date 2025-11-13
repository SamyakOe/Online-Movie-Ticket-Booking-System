<?php
session_start();
include("../includes/connection.php");
include("../auth/checkAuth.php");
if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $query = "Delete from movies where movie_id='$delete_id'";
    $res = mysqli_query($db_server, $query);
    if ($res) {
        echo "<script>alert('Movie deleted successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "Error deleting movie: " . mysqli_error($db_server);
    }
} else {
    echo "No movie ID provided.";
}
