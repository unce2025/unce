<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = 'localhost';
$db = 'sql3780760'; // Your database name
$user = 'sql3780760'; // Your database username
$pass = '7b6vGt5LVg'; // Your database password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$action = $_GET['action'] ?? '';

if ($action === 'search') {
  $tracking_number = $conn->real_escape_string($_GET['tracking_number']);
  $result = $conn->query("SELECT * FROM shipments WHERE tracking_number = '$tracking_number'");
  if ($result->num_rows > 0) {
    echo json_encode(["shipment" => $result->fetch_assoc()]);
  } else {
    echo json_encode(["shipment" => null]);
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);

  $tracking_number = $conn->real_escape_string($input['tracking_number']);
  $status = $conn->real_escape_string($input['status']);
  $shipper_name = $conn->real_escape_string($input['shipper_name']);
  $shipper_phone = $conn->real_escape_string($input['shipper_phone']);
  $shipper_address = $conn->real_escape_string($input['shipper_address']);
  $shipper_email = $conn->real_escape_string($input['shipper_email']);
  $receiver_name = $conn->real_escape_string($input['receiver_name']);
  $receiver_phone = $conn->real_escape_string($input['receiver_phone']);
  $receiver_address = $conn->real_escape_string($input['receiver_address']);
  $receiver_email = $conn->real_escape_string($input['receiver_email']);
  $estimated_delivery_date = $conn->real_escape_string($input['estimated_delivery_date']);
  $shipped_date = $conn->real_escape_string($input['shipped_date']);
  $flight_number = $conn->real_escape_string($input['flight_number']);
  $package_weight = $conn->real_escape_string($input['package_weight']);
  $mode = $conn->real_escape_string($input['mode']);
  $product = $conn->real_escape_string($input['product']);
  $quantity = intval($input['quantity']);
  $payment = $conn->real_escape_string($input['payment']);
  $total_freight = $conn->real_escape_string($input['total_freight']);
  $history_json = $conn->real_escape_string($input['history_json']);
  $log_json = $conn->real_escape_string($input['log_json']);

  if ($action === 'add') {
    $sql = "INSERT INTO shipments (tracking_number, status, shipper_name, shipper_phone, shipper_address, shipper_email, receiver_name, receiver_phone, receiver_address, receiver_email, estimated_delivery_date, shipped_date, flight_number, package_weight, mode, product, quantity, payment, total_freight, history_json, log_json) VALUES ('$tracking_number', '$status', '$shipper_name', '$shipper_phone', '$shipper_address', '$shipper_email', '$receiver_name', '$receiver_phone', '$receiver_address', '$receiver_email', '$estimated_delivery_date', '$shipped_date', '$flight_number', '$package_weight', '$mode', '$product', $quantity, '$payment', '$total_freight', '$history_json', '$log_json')";
    
    if ($conn->query($sql)) {
      echo json_encode(["message" => "Shipment added successfully"]);
    } else {
      echo json_encode(["error" => $conn->error]);
    }
    exit;
  }

  if ($action === 'edit') {
    $sql = "UPDATE shipments SET status='$status', shipper_name='$shipper_name', shipper_phone='$shipper_phone', shipper_address='$shipper_address', shipper_email='$shipper_email', receiver_name='$receiver_name', receiver_phone='$receiver_phone', receiver_address='$receiver_address', receiver_email='$receiver_email', estimated_delivery_date='$estimated_delivery_date', shipped_date='$shipped_date', flight_number='$flight_number', package_weight='$package_weight', mode='$mode', product='$product', quantity=$quantity, payment='$payment', total_freight='$total_freight', history_json='$history_json', log_json='$log_json' WHERE tracking_number='$tracking_number'";

    if ($conn->query($sql)) {
      echo json_encode(["message" => "Shipment updated successfully"]);
    } else {
      echo json_encode(["error" => $conn->error]);
    }
    exit;
  }
}

$conn->close();
echo json_encode(["error" => "Invalid request"]);
