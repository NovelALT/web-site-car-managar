<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection_cars.php';

function getAllCars() {
    global $conn;
    $sql = "SELECT ID, name, lastname, Student_ID, Division, Province, Running_Number FROM Manage_Cars ORDER BY ID DESC";
    $result = $conn->query($sql);

    $cars = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    }
    return $cars;
}

if (isset($_POST['updateCar'])) {
    $carId = $_POST['car_id'];
    $grade = $_POST['grade'];
    $room = $_POST['room'];
    $carBand = $_POST['car_band'];
    $color = $_POST['color'];
    $vehicleType = $_POST['vehicle_type'];
    $vin = $_POST['vehicle_identification_number'];
    $engineNumber = $_POST['engine_number'];
    $year = $_POST['year_of_manufacture'];
    $weight = $_POST['vehicle_weight'];
    $fuel = $_POST['fuel_type'];
    $engineCC = $_POST['engine_cc'];
    
    $stmt = $conn->prepare("UPDATE Manage_Cars SET Grade = ?, Room = ?, Car_Band = ?, Color_of_Vehicle = ?, Vehicle_Type = ?, Vehicle_Identification_Number = ?, Engine_Number = ?, Year_of_Manufacture = ?, Vehicle_Weight = ?, Fuel_Type = ?, Engine_CC = ? WHERE ID = ?");
    $stmt->bind_param("sssssssssssi", $grade, $room, $carBand, $color, $vehicleType, $vin, $engineNumber, $year, $weight, $fuel, $engineCC, $carId);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_cars.php"); // Refresh หน้า
    exit();
}



