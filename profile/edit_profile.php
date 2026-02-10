<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id=?";
$user = get_one_row($db_server, $query, [$user_id], "i");

$message = $message_class = "";

if (isset($_POST['edit_profile'])) {
    $name     = trim(mysqli_real_escape_string($db_server, $_POST['name']));
    $email    = trim(mysqli_real_escape_string($db_server, $_POST['email']));
    $mobile   = trim(mysqli_real_escape_string($db_server, $_POST['mobile_no']));

    // Validate mobile format (Nepali: starts with 9, 10 digits)
    if (!preg_match('/^[9][0-9]{9}$/', $mobile)) {
        $message = "Invalid mobile number format.";
        $message_class = "error";
    } else {
        // Check email not taken by another user
        $existing = get_one_row($db_server, "SELECT id FROM users WHERE email=? AND id!=?", [$email, $user_id], "si");
        if ($existing) {
            $message = "Email is already in use by another account.";
            $message_class = "error";
        } else {
            $update = "UPDATE users SET name=?, email=?, mobile_no=? WHERE id=?";
            if (execute_query($db_server, $update, [$name, $email, $mobile, $user_id], "sssi")) {
                // Update session name so navbar refreshes
                $_SESSION['user_name'] = $name;
                $message = "Profile updated successfully!";
                $message_class = "success";
                // Refresh user data
                $user = get_one_row($db_server, $query, [$user_id], "i");
            } else {
                $message = "Error updating profile. Please try again.";
                $message_class = "error";
            }
        }
    }
}
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>


<form method="POST" class="form">
    
    <p class="form-title">Edit Profile</p>

    <label for="name">Full Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="mobile_no">Mobile Number</label>
    <input type="text" name="mobile_no" value="<?= htmlspecialchars($user['mobile_no']) ?>" required>

    <button type="submit" name="edit_profile" class="button add" style="text-align: center; margin-top: 1rem;">
        Save Changes
    </button>

    <?php if ($message): ?>
        <p class="message <?= $message_class ?>" style="margin: 0.5rem 0rem 0;"><?= $message ?></p>
    <?php endif; ?>
</form>

<?php if ($message_class === 'success'): ?>
<script>
    setTimeout(() => {
        window.parent.location.reload();
    }, 1200);
</script>
<?php endif; ?>