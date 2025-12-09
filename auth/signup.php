<?php
include("../includes/connection.php");
include("../includes/db_helper.php");

$message = $message_class = "";
if (isset($_POST['signup'])) {
  $email = trim(mysqli_real_escape_string($db_server, $_POST['email']));
  $name = trim(mysqli_real_escape_string($db_server, $_POST['fullname']));
  $mobile_no = trim(mysqli_real_escape_string($db_server, $_POST['mobile_no']));
  $password = trim(mysqli_real_escape_string($db_server, $_POST['password']));
  $confirm_password = trim(mysqli_real_escape_string($db_server, $_POST['confirm_password']));

  $check = get_all_rows($db_server, "SELECT * FROM users WHERE email=?", $email, 's');
  if ($check && count($check) > 0) {
    $message = "Email already exists!";
    $message_class = "error";
  } else {
    if (strlen($password) < 8) {
      $message = "Password must be at least 8 characters long.";
      $message_class = "error";
    } else if (!preg_match('/[0-9]/', $password)) {
      $message = "Password must contain atleast one number";
      $message_class = "error";
    } else if (!preg_match('/[a-z]/', $password)) {
      $message = "Password must contain atleast one lowercase character";
      $message_class = "error";
    } else if (!preg_match('/[A-Z]/', $password)) {
      $message = "Password must contain atleast one uppercase character";
      $message_class = "error";
    } else if (!preg_match('/[!@#$%^&*()_\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
      $message = "Password must contain atleast one special character";
      $message_class = "error";
    } else {
      if ($password !== $confirm_password) {
        $message = "Password and Confirm Password do not match!";
        $message_class = "error";
      } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = "Insert into users (email, name, mobile_no, password) Values (?, ?, ? ?)";
        $params = array($email, $name, $mobile_no, $hashed);
        $types = "ssss";
        $success = execute_query($db_server, $query, $params, $types);

        if ($success) {
          $message = "Signup successful!";
          $message_class = "success";
        }
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
  <?php include("../includes/header.php"); ?>
  <main class="login-container">
    <div class="login-box">
      <p class="head">Sign Up</p>
      <p id="message" class="message <?php echo $message_class ?>"><?php echo $message; ?></p>
      <form action="signup.php" method="post" onsubmit="return validate()">
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
          <input type="number" name="mobile_no" placeholder="Mobile Number" id="mobileNo" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-key"></i>
          <input type="password" name="password" placeholder="Password" id="password" required />
        </div>

        <div class="input-field">
          <i class="fa-solid fa-key"></i>
          <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password" id="confirmPassword" required />
        </div>

        <input type="submit" name="signup" value="Sign Up" class="button" />
      </form>
      <div class="ask">
        <span>Already have an account?</span>
        <a href="login.php"><span class="login">Login</span></a>
      </div>
    </div>
  </main>

  <?php include("../includes/footer.php"); ?>
  <?php if ($message_class === "success") { ?>
    <script>
      setTimeout(() => {
        window.location.href = "login.php";
      }, 3000);
    </script>
  <?php } ?>
</body>
<script>
  function validate() {
    const mobileNo = document.getElementById("mobileNo").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    const mobileNoRegex = /^[9][6-8]\d{8}$/;
    if (!mobileNoRegex.test(mobileNo)) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Invalid Mobile Number Format.";
      return false;
    }

    if (password !== confirmPassword) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password and Confirm Password do not match.";
      return false;
    }
    if (password.length < 8) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password must be at least 8 characters long.";
      return false;
    }
    if (!/[0-9]/.test(password)) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password must contain atleast one number";
      return false;

    }
    if (!/[a-z]/.test(password)) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password must contain atleast one lowercase character";
      return false;
    }
    if (!/[A-Z]/.test(password)) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password must contain atleast one uppercase character";
      return false;
    }
    if (!/[!@#$%^&*()_\-=\[\]{};\':"\\|,.<>\/?]/.test(password)) {
      document.querySelector("#message").classList.add("error");
      document.getElementById('message').innerText = "Password must contain atleast one special character";
      return false;
    }
    return true;
  }
</script>

</html>