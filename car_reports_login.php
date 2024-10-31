<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบข้อมูลรถยนต์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">ตรวจสอบข้อมูลรถยนต์</h2>
        
        <!-- ฟอร์มการกรอก Division และ Running Number -->
        <form method="POST" class="space-y-4">
            <div>
                <label for="division" class="block text-sm font-medium text-gray-700">Division</label>
                <input type="text" name="division" id="division" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="running_number" class="block text-sm font-medium text-gray-700">Running Number</label>
                <input type="text" name="running_number" id="running_number" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit" name="search" class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                ตรวจสอบข้อมูล
            </button>
        </form>

        <?php
        // ตรวจสอบการกดปุ่ม search
        if (isset($_POST['search'])) {
            // เชื่อมต่อฐานข้อมูล
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "Car_Management";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("<p class='text-red-500 mt-4'>การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error . "</p>");
            }

            // รับข้อมูลจากฟอร์ม
            $division = $_POST['division'];
            $running_number = $_POST['running_number'];

            // ค้นหาข้อมูลในตาราง Manage_Cars
            $sql = "SELECT * FROM Manage_Cars WHERE Division = ? AND Running_Number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $division, $running_number);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2 class='text-2xl font-bold text-center mt-8'>ผลการค้นหา:</h2>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-gray-200 p-4 mt-4 rounded-lg'>";
                    echo "<p><strong>ชื่อ:</strong> " . $row['Name'] . " " . $row['Lastname'] . "</p>";
                    echo "<p><strong>Student ID:</strong> " . $row['Student_ID'] . "</p>";
                    echo "<p><strong>Province:</strong> " . $row['Province'] . "</p>";
                    echo "<p><strong>Car Band:</strong> " . $row['Car_Band'] . "</p>";
                    echo "<p><strong>Color of Vehicle:</strong> " . $row['Color_of_Vehicle'] . "</p>";
                    echo "<p><strong>Vehicle Type:</strong> " . $row['Vehicle_Type'] . "</p>";
                    echo "<p><strong>Engine Number:</strong> " . $row['Engine_Number'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-red-500 text-center mt-8'>ไม่พบข้อมูลรถที่ระบุ</p>";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>

</body>
</html>
