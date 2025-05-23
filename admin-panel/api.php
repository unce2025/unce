<?php
// Database credentials
$host = 'localhost';
$db   = 'sql3780760'; // Your database name
$user = 'sql3780760';  // Your DB username
$pass = '7b6vGt5LVg';  // Your DB password

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed.']));
}

// Set JSON header
header('Content-Type: application/json');

// Get action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'get') {
    $tracking = $_GET['tracking'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM shipments WHERE TrackingNumber = ?");
    $stmt->bind_param("s", $tracking);
    $stmt->execute();
    $result = $stmt->get_result();
    $shipment = $result->fetch_assoc();
    echo json_encode($shipment ?: ['error' => 'Tracking number not found']);
    exit;
}

if ($action === 'save') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $stmt = $conn->prepare("INSERT INTO shipments (
        TrackingNumber, Status, ShipperName, ShipperPhone, ShipperAddress, ShipperEmail,
        ReceiverName, ReceiverPhone, ReceiverAddress, ReceiverEmail,
        EstimatedDeliveryDate, ShippedDate, FlightNumber, PackageWeight, Mode,
        Product, Quantity, Payment, TotalFreight, log, history
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        Status=VALUES(Status), ShipperName=VALUES(ShipperName), ShipperPhone=VALUES(ShipperPhone),
        ShipperAddress=VALUES(ShipperAddress), ShipperEmail=VALUES(ShipperEmail),
        ReceiverName=VALUES(ReceiverName), ReceiverPhone=VALUES(ReceiverPhone),
        ReceiverAddress=VALUES(ReceiverAddress), ReceiverEmail=VALUES(ReceiverEmail),
        EstimatedDeliveryDate=VALUES(EstimatedDeliveryDate), ShippedDate=VALUES(ShippedDate),
        FlightNumber=VALUES(FlightNumber), PackageWeight=VALUES(PackageWeight),
        Mode=VALUES(Mode), Product=VALUES(Product), Quantity=VALUES(Quantity),
        Payment=VALUES(Payment), TotalFreight=VALUES(TotalFreight), log=VALUES(log),
        history=VALUES(history)");

    $stmt->bind_param("ssssssssssssssssssss", 
        $data['TrackingNumber'], $data['Status'], $data['ShipperName'], $data['ShipperPhone'],
        $data['ShipperAddress'], $data['ShipperEmail'], $data['ReceiverName'], $data['ReceiverPhone'],
        $data['ReceiverAddress'], $data['ReceiverEmail'], $data['EstimatedDeliveryDate'], $data['ShippedDate'],
        $data['FlightNumber'], $data['PackageWeight'], $data['Mode'], $data['Product'], $data['Quantity'],
        $data['Payment'], $data['TotalFreight'], $data['log'], $data['history']
    );

    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
    exit;
}

// Invalid action
echo json_encode(['error' => 'Invalid request']);
?>