function getCarDetails($carId) {
    global $conn;
    $stmt = $conn->prepare("SELECT Grade, Room, Car_Band, Color_of_Vehicle, Vehicle_Type, Vehicle_Identification_Number, 
                            Engine_Number, Year_of_Manufacture, Vehicle_Weight, Fuel_Type, Engine_CC, 
                            Identification_Card_Document, Vehicle_Verification_Document, Creates_Timestamp 
                            FROM Manage_Cars WHERE ID = ?");
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$cars = getAllCars();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="../node_modules/tailwindcss/tailwind.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


    <style>
                body {
            font-family: 'Prompt', sans-serif;
        }

        /* กำหนดคลาสสำหรับแสดงและซ่อน Sidebar */
        .sidebar-expanded { width: 250px; }
        .sidebar-collapsed { width: 64px; }
        .sidebar-collapsed .menu-text { display: none; }
        .content-expanded { margin-left: 20px; }
        .content-full { margin-left: 0px; }
    </style>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }
    .font-bold {
        font-weight: bold;
    }
    .show-dropdown {
        display: block !important;
        opacity: 1;
    }
    .hide-dropdown {
        display: none;
        opacity: 0;
    }
</style>

<style>
    .rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

.font-bold {
    font-weight: bold;
}

.show-dropdown {
    display: block !important;
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}

.hide-dropdown {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

</style>




</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<!-- Navbar -->
<nav class="bg-white shadow-md p-4 flex items-center space-x-4">
    <!-- Toggle Button for Sidebar -->
    <img src="assets/icons/iconmain.png" alt="Settings Icon" class="w-8 h-9 mr-3">

    <!-- Title "Admin Panel" ชิดซ้ายตามปุ่ม -->
    <div class="text-xl font-bold text-gray-800">CR BDC Panel</div>
    
    <button id="toggleSidebar" class="p-2 bg-gray-800 text-white rounded-md focus:outline-none">
        ☰
    </button>
    <!-- ใช้ space-auto เพื่อดันโปรไฟล์ไปทางขวา -->
    <div class="flex-1"></div>

    <!-- Language Selector -->
    <div class="relative inline-block text-left">
        <button id="languageToggle" class="p-2 bg-gray-200 rounded-md focus:outline-none">
            <img id="currentLanguageFlag" src="assets/flags/english-flag.png" alt="Current Language" class="w-6 h-6"> <!-- Flag Image -->
        </button>
        <!-- Dropdown for Language Options -->
        <div id="languageDropdown" class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="languageToggle">
                <a href="#" onclick="setLanguage('en')" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100" role="menuitem">
                    <img src="assets/flags/english-flag.png" alt="English Flag" class="w-5 h-5 mr-2"> <!-- ธงชาติอังกฤษ -->
                    English
                </a>
                <a href="#" onclick="setLanguage('th')" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100" role="menuitem">
                    <img src="assets/flags/thai-flag.png" alt="Thai Flag" class="w-5 h-5 mr-2"> <!-- ธงชาติไทย -->
                    ไทย
                </a>
            </div>
        </div>
    </div>


        <!-- Profile Section -->
         <p>|</p>
         <span class="text-gray-600 font-medium"><?php echo htmlspecialchars($_SESSION['username']); ?> | <?php echo htmlspecialchars($_SESSION['status']); ?></span>
    </div>
</nav>


    <div class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-expanded bg-gray-900 text-white min-h-screen transition-all duration-300">
            <!-- Navigation Links with Icons -->
            <nav class="mt-4">
                <a href="admin.php" class="flex items-center py-2.5 px-4 rounded hover:bg-gray-700">
                    <img src="assets/icons/dashboard.png" alt="Dashboard Icon" class="w-5 h-5 mr-3">
                    <span class="menu-text">Dashboard</span>
                </a>
                <div class="flex items-center justify-between py-2.5 px-4 rounded hover:bg-gray-700 cursor-pointer bg-gray-700" id="userManagementToggle">
                <div class="flex items-center">
                    <img src="assets/icons/user.png" alt="User Icon" class="w-5 h-5 mr-3">
                    <span class="menu-text">User Management</span>
                </div>
                <svg id="dropdownArrow" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </div>
                <div id="userDropdown" class="hidden pl-12 mt-2 space-y-2">
                    <a href="manage_users.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 bg-gray-700 rounded">Manage Users</a>
                    <a href="user_reports.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">User Reports</a>
                </div>
                <div class="flex items-center justify-between py-2.5 px-4 rounded hover:bg-gray-700 cursor-pointer" id="carManagementToggle">
    <div class="flex items-center">
        <img src="assets/icons/car.png" alt="Car Icon" class="w-5 h-5 mr-3">
        <span class="menu-text">Car Management</span>
    </div>
    <svg id="carDropdownArrow" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a 1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>
</div>
<div id="carDropdown" class="hidden pl-12 mt-2 space-y-2">
    <a href="manage_cars.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">Manage Cars</a>
    <a href="car_reports.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">Car Reports</a>
    <a href="car_requests.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">Car request</a>
</div>

                <a href="settings.php" class="flex items-center py-2.5 px-4 rounded hover:bg-gray-700">
                    <img src="assets/icons/settings.png" alt="Settings Icon" class="w-5 h-5 mr-3">
                    <span class="menu-text">Settings</span>
                </a>
                <a href="#" onclick="openLogoutAlert()" class="flex items-center py-2.5 px-4 rounded hover:bg-red-700">
                    <img src="assets/icons/logout.png" alt="Logout Icon" class="w-5 h-5 mr-3">
                    <span class="menu-text">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Content Area -->
        <div id="content" class="flex-1 p-8 transition-all duration-300 content-expanded">
    <!-- Page Title -->
    <h2 class="text-3xl font-bold text-gray-700 lang-text" data-en="Car Reports" data-th="รายงานรถยนต์">Car Reports</h2>
    <p class="text-gray-600 lang-text" data-en="Track all car movements, statuses, and details." data-th="ติดตามความเคลื่อนไหว สถานะ และรายละเอียดรถทั้งหมด">Track all car movements, statuses, and details.</p>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
        <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total All Cars" data-th="จำนวนรถทั้งหมด">Total Wait</h3>
        <p class="text-gray-600">Data date at </p>
        <p class="text-gray-500 text-2xl"> คัน</p> <!-- แสดงจำนวนรถทั้งหมด -->
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-green-200 transition duration-300 ease-in-out">
        <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total In Garage" data-th="จำนวนรถที่อยู่ในโรง">Total Pass</h3>
        <p class="text-gray-600">Data date at </p>
        <p class="text-gray-500 text-2xl">คัน</p> <!-- แสดงจำนวนรถที่อยู่ในโรง -->
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-yellow-200 transition duration-300 ease-in-out">
        <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Out Garage" data-th="จำนวนที่ออกจากในโรง">Total Fail</h3>
        <p class="text-gray-600">Data date at </p>
        <p class="text-gray-500 text-2xl"> คัน</p> <!-- แสดงจำนวนที่ไม่อยู่ในโรง -->
    </div>
</div>

    <!-- User Role Management Section -->
    <br>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg ml-auto">
    <div class="flex justify-end">
        <div id="content" class="flex-1 p-8 transition-all duration-300 content-expanded">
            
        <div class="mt-8 overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b-2 border-gray-200">ID</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Name</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Last Name</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Student ID</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Division</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Province</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Running Number</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?php echo $car['ID']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['name']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['lastname']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['Student_ID']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['Division']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['Province']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $car['Running_Number']; ?></td>
                        <td class="py-2 px-4 border-b">
                            <button onclick="showCarInfo(<?php echo $car['ID']; ?>)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Info</button>
                            <button onclick="editCar(<?php echo $car['ID']; ?>)" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</button>
                            <button onclick="confirmDelete(<?php echo $car['ID']; ?>)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal Edit Car -->
<div id="editCarModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-4xl">
        <h3 class="text-lg font-semibold text-gray-800">Edit Car Details</h3>
        <form method="post" enctype="multipart/form-data" class="mt-4">
            <input type="hidden" name="car_id" id="editCarId">

            <!-- Row 1 -->
            <div class="flex space-x-4">
                <div class="w-1/3">
                    <label for="grade" class="block mb-2">Grade</label>
                    <input type="text" name="grade" id="editGrade" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="room" class="block mb-2">Room</label>
                    <input type="text" name="room" id="editRoom" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="car_band" class="block mb-2">Car Band</label>
                    <input type="text" name="car_band" id="editCarBand" class="border rounded-lg p-2 w-full">
                </div>
            </div>

            <!-- Row 2 -->
            <div class="flex space-x-4 mt-4">
                <div class="w-1/3">
                    <label for="color" class="block mb-2">Color</label>
                    <input type="text" name="color" id="editCarColor" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="vehicle_type" class="block mb-2">Vehicle Type</label>
                    <input type="text" name="vehicle_type" id="editVehicleType" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="vehicle_identification_number" class="block mb-2">VIN</label>
                    <input type="text" name="vehicle_identification_number" id="editVIN" class="border rounded-lg p-2 w-full">
                </div>
            </div>

            <!-- Row 3 -->
            <div class="flex space-x-4 mt-4">
                <div class="w-1/3">
                    <label for="engine_number" class="block mb-2">Engine Number</label>
                    <input type="text" name="engine_number" id="editEngineNumber" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="year_of_manufacture" class="block mb-2">Year of Manufacture</label>
                    <input type="text" name="year_of_manufacture" id="editYear" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="vehicle_weight" class="block mb-2">Vehicle Weight</label>
                    <input type="text" name="vehicle_weight" id="editWeight" class="border rounded-lg p-2 w-full">
                </div>
            </div>

            <!-- Row 4 -->
            <div class="flex space-x-4 mt-4">
                <div class="w-1/3">
                    <label for="fuel_type" class="block mb-2">Fuel Type</label>
                    <input type="text" name="fuel_type" id="editFuel" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="engine_cc" class="block mb-2">Engine CC</label>
                    <input type="text" name="engine_cc" id="editEngineCC" class="border rounded-lg p-2 w-full">
                </div>
                <div class="w-1/3">
                    <label for="create_timestamp" class="block mb-2">Created Timestamp</label>
                    <input type="text" id="createTimestamp" class="border rounded-lg p-2 w-full" disabled>
                </div>
            </div>

            <!-- Row 5: Document Images -->
            <div class="flex space-x-4 mt-4">
                <div class="w-1/2">
                    <h4 class="font-semibold mb-2">ID Document</h4>
                    <img id="identificationCardImg" src="" alt="ID Document" class="w-full h-auto mt-2 border">
                </div>
                <div class="w-1/2">
                    <h4 class="font-semibold mb-2">Vehicle Verification Document</h4>
                    <img id="vehicleVerificationImg" src="" alt="Vehicle Verification Document" class="w-full h-auto mt-2 border">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">Cancel</button>
                <button type="submit" name="updateCar" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>




<!-- Modal Confirm Delete -->
<div id="deleteCarModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-80">
        <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
        <p>Are you sure you want to delete this car?</p>
        <div class="flex justify-end mt-4">
            <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">Cancel</button>
            <button onclick="deleteCar()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Delete</button>
        </div>
    </div>
</div>


<!-- Modal for Car Info -->
<div id="carInfoModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-4xl">
        <h3 class="text-lg font-semibold text-gray-800">Car Details</h3>
        
        <!-- Car Details Table -->
        <div id="carDetails" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <!-- Name, Last Name, Student ID -->
            <div class="col-span-1">
                <p><strong>Name:</strong> <span id="carName"></span></p>
                <p><strong>Last Name:</strong> <span id="carLastName"></span></p>
                <p><strong>Student ID:</strong> <span id="carStudentID"></span></p>
            </div>
            <!-- Division, Province, Running Number -->
            <div class="col-span-1">
                <p><strong>Division:</strong> <span id="carDivision"></span></p>
                <p><strong>Province:</strong> <span id="carProvince"></span></p>
                <p><strong>Running Number:</strong> <span id="carRunningNumber"></span></p>
            </div>
            <!-- Grade, Room, Car Band -->
            <div class="col-span-1">
                <p><strong>Grade:</strong> <span id="carGrade"></span></p>
                <p><strong>Room:</strong> <span id="carRoom"></span></p>
                <p><strong>Car Band:</strong> <span id="carBand"></span></p>
            </div>
            <!-- Color, Vehicle Type, VIN -->
            <div class="col-span-1">
                <p><strong>Color:</strong> <span id="carColor"></span></p>
                <p><strong>Vehicle Type:</strong> <span id="carType"></span></p>
                <p><strong>VIN:</strong> <span id="carVIN"></span></p>
            </div>
            <!-- Engine Number, Year of Manufacture, Vehicle Weight -->
            <div class="col-span-1">
                <p><strong>Engine Number:</strong> <span id="carEngineNumber"></span></p>
                <p><strong>Year of Manufacture:</strong> <span id="carYear"></span></p>
                <p><strong>Vehicle Weight:</strong> <span id="carWeight"></span></p>
            </div>
            <!-- Fuel Type, Engine CC, Created Timestamp -->
            <div class="col-span-1">
                <p><strong>Fuel Type:</strong> <span id="carFuel"></span></p>
                <p><strong>Engine CC:</strong> <span id="carCC"></span></p>
                <p><strong>Created At:</strong> <span id="carTimestamp"></span></p>
            </div>
        </div>
        
        <!-- Documents Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div>
                <h4 class="font-semibold mb-2">ID Document</h4>
                <img id="idDocumentImg" src="" alt="ID Document" class="w-full h-auto mt-2 border">
            </div>
            <div>
                <h4 class="font-semibold mb-2">Vehicle Verification Document</h4>
                <img id="vehicleVerificationImg" src="" alt="Vehicle Verification Document" class="w-full h-auto mt-2 border">
            </div>
        </div>
        
        <!-- Close Button -->
        <button onclick="closeCarInfoModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Close</button>
    </div>
</div>

        </div>
    </div>
</div>

    </div>
</div>

<!-- Cookie Consent Banner -->
<div id="cookieConsent" class="fixed bottom-0 inset-x-0 bg-gray-800 text-white py-4 px-6 flex justify-between items-center shadow-lg">
    <div>
        <p id="cookieText" class="text-sm lang-text" data-en="This website uses cookies to enhance the user experience. By using our site, you consent to our use of cookies." data-th="เว็บไซต์นี้ใช้คุกกี้เพื่อพัฒนาประสบการณ์การใช้งานของคุณ การใช้งานเว็บไซต์แสดงว่าคุณยินยอมให้เราใช้คุกกี้">
            This website uses cookies to enhance the user experience. By using our site, you consent to our use of cookies.
        </p>
    </div>
    <button id="acceptButton" onclick="acceptCookies()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 lang-text" data-en="Accept" data-th="ยอมรับ">
        Accept
    </button>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmation" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="flex p-4 mb-4 rounded-xl text-sm bg-amber-50 max-w-md w-full" role="alert">
        <div class="mr-2">
            <svg class="flex-shrink-0 w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.0043 13.3333V9.16663M9.99984 6.66663H10.0073M9.99984 18.3333C5.39746 18.3333 1.6665 14.6023 1.6665 9.99996C1.6665 5.39759 5.39746 1.66663 9.99984 1.66663C14.6022 1.66663 18.3332 5.39759 18.3332 9.99996C18.3332 14.6023 14.6022 18.3333 9.99984 18.3333Z" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
        <div class="block">
            <h3 class="text-amber-500 font-normal">
                <span class="font-semibold mr-1">Warning</span> Are you sure you want to delete this user?
            </h3>
            <div class="flex items-center gap-6 mt-4">
                <button onclick="confirmDeleteUser()" class="font-semibold text-gray-900 transition-all duration-500 hover:text-amber-500">Yes</button>
                <button onclick="closeDeleteConfirmation()" class="font-semibold text-gray-900 transition-all duration-500 hover:text-amber-500">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Confirmation Modal -->
<div id="confirmEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="flex p-4 mb-4 rounded-xl text-sm bg-amber-50 max-w-md w-full" role="alert">
        <div class="mr-2">
            <svg class="flex-shrink-0 w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.0043 13.3333V9.16663M9.99984 6.66663H10.0073M9.99984 18.3333C5.39746 18.3333 1.6665 14.6023 1.6665 9.99996C1.6665 5.39759 5.39746 1.66663 9.99984 1.66663C14.6022 1.66663 18.3332 5.39759 18.3332 9.99996C18.3332 14.6023 14.6022 18.3333 9.99984 18.3333Z" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
        <div class="block">
            <h3 class="text-amber-500 font-normal">
                <span class="font-semibold mr-1">Warning</span> Are you sure you want to save changes to this user?
            </h3>
            <div class="flex items-center gap-6 mt-4">
                <button onclick="confirmEditUser()" class="font-semibold text-gray-900 transition-all duration-500 hover:text-amber-500">Yes</button>
                <button onclick="closeConfirmEdit()" class="font-semibold text-gray-900 transition-all duration-500 hover:text-amber-500">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Alert -->
<div id="logoutAlert" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-xs w-full">
        <h3 class="text-lg font-semibold text-gray-800 lang-text" data-en="Do you want to log out?" data-th="คุณต้องการออกจากระบบหรือไม่?">Do you want to log out?</h3>
        <div class="flex mt-4 space-x-2">
            <button onclick="confirmLogout()" class="text-white bg-blue-800 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium rounded-lg text-xs px-3 py-1.5 text-center inline-flex items-center">
                <svg class="me-2 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                    <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                </svg>
                Confirm
            </button>
            <button onclick="closeLogoutAlert()" class="text-blue-800 bg-transparent border border-blue-800 hover:bg-blue-900 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium rounded-lg text-xs px-3 py-1.5">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Container สำหรับการแจ้งเตือน -->
<div id="alertOverlay" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm z-50">
    <div id="alertMessage" class="bg-white p-6 rounded-lg shadow-lg max-w-xs w-full text-center text-gray-800 font-medium">
        <!-- ข้อความแจ้งเตือนการ Logout จะถูกแทรกในนี้ผ่าน JavaScript -->
    </div>
</div>

<script>
function openLogoutAlert() {
    document.getElementById("logoutAlert").classList.remove("hidden"); // แสดงกล่องยืนยัน Logout
}

function closeLogoutAlert() {
    document.getElementById("logoutAlert").classList.add("hidden"); // ซ่อนกล่องยืนยัน Logout
}

function confirmLogout() {
    fetch('process_logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const alertOverlay = document.getElementById("alertOverlay");
        const alertMessage = document.getElementById("alertMessage");

        if (data.status === 'success') {
            alertMessage.innerHTML = `<div class='p-4 mb-4 text-green-600'>
                                        <span class='font-semibold'>${data.message}</span>
                                      </div>`;
            alertOverlay.classList.remove("hidden"); // แสดงกล่องแจ้งเตือน
            setTimeout(() => {
                window.location.href = "login.php"; // เปลี่ยนหน้าไปยัง login หลังจากแสดงข้อความสักพัก
            }, 2000);
        } else {
            alertMessage.innerHTML = `<div class='p-4 mb-4 text-red-600'>
                                        <span class='font-semibold'>Error:</span> ${data.message}
                                      </div>`;
            alertOverlay.classList.remove("hidden");
        }
    })
    .catch(error => {
        console.error("Error logging out:", error);
    });
}

</script>

</body>
</html>

    <!-- PHP Functions -->
    <?php
    function getTotalCars() {
        return 120;
    }

    function getActiveCarstodays() {
        return 45;
    }

    function getLeaveCarstoday() {
        return "$2,500";
    }
    ?>

    <!-- JavaScript for Toggle Sidebar and Language Dropdown -->
    <script>
        const toggleSidebarButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        toggleSidebarButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-expanded');
            sidebar.classList.toggle('sidebar-collapsed');
            content.classList.toggle('content-expanded');
            content.classList.toggle('content-full');
        });

    </script>

    <script>
        function openModal() {
    document.getElementById("crud-modal").classList.remove("hidden");
}

