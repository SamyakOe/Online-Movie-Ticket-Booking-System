<?php
include("../includes/connection.php");
if (isset($_POST['signup'])) {
  $email = trim(mysqli_real_escape_string($db_server, $_POST['email']));
  $name = trim(mysqli_real_escape_string($db_server, $_POST['fullname']));
  $mobile_no = trim(mysqli_real_escape_string($db_server, $_POST['mobile_no']));
  $password = trim(mysqli_real_escape_string($db_server, $_POST['password']));
  $confirm_password = trim(mysqli_real_escape_string($db_server, $_POST['confirm_password']));

  $message = $message_class = "";

  $check = mysqli_query($db_server, "Select * from users where email='$email'");
  if ($check && mysqli_num_rows($check) > 0) {
    $message = "Email already exists!";
    $message_class = "error";
  } else {
    if ($password !== $confirm_password) {
      $message = "Password and Confirm Password do not match!";
      $message_class = "error";
    }else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $query = "Insert into users (email, name, mobile_no, password) Values ('$email', '$name', '$mobile_no', '$hashed')";
      if (mysqli_query($db_server, $query)) {
        $message = "Signup successful!";
        $message_class = "success";
      }
    }
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
      <p class="head">Sign Up</p>
      <p class="message <?php echo $message_class ?>"><?php echo $message; ?></p>
      <form action="signup.php" method="post">
        <div class="input-field">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="email" placeholder="Email" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="fullname" placeholder="Full Name" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-mobile"></i>
          <input type="number" name="mobile_no" placeholder="Mobile Number" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-key"></i>
          <input type="password" name="password" placeholder="Password" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-key"></i>
          <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password" required />
        </div>

        <input type="submit" name="signup" value="Sign Up" class="button" />
      </form>
      <div class="ask">
        <span>Already have an account?</span>
        <a href="login.php"><span class="login">Login</span></a>
      </div>
    </div>
  </main>

   <?php include("../includes/footer.php");?>
  <?php if ($message_class === "success") { ?>
  <script>
    setTimeout(() => {
      window.location.href = "login.php";
    }, 3000);
  </script>
<?php } ?>
</body>

</html>