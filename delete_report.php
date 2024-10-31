<?php
include 'db_connection_cars.php';

$data = json_decode(file_get_contents('php://input'), true);
$reportId = $data['reportId'];

if ($reportId) {
    $stmt = $conn->prepare("DELETE FROM Car_Reports WHERE Report_ID = ?");
    $stmt->bind_param("i", $reportId);
    $stmt->execute();

    echo json_encode(['success' => $stmt->affected_rows > 0]);
    $stmt->close();
}
$conn->close();
?>
