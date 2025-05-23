<?php include 'db.php'; ?>
<h2>All Shipments</h2>
<a href="add.php">Add New Shipment</a>
<table border="1">
<tr>
    <th>Tracking #</th><th>Status</th><th>Shipper</th><th>Receiver</th><th>Action</th>
</tr>
<?php
$result = $conn->query("SELECT * FROM shipments");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['tracking_number']}</td>
        <td>{$row['status']}</td>
        <td>{$row['shipper_name']}</td>
        <td>{$row['receiver_name']}</td>
        <td>
            <a href='edit.php?id={$row['tracking_number']}'>Edit</a> | 
            <a href='delete.php?id={$row['tracking_number']}'>Delete</a>
        </td>
    </tr>";
}
?>
</table>
