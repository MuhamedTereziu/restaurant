<?php
$serverName = "DEL";  // 
$connectionOptions = array(
    "Database" => "RestorantAlbulena",
    "TrustServerCertificate" => true
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
} else {
    echo "Connection successful!";
}
?>
