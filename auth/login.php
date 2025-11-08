<?php
include("../includes/connection.php");
session_start();
$message = $message_class = "";
if (isset($_POST['submit'])) {
  $email = trim(mysqli_real_escape_string($db_server, $_POST['email']));
  $password = trim(mysqli_real_escape_string($db_server, $_POST['password']));

  $result = mysqli_query($db_server, "SELECT * FROM users WHERE email='$email'");
  $user = mysqli_fetch_assoc($result);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $message = "Login successful!";
    $message_class = "success";
  } else {
    $message = "Invalid email or password!";
    $message_class = "error";
  }
}
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
  <?php include("../includes/header.php");?>
  <main class="login-container">
    <div class="login-box">
      <p class="head">Login</p>
      <p class="message <?php echo $message_class ?>"><?php echo $message; ?></p>
      <form action="login.php" method="post">
        <div class="input-field">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="email" placeholder="Email" required />
        </div>
        <div class="input-field">
          <i class="fa-solid fa-key"></i>
          <input type="password" name="password" placeholder="Password" required />
        </div>

        <input type="submit" name="submit" value="Login" class="button" />
      </form>
      <div class="ask">
        <span>Don't have an account?</span>
        <a href="signup.php"><span class="signup">Sign Up</span></a>
      </div>
    </div>
  </main>

   <?php include("../includes/footer.php");?>
  <?php if ($message_class === "success") { ?>
    <script>
      setTimeout(() => {
        window.location.href = "../index.php";
      }, 1000);
    </script>
  <?php } ?>
</body>

</html>