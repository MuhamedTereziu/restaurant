<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT ID_User, Emri, Mbiemri, Email, Fjalekalimi, Roli FROM Users WHERE Email = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row && $password === $row['Fjalekalimi']) {
        $_SESSION['user_email'] = $row['Email'];
        $_SESSION['user_role'] = $row['Roli'];
        $_SESSION['user_id'] = $row['ID_User'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>
