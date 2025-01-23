<?php
header('Content-Type: text/html; charset=utf-8');
include 'config.php';
include 'session_check.php';

// Secure session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

    // Ensure a valid reservation is selected
    if ($reservation_id) {
        // Insert the order linked to the selected reservation
        $sql = "INSERT INTO Porosia (ID_Rezervim, ID_Artikull, Sasia) VALUES (?, ?, ?)";
        $params = array($reservation_id, $item_id, $quantity);

        if (sqlsrv_query($conn, $sql, $params)) {
            echo "<p style='color: green;'>Order placed successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . print_r(sqlsrv_errors(), true) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Please select a reservation.</p>";
    }
}

// Fetch menu items
$query = "SELECT ID_Artikull, Emri_Artikullit, Cmimi FROM Menyja";
$result = sqlsrv_query($conn, $query);

// Fetch user's reservations for selection
$reservationQuery = "
    SELECT ID_Rezervim, Data_Rezervimit, Ora_Rezervimit, T.Numri_Tavoline
    FROM Rezervimi R
    INNER JOIN Tavolina T ON R.ID_Tavoline = T.ID_Tavoline
    WHERE R.ID_User = ?
    ORDER BY R.Data_Rezervimit DESC, R.Ora_Rezervimit DESC";
$reservationStmt = sqlsrv_query($conn, $reservationQuery, array($user_id));

// Fetch reservations with linked orders
$reservationOrdersQuery = "
    SELECT R.ID_Rezervim, R.Data_Rezervimit, R.Ora_Rezervimit, T.Numri_Tavoline, M.Emri_Artikullit, P.Sasia
    FROM Rezervimi R
    LEFT JOIN Tavolina T ON R.ID_Tavoline = T.ID_Tavoline
    LEFT JOIN Porosia P ON R.ID_Rezervim = P.ID_Rezervim
    LEFT JOIN Menyja M ON P.ID_Artikull = M.ID_Artikull
    WHERE R.ID_User = ?
    ORDER BY R.Data_Rezervimit DESC, R.Ora_Rezervimit DESC";
$reservationOrdersStmt = sqlsrv_query($conn, $reservationOrdersQuery, array($user_id));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Place Order</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
        font-weight: bold;
    }
    input, select, button {
        display: block;
        width: 100%;
        margin-top: 10px;
        padding: 10px;
    }
    button {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color: #218838;
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
        background: #4CAF50;
        color: white;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Bej nje porosi</h2>
    <form method="POST">
        <label>Zgjidh rezervimin:</label>
        <select name="reservation_id" required>
            <option value="">Zgjidh rezervimin:</option>
            <?php while ($row = sqlsrv_fetch_array($reservationStmt, SQLSRV_FETCH_ASSOC)) { ?>
                <option value="<?= htmlspecialchars($row['ID_Rezervim']) ?>">
                    Reservation <?= htmlspecialchars($row['ID_Rezervim']) ?> - Tavolina <?= htmlspecialchars($row['Numri_Tavoline']) ?> (<?= htmlspecialchars($row['Data_Rezervimit']->format('Y-m-d')) ?> at <?= htmlspecialchars($row['Ora_Rezervimit']->format('H:i')) ?>)
                </option>
            <?php } ?>
        </select><br>

        <label>Zgjidh produktin:</label>
        <select name="item_id" required>
            <option value="">Zgjidh produktin:</option>
            <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
                <option value="<?= htmlspecialchars($row['ID_Artikull']) ?>">
                    <?= htmlspecialchars($row['Emri_Artikullit']) ?> - <?= htmlspecialchars($row['Cmimi']) ?> Lek
                </option>
            <?php } ?>
        </select><br>

        <label>Sasia:</label>
        <input type="number" name="quantity" required min="1"><br>

        <button type="submit">Bej porosine</button>
    </form>

    <h2>Rezervimet e tua dhe porosite</h2>
    <table>
        <tr>
            <th>ID rezervimit</th>
            <th>Data</th>
            <th>Ora</th>
            <th>Nr tavolines</th>
            <th>Produkti</th>
            <th>Sasia</th>
        </tr>
        <?php while ($row = sqlsrv_fetch_array($reservationOrdersStmt, SQLSRV_FETCH_ASSOC)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_Rezervim']) ?></td>
                <td><?= htmlspecialchars($row['Data_Rezervimit']->format('Y-m-d')) ?></td>
                <td><?= htmlspecialchars($row['Ora_Rezervimit']->format('H:i')) ?></td>
                <td><?= htmlspecialchars($row['Numri_Tavoline']) ?></td>
                <td><?= htmlspecialchars($row['Emri_Artikullit'] ?? 'No Order') ?></td>
                <td><?= htmlspecialchars($row['Sasia'] ?? '-') ?></td>
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
