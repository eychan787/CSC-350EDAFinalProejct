
<?php
// allows admin to delete users or change their admin status
session_start();

// Restrict to admins only
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized.");
}

$conn = new mysqli("localhost", "root", "", "myapp");

$action = $_GET['action'];
$id = intval($_GET['id']);

// Prevent admin from deleting or modifying themselves
if ($id == $_SESSION['user_id']) {
    die("You cannot modify your own admin status or delete your own account!");
}

switch ($action) {

    case 'delete':
        $conn->query("DELETE FROM users WHERE id = $id");
        break;

    case 'promote':
        $conn->query("UPDATE users SET is_admin = 1 WHERE id = $id");
        break;

    case 'demote':
        $conn->query("UPDATE users SET is_admin = 0 WHERE id = $id");
        break;
}

header("Location: admin.php");
exit;
?>