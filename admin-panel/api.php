<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DB connection info - replace with your own
$host = "localhost";          // your DB host, e.g. localhost
$user = "sql3780760";   // your DB username
$pass = "sql3780760";   // your DB password
$dbname = "7b6vGt5LVg";     // your DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($input['action'] ?? '');

if ($method == 'GET' && $action == 'search') {
    $tracking = $conn->real_escape_string($_GET['tracking_number'] ?? '');

    if (!$tracking) {
        http_response_code(400);
        echo json_encode(["error" => "Tracking number is required"]);
        exit();
    }

    $sql = "SELECT * FROM shipments WHERE tracking_number = '$tracking' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $shipment = $result->fetch_assoc();

        // Decode JSON fields before sending
        $shipment['history_json'] = json_decode($shipment['history_json'], true);
        $shipment['log_json'] = json_decode($shipment['log_json'], true);

        echo json_encode(["shipment" => $shipment]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Shipment not found"]);
    }
    exit();
}

if ($method == 'POST') {
    if ($action == 'add') {
        // Sanitize & prepare all fields
        $tracking_number = $conn->real_escape_string($input['tracking_number'] ?? '');
        $status = $conn->real_escape_string($input['status'] ?? '');
        $shipper_name = $conn->real_escape_string($input['shipper_name'] ?? '');
        $shipper_phone = $conn->real_escape_string($input['shipper_phone'] ?? '');
        $shipper_address = $conn->real_escape_string($input['shipper_address'] ?? '');
        $shipper_email = $conn->real_escape_string($input['shipper_email'] ?? '');
        $receiver_name = $conn->real_escape_string($input['receiver_name'] ?? '');
        $receiver_phone = $conn->real_escape_string($input['receiver_phone'] ?? '');
        $receiver_address = $conn->real_escape_string($input['receiver_address'] ?? '');
        $receiver_email = $conn->real_escape_string($input['receiver_email'] ?? '');
        $estimated_delivery_date = $conn->real_escape_string($input['estimated_delivery_date'] ?? '');
        $shipped_date = $conn->real_escape_string($input['shipped_date'] ?? '');
        $flight_number = $conn->real_escape_string($input['flight_number'] ?? '');
        $package_weight = $conn->real_escape_string($input['package_weight'] ?? '');
        $mode = $conn->real_escape_string($input['mode'] ?? '');
        $product = $conn->real_escape_string($input['product'] ?? '');
        $quantity = intval($input['quantity'] ?? 0);
        $payment = $conn->real_escape_string($input['payment'] ?? '');
        $total_freight = $conn->real_escape_string($input['total_freight'] ?? '');
        $history_json = $conn->real_escape_string(json_encode($input['history_json'] ?? []));
        $log_json = $conn->real_escape_string(json_encode($input['log_json'] ?? []));

        if (!$tracking_number || !$status) {
            http_response_code(400);
            echo json_encode(["error" => "Tracking number and status are required"]);
            exit();
        }

        $sql = "INSERT INTO shipments (
            tracking_number, status, shipper_name, shipper_phone, shipper_address, shipper_email,
            receiver_name, receiver_phone, receiver_address, receiver_email,
            estimated_delivery_date, shipped_date, flight_number, package_weight, mode,
            product, quantity, payment, total_freight, history_json, log_json
        ) VALUES (
            '$tracking_number', '$status', '$shipper_name', '$shipper_phone', '$shipper_address', '$shipper_email',
            '$receiver_name', '$receiver_phone', '$receiver_address', '$receiver_email',
            '$estimated_delivery_date', '$shipped_date', '$flight_number', '$package_weight', '$mode',
            '$product', $quantity, '$payment', '$total_freight', '$history_json', '$log_json'
        )";

        if ($conn->query($sql)) {
            echo json_encode(["message" => "Shipment added"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add shipment: " . $conn->error]);
        }
        exit();
    } elseif ($action == 'edit') {
        $tracking_number = $conn->real_escape_string($input['tracking_number'] ?? '');
        if (!$tracking_number) {
            http_response_code(400);
            echo json_encode(["error" => "Tracking number is required"]);
            exit();
        }

        $fields = [];

        $map_fields = [
            "status", "shipper_name", "shipper_phone", "shipper_address", "shipper_email",
            "receiver_name", "receiver_phone", "receiver_address", "receiver_email",
            "estimated_delivery_date", "shipped_date", "flight_number", "package_weight", "mode",
            "product", "quantity", "payment", "total_freight"
        ];

        foreach ($map_fields as $field) {
            if (isset($input[$field])) {
                if ($field === "quantity") {
                    $fields[] = "$field = " . intval($input[$field]);
                } else {
                    $fields[] = "$field = '" . $conn->real_escape_string($input[$field]) . "'";
                }
            }
        }

        // Handle JSON fields separately
        if (isset($input['history_json'])) {
            $fields[] = "history_json = '" . $conn->real_escape_string(json_encode($input['history_json'])) . "'";
        }
        if (isset($input['log_json'])) {
            $fields[] = "log_json = '" . $conn->real_escape_string(json_encode($input['log_json'])) . "'";
        }

        if (count($fields) == 0) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit();
        }

        $sql = "UPDATE shipments SET " . implode(", ", $fields) . " WHERE tracking_number = '$tracking_number'";

        if ($conn->query($sql)) {
            echo json_encode(["message" => "Shipment updated"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update shipment: " . $conn->error]);
        }
        exit();
    }
}

http_response_code(400);
echo json_encode(["error" => "Invalid request"]);
$conn->close();
?>
