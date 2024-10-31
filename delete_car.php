<?php
include 'db_connection_cars.php';

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['car_id'])) {
    $carId = $data['car_id'];
    $stmt = $conn->prepare("DELETE FROM Manage_Cars WHERE ID = ?");
    $stmt->bind_param("i", $carId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}
?>