function closeModal() {
    document.getElementById("crud-modal").classList.add("hidden");
}
    </script>

    <script>
const userManagementToggle = document.getElementById('userManagementToggle');
const userDropdown = document.getElementById('userDropdown');
const dropdownArrow = document.getElementById('dropdownArrow');
const userManagementText = document.getElementById('userManagementText');

userManagementToggle.addEventListener('click', () => {
    // แสดงหรือซ่อนดรอปดาวน์พร้อมแอนิเมชัน
    if (userDropdown.classList.contains('hide-dropdown')) {
        userDropdown.classList.remove('hide-dropdown');
        userDropdown.classList.add('show-dropdown');
        userManagementToggle.classList.add('bg-gray-700'); // เปลี่ยนพื้นหลังเมื่อเปิด
    } else {
        userDropdown.classList.remove('show-dropdown');
        userDropdown.classList.add('hide-dropdown');
        userManagementToggle.classList.remove('bg-gray-700'); // เอาพื้นหลังออกเมื่อปิด
    }

    // หมุนไอคอนลูกศร
    dropdownArrow.classList.toggle('rotate-180');

    // ทำตัวหนาสำหรับ User Management
    userManagementText.classList.toggle('font-bold');
});

    </script>

    <script>
        const carManagementToggle = document.getElementById('carManagementToggle');
