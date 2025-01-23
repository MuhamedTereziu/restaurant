<?php
include 'config.php';
include 'session_check.php';

// Secure session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure only Admin can access the page
if ($_SESSION['user_role'] !== 'Admin') {
    echo "<p style='color: red; text-align: center;'>Access denied. Only admins can manage orders.</p>";
    exit();
}

// Handle updating order (quantity modification)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $new_quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

    $sql = "UPDATE Porosia SET Sasia = ? WHERE ID_Porosi = ?";
    $params = array($new_quantity, $order_id);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        echo "<p class='message success'>Order updated successfully!</p>";
    } else {
        echo "<p class='message error'>Error updating order.</p>";
    }
}

// Handle deleting an order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    
    $deleteQuery = "DELETE FROM Porosia WHERE ID_Porosi = ?";
    $params = array($order_id);

    if (sqlsrv_query($conn, $deleteQuery, $params)) {
        echo "<p class='message success'>Order deleted successfully!</p>";
    } else {
        echo "<p class='message error'>Error deleting order.</p>";
    }
}

// Fetch all orders
$query = "
    SELECT P.ID_Porosi, U.Emri, U.Mbiemri, M.Emri_Artikullit, P.Sasia
    FROM Porosia P
    INNER JOIN Rezervimi R ON P.ID_Rezervim = R.ID_Rezervim
    INNER JOIN Users U ON R.ID_User = U.ID_User
    INNER JOIN Menyja M ON P.ID_Artikull = M.ID_Artikull
    ORDER BY P.ID_Porosi DESC";

$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die("<p class='message error'>Error fetching orders: " . print_r(sqlsrv_errors(), true) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #121212;
        margin: 0;
        padding: 20px;
        color: #e0e0e0;
    }
    .container {
        max-width: 900px;
        margin: auto;
        background: #1e1e1e;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }
    h2 {
        text-align: center;
        color: #e0e0e0;
        font-size: 28px;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #424242;
        padding: 12px;
        text-align: center;
    }
    th {
        background: #333;
        font-weight: bold;
    }
    tr:nth-child(even) {
        background-color: #1e1e1e;
    }
    tr:hover {
        background-color: #333;
    }
    input[type="number"] {
        width: 60px;
        padding: 8px;
        border: 1px solid #424242;
        border-radius: 5px;
        background: #333;
        color: #e0e0e0;
    }
    button {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-update {
        background: #42A5F5;
    }
    .btn-update:hover {
        background: #2196F3;
    }
    .btn-delete {
        background: #E57373;
    }
    .btn-delete:hover {
        background: #EF5350;
    }
    .message {
        text-align: center;
        padding: 15px;
        border-radius: 5px;
        max-width: 400px;
        margin: 10px auto;
        font-weight: bold;
    }
    .message.success {
        background-color: #42A5F5;
        color: white;
    }
    .message.error {
        background-color: #E57373;
        color: white;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Manage Orders</h2>
    
    <table>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_Porosi']) ?></td>
                <td><?= htmlspecialchars($row['Emri']) . " " . htmlspecialchars($row['Mbiemri']) ?></td>
                <td><?= htmlspecialchars($row['Emri_Artikullit']) ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= $row['ID_Porosi'] ?>">
                        <input type="number" name="quantity" value="<?= $row['Sasia'] ?>" min="1" required>
                        <button type="submit" name="update_order" class="btn btn-update">Update</button>
                    </form>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= $row['ID_Porosi'] ?>">
                        <button type="submit" name="delete_order" class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<div style="text-align: center; margin-top: 30px;">
    <a href="dashboard.php" style="
        display: inline-block;
        background-color: #42A5F5;
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
