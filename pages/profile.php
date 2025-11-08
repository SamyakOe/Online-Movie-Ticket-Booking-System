<?php
session_start();
include("../includes/connection.php");
include("../auth/checkAuth.php");

$id = $_SESSION["user_id"];
$result = mysqli_query($db_server, "Select * from users where id='$id'");
$user = mysqli_fetch_assoc($result);

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
    <main class="login-container">
        <div class="login-box">
            <p class="head">My Profile</p>
            <p class="message <?php echo $message_class ?>"><?php echo $message; ?></p>
            <form action="profile.php" method="post">
                <div class="input-field">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="fullname" value="<?= $user["name"] ?>" required />
                </div>

                <div class="input-field">
                    <i class="fa-solid fa-mobile"></i>
                    <input type="number" name="mobile_no" value="<?= $user["mobile_no"] ?>" required />
                </div>

                <input type="submit" name="submit" value="Change" class="button" />
            </form>
        
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>

</html>