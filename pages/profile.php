<?php
session_start();
include("../includes/connection.php");
require("../auth/checkAuth.php");
include("../includes/db_helper.php");

$id = $_SESSION["user_id"];
$user = get_one_row($db_server, "SELECT * FROM users WHERE id=?", [$id], 'i');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <title>Online Movie Ticketing System</title>
</head>

<body>

    <?php include("../includes/header.php"); ?>
    <main class="profile-container">
        <div class="profile-body">
            <div class="profile-info">
                <div class="profile"><i class="fa-solid fa-user"></i></div>
                <div class="profile-text">
                    <p class="profile-username"><?= $user["name"] ?></p>
                    <p class="profile-email"><?= $user["email"] ?></p>
                    <p class="profile-role"><?= $user["role"] ? "Admin" : "User" ?></p>
                </div>
            </div>
        </div>
        <div class="profile-body">
            <div class="profile-body-title">Profile Information</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?= $user["name"] ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= $user["email"] ?></td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td><?= $user["mobile_no"] ?></td>

                </tr>
            </table>
            <hr>
            <div class="profile-actions">
                <button class="profile-button" onclick="openModel('../profile/edit_profile.php')"><i class="fa-solid fa-pen-to-square"></i>Edit Profile</button>
                <button class="profile-button" onclick="openModel('../profile/change_password.php')"><i class="fa-solid fa-key"></i>Change Password</button>
            </div>
        </div>

    </main>
    <?php include("../includes/footer.php"); ?>



</body>
<div class="model" id="model">
    <div class="model-content">
        <span class="close"><i class="fa-solid fa-xmark" onclick="closeModel()"></i></span>
        <iframe src="" frameborder="0" height="100%" width="100%" id="model-frame"></iframe>
    </div>
</div>
<script src="../assets/js/modelToggle.js"></script>

</html>