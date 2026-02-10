<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");

$user_id = $_SESSION['user_id'];
$user = get_one_row($db_server, "SELECT * FROM users WHERE id=?", [$user_id], "i");

$message = $message_class = "";

if (isset($_POST['change_password'])) {
    $current_password  = $_POST['current_password'];
    $new_password      = $_POST['new_password'];
    $confirm_password  = $_POST['confirm_password'];

    if (!password_verify($current_password, $user['password'])) {
        $message = "Current password is incorrect.";
        $message_class = "error";
    } elseif (strlen($new_password) < 8) {
        $message = "New password must be at least 8 characters long.";
        $message_class = "error";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $message = "Password must contain at least one number.";
        $message_class = "error";
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $message = "Password must contain at least one lowercase character.";
        $message_class = "error";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $message = "Password must contain at least one uppercase character.";
        $message_class = "error";
    } elseif (!preg_match('/[!@#$%^&*()_\-=\[\]{};\':"\\|,.<>\/?]/', $new_password)) {
        $message = "Password must contain at least one special character.";
        $message_class = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirm password do not match.";
        $message_class = "error";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        if (execute_query($db_server, "UPDATE users SET password=? WHERE id=?", [$hashed, $user_id], "si")) {
            $message = "Password changed successfully!";
            $message_class = "success";
        } else {
            $message = "Error changing password. Please try again.";
            $message_class = "error";
        }
    }
}
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php if ($message): ?>
    <p class="message <?= $message_class ?>" style="margin: 0.5rem 1rem 0;"><?= $message ?></p>
<?php endif; ?>

<form method="POST" class="form" onsubmit="return validatePasswords()">
    <p class="form-title">Change Password</p>

    <label for="current_password">Current Password</label>
    <input type="password" name="current_password" id="current_password" required>

    <label for="new_password">New Password</label>
    <input type="password" name="new_password" id="new_password" required>

    <label for="confirm_password">Confirm New Password</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <div class="note-box" style="margin-top: 0.5rem;">
        <p class="note-text">
            <i class="fa-solid fa-circle-info"></i>
            Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.
        </p>
    </div>

    <button type="submit" name="change_password" class="button add" style="text-align: center; margin-top: 1rem;">
        Change Password
    </button>
</form>

<script>
function validatePasswords() {
    const newPwd = document.getElementById('new_password').value;
    const confirmPwd = document.getElementById('confirm_password').value;

    if (newPwd !== confirmPwd) {
        alert('New password and confirm password do not match.');
        return false;
    }
    if (newPwd.length < 8) {
        alert('Password must be at least 8 characters long.');
        return false;
    }
    if (!/[0-9]/.test(newPwd)) {
        alert('Password must contain at least one number.');
        return false;
    }
    if (!/[a-z]/.test(newPwd)) {
        alert('Password must contain at least one lowercase character.');
        return false;
    }
    if (!/[A-Z]/.test(newPwd)) {
        alert('Password must contain at least one uppercase character.');
        return false;
    }
    if (!/[!@#$%^&*()_\-=\[\]{};':"\\|,.<>/?]/.test(newPwd)) {
        alert('Password must contain at least one special character.');
        return false;
    }
    return true;
}
</script>

<?php if ($message_class === 'success'): ?>
<script>
    setTimeout(() => {
        window.parent.location.reload();
    }, 1500);
</script>
<?php endif; ?>