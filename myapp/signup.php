<?php
// processes signup form submissions
$servername = "localhost";
$username = "root"; // your DB username
$password = "";     // your DB password
$dbname = "myapp";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$email = $_POST['email'];

// Hash password for security
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user, $email, $pass);

if ($stmt->execute()) {
    echo "User created! <a href='login.html'>Login here</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
