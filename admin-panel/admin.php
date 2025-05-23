<?php
include 'db.php';

$edit_mode = false;
$edit_data = [];

// Handle Add or Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $tracking = $_POST['tracking_number'];
        $status = $_POST['status'];
        $shipper = $_POST['shipper_name'];
        $receiver = $_POST['receiver_name'];

        // If editing existing
        if (!empty($_POST['is_edit'])) {
            $stmt = $conn->prepare("UPDATE shipments SET status=?, shipper_name=?, receiver_name=? WHERE tracking_number=?");
            $stmt->bind_param("ssss", $status, $shipper, $receiver, $tracking);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO shipments (tracking_number, status, shipper_name, receiver_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $tracking, $status, $shipper, $receiver);
            $stmt->execute();
        }

        header("Location: admin.php");
        exit;
    }
}

// Handle Edit button
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM shipments WHERE tracking_number='$id'");
    $edit_data = $result->fetch_assoc();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM shipments WHERE tracking_number='$id'");
    header("Location: admin.php");
    exit;
}

// Handle Search
$search_results = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $res = $conn->query("SELECT * FROM shipments WHERE tracking_number LIKE '%$search%'");
    $search_results = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<h2>Admin Panel - Shipment Manager</h2>

<!-- Search -->
<form method="get">
    <input type="text" name="search" placeholder="Search Tracking Number">
    <input type="submit" value="Search">
</form>

<?php if ($search_results): ?>
    <h3>Search Results:</h3>
    <table border="1" cellpadding="5">
        <tr><th>Tracking #</th><th>Status</th><th>Shipper</th><th>Receiver</th></tr>
        <?php foreach ($search_results as $row): ?>
            <tr>
                <td><?= $row['tracking_number'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['shipper_name'] ?></td>
                <td><?= $row['receiver_name'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<!-- Add/Edit Form -->
<h3><?= $edit_mode ? 'Edit Shipment' : 'Add New Shipment' ?></h3>
<form method="post">
    <input type="hidden" name="is_edit" value="<?= $edit_mode ? '1' : '' ?>">
    Tracking #: <input type="text" name="tracking_number" value="<?= $edit_mode ? $edit_data['tracking_number'] : '' ?>" <?= $edit_mode ? 'readonly' : '' ?>><br>
    Status: <input type="text" name="status" value="<?= $edit_mode ? $edit_data['status'] : '' ?>"><br>
    Shipper Name: <input type="text" name="shipper_name" value="<?= $edit_mode ? $edit_data['shipper_name'] : '' ?>"><br>
    Receiver Name: <input type="text" name="receiver_name" value="<?= $edit_mode ? $edit_data['receiver_name'] : '' ?>"><br>
    <input type="submit" name="save" value="<?= $edit_mode ? 'Save Changes' : 'Add Shipment' ?>">
</form>

<!-- List All -->
<h3>All Shipments</h3>
<table border="1" cellpadding="5">
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
            <a href='admin.php?edit={$row['tracking_number']}'>Edit</a> | 
            <a href='admin.php?delete={$row['tracking_number']}' onclick=\"return confirm('Delete this shipment?')\">Delete</a>
        </td>
    </tr>";
}
?>
</table>
