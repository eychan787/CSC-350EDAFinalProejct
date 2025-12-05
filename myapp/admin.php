<?php
// Admin dashboard to manage users
session_start();

// Only allow admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Access denied.");
}

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "myapp");

// Fetch all users
$result = $conn->query("SELECT id, username, email, is_admin, created_at FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Controls</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #333; padding: 10px; text-align: center; }
        th { background-color: #eee; }
        a.button { padding: 6px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; }
        a.delete { background: #d9534f; }
        a.admin { background: #5cb85c; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Admin Dashboard</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Admin?</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= $row['username']; ?></td>
        <td><?= $row['email']; ?></td>
        <td><?= $row['is_admin'] ? "Yes" : "No"; ?></td>
        <td><?= $row['created_at']; ?></td>
        <td>

            <?php if ($row['id'] != $_SESSION['user_id']): ?> 
                <!-- Delete user -->
                <a class="button delete" href="admin_actions.php?action=delete&id=<?= $row['id']; ?>">Delete</a>

                <!-- Promote / demote -->
                <?php if ($row['is_admin']): ?>
                    <a class="button admin" href="admin_actions.php?action=demote&id=<?= $row['id']; ?>">Demote</a>
                <?php else: ?>
                    <a class="button admin" href="admin_actions.php?action=promote&id=<?= $row['id']; ?>">Promote</a>
                <?php endif; ?>
            <?php endif; ?>

        </td>
    </tr>
    <?php endwhile; ?>

</table>

<br>
<div style="text-align:center;">
    <a href="logout.php">Log Out</a>
    <a href="userhomepage.html">Home</a>
    <a href="reviews.php" style="margin-left:20px;">Reviews</a>
</div>

</body>
</html>
