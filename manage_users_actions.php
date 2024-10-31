<?php
include 'db_connection_userroles.php';
header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $user_id = $_POST['user_id'];
        $status = $_POST['status'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO Users (name, lastname, user_id, status, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $lastname, $user_id, $status, $role);
        $stmt->execute();

        $response['success'] = true;
        $response['user'] = [
            'id' => $conn->insert_id,
            'name' => $name,
            'lastname' => $lastname,
            'user_id' => $user_id,
            'status' => $status,
            'role' => $role
        ];
        $stmt->close();
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['success'] = true;
        $stmt->close();
    }
}

echo json_encode($response);
