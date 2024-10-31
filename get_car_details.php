<?php
include 'db_connection_cars.php';

if (isset($_GET['car_id'])) {
    $carId = $_GET['car_id'];
    $stmt = $conn->prepare("SELECT * FROM Manage_Cars WHERE ID = ?");
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    $carDetails = $result->fetch_assoc();
    echo json_encode($carDetails);
}
?>
