<?php
session_start();
include("../includes/connection.php");
include("../includes/db_helper.php");
include("../auth/checkAuth.php");
include("../auth/checkAdmin.php");

$success = "";
$error   = "";

// ── Handle role toggle ───────────────────────────────────────────────────────
if (isset($_POST['toggle_role'])) {
    $target_id   = (int)$_POST['user_id'];
    $current_role = (int)$_POST['current_role'];

    // Prevent admin from demoting themselves
    if ($target_id === (int)$_SESSION['user_id']) {
        $error = "You cannot change your own role.";
    } else {
        $new_role = $current_role === 1 ? 0 : 1;
        if (execute_query($db_server,
                "UPDATE users SET role = ? WHERE id = ?",
                [$new_role, $target_id], "ii")) {
            $success = "User role updated successfully.";
        } else {
            $error = "Failed to update role.";
        }
    }
}

// ── Handle delete ────────────────────────────────────────────────────────────
if (isset($_POST['delete_user'])) {
    $target_id = (int)$_POST['user_id'];

    // Prevent admin from deleting themselves
    if ($target_id === (int)$_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        // Check if user has confirmed bookings
        $booking_check = get_one_row($db_server,
            "SELECT COUNT(*) AS c FROM bookings WHERE user_id = ? AND status = 'confirmed'",
            [$target_id], "i"
        );
        if ($booking_check['c'] > 0) {
            $error = "Cannot delete user with existing confirmed bookings.";
        } else {
            if (execute_query($db_server,
                    "DELETE FROM users WHERE id = ?",
                    [$target_id], "i")) {
                $success = "User deleted successfully.";
            } else {
                $error = "Failed to delete user.";
            }
        }
    }
}

// ── Fetch all users ──────────────────────────────────────────────────────────
$users = get_all_rows($db_server,
    "SELECT u.id, u.name, u.email, u.mobile_no, u.role,
            COUNT(b.booking_id) AS total_bookings
     FROM users u
     LEFT JOIN bookings b ON u.id = b.user_id
     GROUP BY u.id
     ORDER BY u.role DESC, u.name ASC"
);

$total_users  = count($users);
$total_admins = count(array_filter($users, fn($u) => (int)$u['role'] === 1));
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Summary cards -->
<div class="indicators-menu" style="grid-template-columns: repeat(2,1fr); padding: 2rem 5rem 0;">
    <div class="indicator">
        <div class="indicator-text">
            Total Users
            <p class="number"><?= $total_users ?></p>
        </div>
        <i class="fa-solid fa-users indicator-icon"></i>
    </div>
    <div class="indicator">
        <div class="indicator-text">
            Admins
            <p class="number"><?= $total_admins ?></p>
        </div>
        <i class="fa-solid fa-user-shield indicator-icon"></i>
    </div>
</div>

<div class="management-container">
    <div class="management-body">

        <div class="management-head">
            <p>User Management</p>
        </div>

        <div class="management-content">
            <table class="admin-content-table">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Bookings</th>
                    <th>Role</th>
                    <th class="action">Actions</th>
                </tr>

                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:2rem; color:white;">
                            No users found.
                        </td>
                    </tr>
                <?php else: foreach ($users as $u):
                    $is_self = (int)$u['id'] === (int)$_SESSION['user_id'];
                ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($u['name']) ?>
                            <?php if ($is_self): ?>
                                <span class="self-badge">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['mobile_no']) ?></td>
                        <td style="text-align:center;"><?= $u['total_bookings'] ?></td>
                        <td>
                            <span class="role-badge <?= (int)$u['role'] === 1 ? 'role-admin' : 'role-user' ?>">
                                <?= (int)$u['role'] === 1 ? 'Admin' : 'User' ?>
                            </span>
                        </td>
                        <td class="action">
                            <?php if (!$is_self): ?>
                                <!-- Toggle Role -->
                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Change role for <?= addslashes(htmlspecialchars($u['name'])) ?>?')">
                                    <input type="hidden" name="user_id"      value="<?= $u['id'] ?>">
                                    <input type="hidden" name="current_role" value="<?= $u['role'] ?>">
                                    <button type="submit" name="toggle_role"
                                            class="action-btn"
                                            title="<?= (int)$u['role'] === 1 ? 'Demote to User' : 'Promote to Admin' ?>">
                                        <i class="fa-solid <?= (int)$u['role'] === 1 ? 'fa-user-minus' : 'fa-user-shield' ?> action-edit"></i>
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Delete <?= addslashes(htmlspecialchars($u['name'])) ?>? This cannot be undone.')">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" name="delete_user" class="action-btn" title="Delete user">
                                        <i class="fa-solid fa-trash action-delete"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color:var(--color4); font-size:0.8rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </table>
        </div>

    </div>
</div>

<style>
.alert {
    margin: 1rem 5rem 0;
    padding: 0.8rem 1.2rem;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}
.alert-success { background-color: var(--success); }
.alert-error   { background-color: var(--error);   }

.role-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 2rem;
}
.role-admin { background-color: var(--color3); color: var(--color1); }
.role-user  { background-color: var(--color4); color: white;         }

.self-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.15rem 0.45rem;
    border-radius: 2rem;
    background-color: var(--color1);
    color: var(--color4);
    margin-left: 0.3rem;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.2rem;
}
</style>