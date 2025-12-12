<?php
session_start();

// --- Simulate logged-in user for testing ---
// Replace this with your actual login system
// $_SESSION['user_id'] = 1;
// $_SESSION['is_admin'] = 1; // admin
// $_SESSION['is_admin'] = 0; // regular user

$host = 'localhost';
$db   = 'myapp';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// --- Handle review submission ---
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $review = $_POST['review'];
    $game = $_POST['game'];

    if (!empty($username) && !empty($review) && !empty($game)) {
        $stmt = $pdo->prepare("INSERT INTO reviews (username, game, review) VALUES (?, ?, ?)");
        $stmt->execute([$username, $game, $review]);
        $success = "Review submitted successfully!";
        header("Location: reviews.php"); // refresh to show new review
        exit;
    } else {
        $error = "Please fill in all fields.";
    }
}

// --- Handle deletion (admins only) ---
if (isset($_GET['delete']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    $review_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    $success = "Review deleted successfully!";
    header("Location: reviews.php");
    exit;
}

// --- Fetch all reviews ---
$stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - EDA Games</title>
    <style>
         body {
            background: hsl(0, 0%, 6%);
            color: #58fb00;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .background-color{#0a0101ff;}
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea, select { width: 100%; }
        textarea { height: 100px; }
        .review { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        .delete-link { color: red; text-decoration: none; margin-left: 10px; }
        .review-form { max-width: 600px; margin: 20px auto; }
        .reviews-section { max-width: 800px; margin: 20px auto; }
    </style>
</head>
<body>

<h1 align="center">User Reviews</h1>
<a href="userhomepage.php" align="center">Home</a> 
<a href="logout.php" align="center">Logout</a>


<!-- Review Form -->
<div class="review-form">
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <label for="username">Name:</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label for="game">Select Game:</label><br>
        <select name="game" id="game" required>
            <option value="">--Choose a game--</option>
            <option value="Blackjack">Blackjack</option>
            <option value="connect4">connect4</option>
            <option value="Hangman">Hangman</option>
            <option value="Tic Tac Toe">Tic Tac Toe</option>
            <option value="rock paper scissors">rock paper scissors</option>
            <option value="pacman">pacman</option>
        </select><br><br>

        <label for="review">Review:</label><br>
        <textarea name="review" id="review" required></textarea><br><br>

        <input type="submit" name="submit" value="Submit Review">
    </form>
</div>

<!-- Display Reviews -->
<div class="reviews-section">
    <h2>Recent Reviews</h2>
    <?php
    if ($reviews) {
        foreach ($reviews as $r) {
            echo "<div class='review'>";
            echo "<strong>" . htmlspecialchars($r['username']) . "</strong> reviewed <em>" . htmlspecialchars($r['game']) . "</em> (" . $r['created_at'] . "):<br>";
            echo nl2br(htmlspecialchars($r['review']));
            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                echo "<a class='delete-link' href='?delete=" . $r['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet.</p>";
    }
    ?>
</div>

</body>
</html>