const carDropdown = document.getElementById('carDropdown');
const carDropdownArrow = document.getElementById('carDropdownArrow');

carManagementToggle.addEventListener('click', () => {
    if (carDropdown.classList.contains('hide-dropdown')) {
        carDropdown.classList.remove('hide-dropdown');
        carDropdown.classList.add('show-dropdown');
        carManagementToggle.classList.add('bg-gray-700'); // เปลี่ยนพื้นหลังเมื่อเปิด
    } else {
        carDropdown.classList.remove('show-dropdown');
        carDropdown.classList.add('hide-dropdown');
        carManagementToggle.classList.remove('bg-gray-700'); // เอาพื้นหลังออกเมื่อปิด
    }

    // หมุนไอคอนลูกศร
    carDropdownArrow.classList.toggle('rotate-180');
});

    </script>

<script>
    const currentLanguageFlag = document.getElementById('currentLanguageFlag');

    // Function to set language for elements with class "lang-text"
    function setLanguage(lang) {
        document.querySelectorAll('.lang-text').forEach((element) => {
            element.textContent = element.getAttribute(`data-${lang}`);
        });
    }

    // Language toggle button click event
    document.getElementById('languageToggle').addEventListener('click', () => {
        const currentLang = localStorage.getItem('language') || 'th';
        const newLang = currentLang === 'en' ? 'th' : 'en';

        localStorage.setItem('language', newLang);
        setLanguage(newLang);

        // Update flag for language
        currentLanguageFlag.src = newLang === 'en' ? "assets/flags/english-flag.png" : "assets/flags/thai-flag.png";
    });

    window.addEventListener('load', () => {
        if (localStorage.getItem('cookieConsent') === 'true') {
            document.getElementById('cookieConsent').style.display = 'none';
        }
    });

    // Function to accept cookies and hide the banner
    function acceptCookies() {
        localStorage.setItem('cookieConsent', 'true');
        document.getElementById('cookieConsent').style.display = 'none';
    }

    // Set initial language based on stored language
    window.addEventListener('load', () => {
        const savedLang = localStorage.getItem('language') || 'en';
        setLanguage(savedLang);

        // Set initial flag icon based on saved language
        currentLanguageFlag.src = savedLang === 'en' ? "assets/flags/english-flag.png" : "assets/flags/thai-flag.png";
    });
