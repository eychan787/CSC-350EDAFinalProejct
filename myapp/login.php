<?php
// processes login form submissions
session_start();

$conn = new mysqli("localhost", "root", "", "myapp");

$login = $_POST['login'];
$pass = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $login, $login);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($pass, $user['password'])) {
        
        // Save user info in session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Redirect based on role
        if ($user['is_admin'] == 1) {
            header("Location: admin.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;

    } else {
        echo "Incorrect password.";
    }
} else {
    echo "User not found.";
}
?>
