<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<nav class="navbar">
  <div class="logo">MovieBook</div>
  <ul>
    <?php if (isset($_SESSION["user_name"])) { ?>
      <li>Hello, <?php echo $_SESSION["user_name"]; ?>!</li>
    <?php } ?>

    <li><a href="/moviebooking/index.php" class="menu-links">Home</a></li>

    <li><a href="/moviebooking/pages/profile.php" class="menu-links">My Profile</a></li>

    <?php if (isset($_SESSION["user_name"]) && $_SESSION["user_role"] ===1) { ?>
      <li><a href="/moviebooking/admin/admin.php" class="menu-links">Admin Panel</a></li>
    <?php }  ?>

    <?php if (isset($_SESSION["user_name"])) { ?>
      <li>
        <a href="/moviebooking/auth/logout.php">
          <div class="button">Logout</div>
        </a>
      </li>
    <?php } else { ?>
      <li>
        <a href="/moviebooking/auth/login.php">
          <div class="button">Login</div>
        </a>
      </li>
    <?php } ?>
  </ul>
</nav>