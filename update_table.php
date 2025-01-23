<?php
include 'config.php';

if (isset($_GET['id'])) {
    $table_id = $_GET['id'];
    
    $sql = "SELECT * FROM Tavolina WHERE ID_Tavoline = ?";
    $params = array($table_id);
    $stmt = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_id = $_POST['id'];
    $capacity = $_POST['capacity'];
    $status = $_POST['status'];

    $sql = "UPDATE Tavolina SET Kapaciteti = ?, Statusi = ? WHERE ID_Tavoline = ?";
    $params = array($capacity, $status, $table_id);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        header("Location: manage_tables.php");
        exit();
    } else {
        echo "Error updating table.";
    }
}
?>

<form method="POST">
    <input type="hidden" name="id" value="<?= $row['ID_Tavoline'] ?>">
    <label>Capacity:</label>
    <input type="number" name="capacity" value="<?= $row['Kapaciteti'] ?>" required><br>
    <label>Status:</label>
    <select name="status">
        <option value="Available" <?= $row['Statusi'] == 'Available' ? 'selected' : '' ?>>Available</option>
        <option value="Occupied" <?= $row['Statusi'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
    </select><br>
    <button type="submit">Update Table</button>
</form>
