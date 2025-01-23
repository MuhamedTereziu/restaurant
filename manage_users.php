<?php
include 'session_check.php';
include 'config.php';

// Handle adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];  // Plain text for now
    $role = $_POST['role'];

    // Check if email already exists
    $checkEmail = "SELECT * FROM Users WHERE Email = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $checkEmail, $params);

    if (sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<p style='color: red;'>Error: Email already exists.</p>";
    } else {
        // Insert new user
        $sql = "INSERT INTO Users (Emri, Mbiemri, Email, Fjalekalimi, Roli) VALUES (?, ?, ?, ?, ?)";
        $params = array($name, $surname, $email, $password, $role);
        if (sqlsrv_query($conn, $sql, $params)) {
            echo "<p style='color: green;'>User added successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . print_r(sqlsrv_errors(), true) . "</p>";
        }
    }
}

// Handle deleting a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    $deleteQuery = "DELETE FROM Users WHERE ID_User = ?";
    $params = array($userId);
    if (sqlsrv_query($conn, $deleteQuery, $params)) {
        echo "<p style='color: green;'>User deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting user.</p>";
    }
}

// Fetch all users
$query = "SELECT * FROM Users";
$result = sqlsrv_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #333;
            color: white;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<div class="form-container">
    <h3>Add New User</h3>
    <form method="POST">
        <input type="text" name="name" placeholder="First Name" required>
        <input type="text" name="surname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="Admin">Admin</option>
            <option value="Klient">Klient</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>
</div>

<h3>All Users</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Surname</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
        <tr>
            <td><?= $row['ID_User'] ?></td>
            <td><?= $row['Emri'] ?></td>
            <td><?= $row['Mbiemri'] ?></td>
            <td><?= $row['Email'] ?></td>
            <td><?= $row['Roli'] ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    <input type="hidden" name="user_id" value="<?= $row['ID_User'] ?>">
                    <button type="submit" name="delete_user" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
<div style="text-align: center; margin-top: 30px;">
    <a href="dashboard.php" style="
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 15px 30px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: 0.3s;
    ">Kthehu ne faqen kryesore</a>
</div>


</body>
</html>