</script>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.style.fontFamily = "'Prompt', sans-serif";
        });
    </script>


<script>
    function showCarInfo(carId) {
        fetch(`get_car_details.php?car_id=${carId}`)
            .then(response => response.json())
            .then(data => {
                const details = `
                    <p><strong>Grade:</strong> ${data.Grade}</p>
                    <p><strong>Room:</strong> ${data.Room}</p>
                    <p><strong>Car Band:</strong> ${data.Car_Band}</p>
                    <p><strong>Color:</strong> ${data.Color_of_Vehicle}</p>
                    <p><strong>Vehicle Type:</strong> ${data.Vehicle_Type}</p>
                    <p><strong>VIN:</strong> ${data.Vehicle_Identification_Number}</p>
                    <p><strong>Engine Number:</strong> ${data.Engine_Number}</p>
                    <p><strong>Year:</strong> ${data.Year_of_Manufacture}</p>
                    <p><strong>Weight:</strong> ${data.Vehicle_Weight}</p>
                    <p><strong>Fuel Type:</strong> ${data.Fuel_Type}</p>
                    <p><strong>Engine CC:</strong> ${data.Engine_CC}</p>
                    <p><strong>Created:</strong> ${data.Creates_Timestamp}</p>
                    <img src="${data.Identification_Card_Document}" alt="ID Document" class="mt-2"/>
                    <img src="${data.Vehicle_Verification_Document}" alt="Verification Document" class="mt-2"/>
                `;
                document.getElementById('carDetails').innerHTML = details;
                document.getElementById('carInfoModal').classList.remove('hidden');
            });
    }

    function closeCarInfoModal() {
        document.getElementById('carInfoModal').classList.add('hidden');
    }
