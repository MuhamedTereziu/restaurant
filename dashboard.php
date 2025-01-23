<?php
// Secure session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}

// Get user details from session
$user_name = htmlspecialchars($_SESSION['user_email']);
$user_role = htmlspecialchars($_SESSION['user_role']);  // 'Admin' or 'Klient'
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 900px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    header {
        background: #2E7D32; 
        color: white;
        padding: 40px 0;
        border-bottom: 5px solid #1B5E20; 
        text-align: center;
    }
    nav {
        display: flex;
        justify-content: center;
        background: #81C784;
        padding: 15px;
        border-radius: 8px;
    }
    nav a {
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        font-size: 18px;
        margin: 0 10px;
        font-weight: bold;
        background: #4CAF50;
        border-radius: 5px;
        transition: background 0.3s ease;
    }
    nav a:hover {
        background: #388E3C;
    }
    footer {
        text-align: center;
        padding: 20px;
        background: #2E7D32;
        color: white;
        margin-top: 20px;
        border-top: 3px solid #1B5E20;
    }
</style>
</head>
<body>

<header>
    <h1>Mirësevini, <?= $user_name ?>!</h1>
    <p>Roli: <?= $user_role ?></p>
</header>

<nav>
    <a href="reserve.php">Rezervo Tavolinë</a>
    <a href="order.php">Porosit Ushqim</a>
    <a href="fatura.php">Fatura</a>
    <?php if ($user_role == 'Admin'): ?>
        <a href="manage_tables.php">Menaxho Tavolinat</a>
        <a href="manage_orders.php">Menaxho Porositë</a>
        <a href="manage_users.php">Menaxho Përdoruesit</a>
    <?php endif; ?>
    <a href="logout.php">Dil</a>
</nav>

<div class="container">
    <h2>Rreth Nesh</h2>
    <p>
        Mirë se vini në <span style="color: #2E7D32;">Restorantin Tradicional Shqiptar</span>, ku tradita dhe shija bashkohen për të krijuar përvoja të paharrueshme.
    </p>
</div>

<footer>
    <p>Restoranti Tradicional Shqiptar &copy; <?= date('Y'); ?> - Të gjitha të drejtat të rezervuara.</p>
</footer>

</body>
</html>
