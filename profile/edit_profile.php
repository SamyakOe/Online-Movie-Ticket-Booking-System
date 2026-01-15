<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

$user_id=$_SESSION['user_id'];
$query="SELECT * FROM users where id=?";
$user=get_one_row($db_server,$query,[$user_id],"i");
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<form method="POST" enctype="multipart/form-data" class="form">
    <p>Edit Profile</p>
    <input type="text" name="username" value="<?= $user['name'] ?>">
    

    <button type="submit" name="edit_profile" class="button add" style="text-align: center;">Edit Profile</button>
</form>