</script>

<script>
function editCar(carId) {
    fetch(`get_car_details.php?car_id=${carId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editCarId').value = carId;
            document.getElementById('editGrade').value = data.Grade;
            document.getElementById('editRoom').value = data.Room;
            document.getElementById('editCarBand').value = data.Car_Band;
            document.getElementById('editCarColor').value = data.Color_of_Vehicle;
            document.getElementById('editVehicleType').value = data.Vehicle_Type;
            document.getElementById('editVIN').value = data.Vehicle_Identification_Number;
            document.getElementById('editEngineNumber').value = data.Engine_Number;
            document.getElementById('editYear').value = data.Year_of_Manufacture;
            document.getElementById('editWeight').value = data.Vehicle_Weight;
            document.getElementById('editFuel').value = data.Fuel_Type;
            document.getElementById('editEngineCC').value = data.Engine_CC;

            // โหลดรูปภาพ
            document.getElementById('identificationCardImg').src = `path/to/your/uploads/${data.Identification_Card_Document}`;
            document.getElementById('vehicleVerificationImg').src = `path/to/your/uploads/${data.Vehicle_Verification_Document}`;
            
            document.getElementById('editCarModal').classList.remove('hidden');
        });
}

function closeEditModal() {
    document.getElementById('editCarModal').classList.add('hidden');
}

</script>

<script>
    let carIdToDelete = null;

function confirmDelete(carId) {
    carIdToDelete = carId;
    document.getElementById("deleteCarModal").classList.remove("hidden");
}

function closeDeleteModal() {
    carIdToDelete = null;
    document.getElementById("deleteCarModal").classList.add("hidden");
}

function deleteCar() {
    if (!carIdToDelete) return;

    fetch('delete_car.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ car_id: carIdToDelete })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert("Error deleting car");
        }
        closeDeleteModal();
    })
    .catch(error => console.error("Error:", error));
}

</script>

</body>
</html>
