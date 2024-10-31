<?php
include 'db_connection_userroles.php';
header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action == 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $user_id = $_POST['user_id'];
        $status = $_POST['status'];
        $role = $_POST['role'];

        // ตรวจสอบให้แน่ใจว่าการอัปเดตในฐานข้อมูลสำเร็จ
        $stmt = $conn->prepare("UPDATE Users SET name = ?, lastname = ?, user_id = ?, status = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $lastname, $user_id, $status, $role, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['user'] = [
                'id' => $id,
                'name' => $name,
                'lastname' => $lastname,
                'user_id' => $user_id,
                'status' => $status,
                'role' => $role
            ];
        } else {
            $response['message'] = "Failed to update user in database.";
        }

        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>
