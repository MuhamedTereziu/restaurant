<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $table_id = filter_input(INPUT_POST, 'table_id', FILTER_SANITIZE_NUMBER_INT);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $num_people = filter_input(INPUT_POST, 'num_people', FILTER_SANITIZE_NUMBER_INT);

    // Check if the table is already reserved at the selected date and time
    $checkQuery = "
        SELECT COUNT(*) AS count 
        FROM Rezervimi 
        WHERE ID_Tavoline = ? AND Data_Rezervimit = ? AND Ora_Rezervimit = ?";
    
    $checkParams = array($table_id, $date, $time);
    $checkStmt = sqlsrv_query($conn, $checkQuery, $checkParams);
    $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

    if ($row['count'] > 0) {
        echo "<p style='color: red;'>Tavolina është e rezervuar për këtë orë.</p>";
    } else {
        // Insert the reservation if no conflicts
        $insertQuery = "INSERT INTO Rezervimi (ID_User, ID_Tavoline, Data_Rezervimit, Ora_Rezervimit, Numri_Personave, Statusi)
                        VALUES (?, ?, ?, ?, ?, 'Konfirmuar')";
        $insertParams = array($user_id, $table_id, $date, $time, $num_people);

        if (sqlsrv_query($conn, $insertQuery, $insertParams)) {
            echo "<p style='color: green;'>Rezervimi u konfirmua me sukses!</p>";
        } else {
            echo "<p style='color: red;'>Gabim: " . print_r(sqlsrv_errors(), true) . "</p>";
        }
    }
}

// Fetch available tables
$query = "SELECT ID_Tavoline, Numri_Tavoline FROM Tavolina WHERE Statusi = 'E Lire'";
$result = sqlsrv_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rezervo nje tavoline</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 600px;
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
        border: 1px solid #ccc;
        border-radius: 5px;
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
    .error {
        color: red;
        font-weight: bold;
    }
    .success {
        color: green;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Rezervo nje tavoline</h2>
    <form method="POST">
        <label>Zgjidh tavolinen:</label>
        <select name="table_id" required>
            <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
                <option value="<?= htmlspecialchars($row['ID_Tavoline']) ?>">
                    Tavolina <?= htmlspecialchars($row['Numri_Tavoline']) ?>
                </option>
            <?php } ?>
        </select><br>

        <label>Data:</label>
        <input type="date" name="date" required><br>

        <label>Ora:</label>
        <input type="time" name="time" required><br>

        <label>Nr. personave:</label>
        <input type="number" name="num_people" required min="1"><br>

        <button type="submit">Rezervo</button>
    </form>
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
