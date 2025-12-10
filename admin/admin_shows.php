<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<div class="management-container">
    <div class="management-body">

        <div class="management-head">
            <p>Shows</p>

            <div class="button add" onclick="openModel('')"><i class="fa-solid fa-plus"></i>
                <p>Add Show</p>
            </div>

        </div>
        <div class="management-content">
            <table class="admin-content-table">
                <tr>
                    <th>Movie</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th class="action">Actions</th>

                </tr>
                <?php
                $query = "SELECT showtime_id, title, show_date, show_time FROM movies INNER JOIN showtime ON movies.movie_id = showtime.movie_id";
                $shows = get_all_rows($db_server, $query);
                foreach ($shows as $row) {
                ?>
                    <tr>
                        <td><?= $row["title"] ?></td>
                        <td><?= date("M d, Y", strtotime($row["show_date"])) ?></td>
                        <td><?= date("H:i", strtotime($row["show_time"])) ?></td>
                        <td class="action">
                            <i class="fa-solid fa-pen-to-square action-edit" onclick="openModel('admin_edit_movie.php?id=<?= $row["showtime_id"] ?>')"></i>
                            <a href="admin_delete_movie.php?id=<?= $row["showtime_id"] ?>" onclick="return confirm('Are you sure you want to delete this movie?')">
                                <i class="fa-solid fa-trash action-delete"></i>
                            </a>
                        </td>
                    </tr>
                <?php
                }

                ?>
            </table>
        </div>
    </div>
</div>
<div class="model" id="model">
    <div class="model-content">
        <span class="close"><i class="fa-solid fa-xmark" onclick="closeModel()"></i></span>
        <iframe src="" frameborder="0" height="100%" width="100%" id="model-frame"></iframe>
    </div>
</div>

<script src="../assets/js/modelToggle.js"></script>