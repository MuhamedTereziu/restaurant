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

$user_id = $_SESSION['user_id'];

// Fetch invoices linked with reservations and orders
$query = "
    SELECT 
        R.ID_Rezervim, 
        R.Data_Rezervimit, 
        R.Ora_Rezervimit, 
        T.Numri_Tavoline, 
        SUM(P.Sasia * M.Cmimi) AS Totali
    FROM Rezervimi R
    LEFT JOIN Tavolina T ON R.ID_Tavoline = T.ID_Tavoline
    LEFT JOIN Porosia P ON R.ID_Rezervim = P.ID_Rezervim
    LEFT JOIN Menyja M ON P.ID_Artikull = M.ID_Artikull
    WHERE R.ID_User = ?
    GROUP BY R.ID_Rezervim, R.Data_Rezervimit, R.Ora_Rezervimit, T.Numri_Tavoline
    ORDER BY R.Data_Rezervimit DESC";

$params = array($user_id);
$result = sqlsrv_query($conn, $query, $params);

$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fatura</title>
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
    h2 {
        color: #2E7D32;
        text-align: center;
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
    .total {
        font-weight: bold;
        color: #2E7D32;
    }
    .grand-total {
        font-weight: bold;
        font-size: 20px;
        color: #d32f2f;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Faturat e Klientit</h2>
    <table>
        <tr>
            <th>ID Rezervimi</th>
            <th>Data</th>
            <th>Ora</th>
            <th>Numri Tavolinës</th>
            <th>Totali (Lek)</th>
        </tr>
        <?php
        if ($result && sqlsrv_has_rows($result)) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $total_amount += $row['Totali']; ?>
                <tr>
                    <td><?= htmlspecialchars($row['ID_Rezervim']) ?></td>
                    <td><?= htmlspecialchars($row['Data_Rezervimit']->format('Y-m-d')) ?></td>
                    <td><?= htmlspecialchars($row['Ora_Rezervimit']->format('H:i')) ?></td>
                    <td><?= htmlspecialchars($row['Numri_Tavoline']) ?></td>
                    <td class="total"><?= number_format($row['Totali'], 2) ?> Lek</td>
                </tr>
            <?php }
        } else {
            echo "<tr><td colspan='5'>Nuk ka fatura të disponueshme.</td></tr>";
        }
        ?>
        <tr>
            <td colspan="4" class="grand-total">Totali Gjithsej:</td>
            <td class="grand-total"><?= number_format($total_amount, 2) ?> Lek</td>
        </tr>
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
