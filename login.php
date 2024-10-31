<?php
session_start();
include 'db_connection_userroles.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$alert_message = ''; // ตัวแปรเก็บข้อความแจ้งเตือน

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['username'];
    $role = $_POST['password'];

    // ตรวจสอบการพยายามล็อกอิน
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3) {
        $last_attempt_time = $_SESSION['last_attempt_time'] ?? time();
        $wait_time = time() - $last_attempt_time;

        if ($wait_time < 10) {
            $alert_message = "<div class='p-4 mb-4 text-sm text-red-600 rounded-xl bg-red-50 font-normal' role='alert'>
                                <span class='font-semibold mr-2'>ล็อกอินไม่สำเร็จ</span> กรุณาลองใหม่หลังจาก " . (10 - $wait_time) . " วินาที
                              </div>";
            exit();
        } else {
            $_SESSION['login_attempts'] = 0; // รีเซ็ตตัวนับเมื่อครบเวลา
        }
    }

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล พร้อม `status`
    $stmt = $conn->prepare("SELECT id, user_id, role, status FROM Users WHERE user_id = ? AND role = ?");
    $stmt->bind_param("ss", $user_id, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['username'] = $user_id;
        $_SESSION['role'] = $user['role'];
        $_SESSION['status'] = $user['status']; // เพิ่ม status ลงในเซสชัน

        // บันทึกการล็อกอินสำเร็จในตาราง UserRoles
        $stmt = $conn->prepare("INSERT INTO UserRoles (user_id, action) VALUES (?, 'Login Success')");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        $alert_message = "<div class='p-4 mb-4 text-sm text-green-600 rounded-xl bg-green-50 font-normal' role='alert'>
                            <span class='font-semibold mr-2'>เข้าสู่ระบบสำเร็จ</span> ยินดีต้อนรับ $user_id
                          </div>";

        echo "<script>setTimeout(function(){ window.location.href = 'admin.php'; }, 2000);</script>";
    } else {
        // บันทึกการล็อกอินไม่สำเร็จในตาราง UserRoles
        if ($user) {
            $stmt = $conn->prepare("INSERT INTO UserRoles (user_id, action) VALUES (?, 'Login Fail')");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt_time'] = time();

        $alert_message = "<div class='p-4 mb-4 text-sm text-red-600 rounded-xl bg-red-50 font-normal' role='alert'>
                            <span class='font-semibold mr-2'>ล็อกอินไม่สำเร็จ</span> ID หรือ Role ไม่ถูกต้อง
                          </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="../node_modules/tailwindcss/tailwind.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


<style>
    body {
    font-family: 'Prompt', sans-serif;
    }
</style>

</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm bg-white rounded-lg shadow-lg p-8">
        <center><img src="assets/icons/iconmain.png" alt="Settings Icon" class="w-12 h-14 mb-4"></center>
        <h2 class="text-3xl font-bold text-gray-700 text-center mb-6">CR BDC Panel Login</h2>

        <?php
        // แสดงข้อความแจ้งเตือนถ้ามี
        if (!empty($alert_message)) {
            echo $alert_message;
        }
        ?>

        <form action="login.php" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold">ID</label>
                <input type="text" name="username" id="username" class="w-full p-3 border border-gray-300 rounded mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-semibold">Role</label>
                <input type="text" name="password" id="password" class="w-full p-3 border border-gray-300 rounded mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Login
            </button>
        </form>
    </div>
</body>
</html>
