<?php
session_start();
include 'db_connection_userroles.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรเก็บผลลัพธ์การ logout

// ตรวจสอบว่าผู้ใช้ login อยู่หรือไม่
if (isset($_SESSION['username'])) {
    $user_id = $_SESSION['username'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT id FROM Users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // บันทึกการ logout ลงในตาราง UserRoles
        $stmt = $conn->prepare("INSERT INTO UserRoles (user_id, action) VALUES (?, 'Logout')");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        // ส่งผลลัพธ์ออกมา
        $response['status'] = 'success';
        $response['message'] = "ออกจากระบบสำเร็จ ลาก่อน $user_id";
    } else {
        $response['status'] = 'error';
        $response['message'] = 'ไม่พบข้อมูลผู้ใช้ในระบบ';
    }
}

// ล้าง session
session_unset();
session_destroy();

echo json_encode($response); // ส่งผลลัพธ์เป็น JSON กลับไปยัง client
exit();
?>
