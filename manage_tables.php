<?php 
include 'config.php';
include 'session_check.php';

// Secure session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure only Admin can access the page
if ($_SESSION['user_role'] !== 'Admin') {
    echo "<p style='color: red;'>Access denied. Only admins can manage tables.</p>";
    exit();
}

// Handle deleting a reservation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_reservation'])) {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);

    $deleteQuery = "DELETE FROM Rezervimi WHERE ID_Rezervim = ?";
    $params = array($reservation_id);

    if (sqlsrv_query($conn, $deleteQuery, $params)) {
        echo "<p style='color: green;'>Reservation deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting reservation.</p>";
    }
}

// Handle deleting a table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_table'])) {
    $table_id = filter_input(INPUT_POST, 'table_id', FILTER_SANITIZE_NUMBER_INT);

    $deleteQuery = "DELETE FROM Tavolina WHERE ID_Tavoline = ?";
    $params = array($table_id);

    if (sqlsrv_query($conn, $deleteQuery, $params)) {
        echo "<p style='color: green;'>Table deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting table.</p>";
    }
}

// Handle updating table capacity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_capacity'])) {
    $table_id = filter_input(INPUT_POST, 'table_id', FILTER_SANITIZE_NUMBER_INT);
    $new_capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);

    $updateQuery = "UPDATE Tavolina SET Kapaciteti = ? WHERE ID_Tavoline = ?";
    $params = array($new_capacity, $table_id);

    if (sqlsrv_query($conn, $updateQuery, $params)) {
        echo "<p style='color: green;'>Table capacity updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating table capacity.</p>";
    }
}

// Handle adding a new table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_table'])) {
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_NUMBER_INT);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);

    $sql = "INSERT INTO Tavolina (Numri_Tavoline, Kapaciteti) VALUES (?, ?)";
    $params = array($number, $capacity);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        echo "<p style='color: green;'>Table added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error adding table.</p>";
    }
}

// Fetch reserved tables with user details and reservation time
$query_reserved = "
    SELECT R.ID_Rezervim, U.Emri, U.Mbiemri, T.Numri_Tavoline, R.Data_Rezervimit, R.Ora_Rezervimit, R.Numri_Personave
    FROM Rezervimi R
    INNER JOIN Tavolina T ON R.ID_Tavoline = T.ID_Tavoline
    INNER JOIN Users U ON R.ID_User = U.ID_User
    ORDER BY R.Data_Rezervimit ASC, R.Ora_Rezervimit ASC";
$result_reserved = sqlsrv_query($conn, $query_reserved);

// Fetch all tables
$query_tables = "SELECT ID_Tavoline, Numri_Tavoline, Kapaciteti FROM Tavolina ORDER BY Numri_Tavoline ASC";
$result_tables = sqlsrv_query($conn, $query_tables);

if ($result_tables === false || $result_reserved === false) {
    die("<p style='color:red;'>Error fetching tables: " . print_r(sqlsrv_errors(), true) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Tables</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 1000px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }
    th {
        background: #2E7D32;
        color: white;
    }
    button {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color: #388E3C;
    }
    .delete-btn {
        background-color: #f44336;
    }
    .delete-btn:hover {
        background-color: #c62828;
    }
    input[type="number"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 80px;
        text-align: center;
    }
    .form-inline {
        display: flex;
        justify-content: center;
        gap: 10px;
        align-items: center;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Tavolinat e rezervuara</h2>
    <table>
        <tr>
            <th>ID rezervimit</th>
            <th>Rezervuar nga</th>
            <th>Nr tavolines</th>
            <th>Data</th>
            <th>Ora</th>
            <th>Nr i personave</th>
            <th></th>
        </tr>
        <?php while ($row = sqlsrv_fetch_array($result_reserved, SQLSRV_FETCH_ASSOC)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_Rezervim']) ?></td>
                <td><?= htmlspecialchars($row['Emri']) . " " . htmlspecialchars($row['Mbiemri']) ?></td>
                <td><?= htmlspecialchars($row['Numri_Tavoline']) ?></td>
                <td><?= htmlspecialchars($row['Data_Rezervimit']->format('Y-m-d')) ?></td>
                <td><?= htmlspecialchars($row['Ora_Rezervimit']->format('H:i')) ?></td>
                <td><?= htmlspecialchars($row['Numri_Personave']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="reservation_id" value="<?= $row['ID_Rezervim'] ?>">
                        <button type="submit" name="delete_reservation" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Te gjitha tavolinat</h2>
    <table>
        <tr>
            <th>ID tavolines</th>
            <th>Nr tavolines</th>
            <th>Kapaciteti</th>
            <th></th>
        </tr>
        <?php while ($row = sqlsrv_fetch_array($result_tables, SQLSRV_FETCH_ASSOC)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_Tavoline']) ?></td>
                <td><?= htmlspecialchars($row['Numri_Tavoline']) ?></td>
                <td><?= htmlspecialchars($row['Kapaciteti']) ?></td>
                <td>
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="table_id" value="<?= $row['ID_Tavoline'] ?>">
                        <input type="number" name="capacity" value="<?= $row['Kapaciteti'] ?>" min="1" required>
                        <button type="submit" name="update_capacity">Update</button>
                        <button type="submit" name="delete_table" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
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
