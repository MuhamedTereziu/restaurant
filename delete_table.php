<?php
include 'config.php';

if (isset($_GET['id'])) {
    $table_id = $_GET['id'];
    
    $sql = "DELETE FROM Tavolina WHERE ID_Tavoline = ?";
    $params = array($table_id);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        header("Location: manage_tables.php");
        exit();
    } else {
        echo "Error deleting table.";
    }
}
?>
