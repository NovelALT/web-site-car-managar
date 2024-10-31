<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include 'db_connection_cars.php';

function getCarReports() {
    global $conn;

    // ดึงข้อมูลจาก Car_Reports และเชื่อมกับ Manage_Cars เพื่อแสดงข้อมูลชื่อรถ และรายละเอียดเพิ่มเติม
    $sql = "SELECT Car_Reports.Report_ID, 
                   Car_Reports.License_Plate, 
                   Car_Reports.Status, 
                   Car_Reports.Entry_Exit_Time, 
                   Manage_Cars.Name AS CarName,
                   Manage_Cars.Vehicle_Type,
                   Manage_Cars.Color_of_Vehicle
            FROM Car_Reports 
            JOIN Manage_Cars ON Manage_Cars.ID = Car_Reports.Car_ID 
            ORDER BY Car_Reports.Report_ID DESC"; // เรียงตาม ID ล่าสุดก่อน

    $result = $conn->query($sql);

    $reports = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
    }
    return $reports;
}

// อัพเดตข้อมูลรายงาน
if (isset($_POST['updateReport'])) {
    $reportId = $_POST['report_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE Car_Reports SET Status = ? WHERE Report_ID = ?");
    $stmt->bind_param("si", $status, $reportId);
    $stmt->execute();
    $stmt->close();
    header("Location: car_reports.php"); // รีเฟรชหน้า
    exit();
}

// ลบรายงาน
if (isset($_POST['deleteReport'])) {
    $reportId = $_POST['report_id'];
    $stmt = $conn->prepare("DELETE FROM Car_Reports WHERE Report_ID = ?");
    $stmt->bind_param("i", $reportId);
    $stmt->execute();
    $stmt->close();
    header("Location: car_reports.php"); // รีเฟรชหน้า
    exit();
}

if (!function_exists('getCarsInGarage')) {
    function getCarsInGarage() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as total FROM Car_Reports WHERE Status = 'In Garage'");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}

if (!function_exists('getCarsOutGarage')) {
    function getCarsOutGarage() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as total FROM Car_Reports WHERE Status = 'Out Garage'");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}

$totalCars = getTotalCars();
$carsInGarage = getCarsInGarage();
$carsOutGarage = getCarsOutGarage();
$reports = getCarReports();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports</title>
    <link href="../node_modules/tailwindcss/tailwind.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<style>
    body {
    font-family: 'Prompt', sans-serif;
    }

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
                    <span class="menu-text sidebar-link">Dashboard</span>
                </a>
                <div class="flex items-center justify-between py-2.5 px-4 rounded hover:bg-gray-700 cursor-pointer" id="userManagementToggle">
                <div class="flex items-center">
                    <img src="assets/icons/user.png" alt="User Icon" class="w-5 h-5 mr-3">
                    <span class="menu-text">User Management</span>
                </div>
                <svg id="dropdownArrow" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                </div>
                <div id="userDropdown" class="hidden pl-12 mt-2 space-y-2">
                    <a href="manage_users.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">Manage Roles</a>
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
                    <a href="car_reports.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700  bg-gray-700 rounded">Car Reports</a>
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
        <p class="text-gray-600">Data date at <?php echo date('Y-m-d'); ?></p>
        <p class="text-gray-500 text-2xl"><?php echo $totalCars; ?> คัน</p> <!-- แสดงจำนวนรถทั้งหมด -->
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-green-200 transition duration-300 ease-in-out">
        <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total In Garage" data-th="จำนวนรถที่อยู่ในโรง">Total Pass</h3>
        <p class="text-gray-600">Data date at <?php echo date('Y-m-d'); ?></p>
        <p class="text-gray-500 text-2xl"><?php echo $carsInGarage; ?> คัน</p> <!-- แสดงจำนวนรถที่อยู่ในโรง -->
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-yellow-200 transition duration-300 ease-in-out">
        <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Out Garage" data-th="จำนวนที่ออกจากในโรง">Total Fail</h3>
        <p class="text-gray-600">Data date at <?php echo date('Y-m-d'); ?></p>
        <p class="text-gray-500 text-2xl"><?php echo $carsOutGarage; ?> คัน</p> <!-- แสดงจำนวนที่ไม่อยู่ในโรง -->
    </div>
</div>

    <!-- User Role Management Section -->
    <br>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg ml-auto">
    <div class="flex justify-end">
        <div id="content" class="flex-1 p-8 transition-all duration-300 content-expanded">
            
            <div class="mt-8 overflow-x-auto">
                <table id="reportsTable" class="min-w-full bg-white rounded-lg shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Report ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">License Plate</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Car Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Type</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Color</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Status</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Entry/Exit Time</th>
                            <th class="py-2 px-4 border-b-2 border-gray-200">Actions</th> <!-- คอลัมน์ Actions -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?php echo $report['Report_ID']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo $report['License_Plate']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo $report['CarName']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo $report['Vehicle_Type']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo $report['Color_of_Vehicle']; ?></td>
                                <td class="py-2 px-4 border-b">
                                    <span class="inline-flex items-center 
                                        <?php echo $report['Status'] === 'In Garage' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'; ?> 
                                        text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        
                                        <span class="w-2 h-2 me-1 <?php echo $report['Status'] === 'In Garage' ? 'bg-green-500' : 'bg-red-500'; ?> rounded-full"></span>
                                        <?php echo $report['Status'] === 'In Garage' ? 'In Garage' : 'Out Garage'; ?>
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b"><?php echo $report['Entry_Exit_Time']; ?></td>
                                <!-- ปุ่ม Actions -->
                                <td class="py-2 px-4 border-b text-center">
                                <form method="post" class="inline-block">
                                <input type="hidden" name="report_id" value="<?php echo $report['Report_ID']; ?>">
                                <button type="button" onclick="showEditModal(<?php echo $report['Report_ID']; ?>, '<?php echo $report['Status']; ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded-lg hover:bg-yellow-600">Edit</button>
                                <button type="button" onclick="confirmDelete(<?php echo $report['Report_ID']; ?>)" class="bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-600">Delete</button>
                                </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

<!-- Modal Edit Status Gra -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-80">
        <h3 class="text-lg font-semibold text-gray-800">Edit Report Status</h3>
        <form method="post" class="mt-4">
            <input type="hidden" name="report_id" id="editReportId">
            <label for="status" class="block mb-2 text-sm text-gray-700">Status</label>
            <select name="status" id="editStatus" class="border border-gray-300 rounded-lg p-2 w-full">
                <option value="In Garage">In Garage</option>
                <option value="Out Garage">Out Garage</option>
            </select>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">Cancel</button>
                <button type="submit" name="updateReport" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal ยืนยันการลบ -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50 backdrop-blur-sm">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Confirm Delete</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:bg-gray-200 rounded-lg text-sm w-8 h-8">
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <p class="text-gray-700">Are you sure you want to delete this report?</p>
                <div class="flex justify-end mt-4 space-x-2">
                    <button onclick="deleteReport()" class="bg-red-500 text-white py-1 px-3 rounded-lg hover:bg-red-600">Delete</button>
                    <button onclick="closeDeleteModal()" class="bg-gray-200 text-gray-700 py-1 px-3 rounded-lg hover:bg-gray-300">Cancel</button>
                </div>
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

<script>
    let rowsPerPage = 10;
    let currentPage = 1;
    const table = document.getElementById("reportsTable");
    const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    function displayPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = "none";
        }

        for (let i = start; i < end && i < rows.length; i++) {
            rows[i].style.display = "";
        }

        document.getElementById("totalPages").innerText = Math.ceil(rows.length / rowsPerPage);
        document.getElementById("pageSelect").value = page;
    }

    function updatePagination() {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        const pageSelect = document.getElementById("pageSelect");

        // Clear and re-populate page options
        pageSelect.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) {
            const option = document.createElement("option");
            option.value = i;
            option.textContent = i;
            pageSelect.appendChild(option);
        }

        document.getElementById("totalPages").innerText = totalPages;
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayPage(currentPage);
        }
    }

    function nextPage() {
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
            currentPage++;
            displayPage(currentPage);
        }
    }

    function updateRowsPerPage() {
        rowsPerPage = parseInt(document.getElementById("rowsPerPage").value);
        currentPage = 1;
        updatePagination();
        displayPage(currentPage);
    }

    function searchTable() {
        const filter = document.getElementById("searchInput").value.toUpperCase();
        let visibleRows = 0;

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName("td");
            let match = false;

            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? "" : "none";
            if (match) visibleRows++;
        }

        if (filter === "") {
            rowsPerPage = 10;
            document.getElementById("rowsPerPage").value = "10";
        } else {
            rowsPerPage = visibleRows;
        }

        updatePagination();
        currentPage = 1;
        displayPage(currentPage);
    }

    function goToPage(page) {
        currentPage = parseInt(page);
        displayPage(currentPage);
    }

    // Initialize table display and pagination
    updatePagination();
    displayPage(currentPage);
</script>

<script>
    // Function to open the details modal
    function viewDetails(id) {
        document.getElementById('action-details').innerText = "Details for action ID: " + id;
        document.getElementById('details-modal').classList.remove('hidden');
    }

    // Function to close the details modal
    function closeDetailsModal() {
        document.getElementById('details-modal').classList.add('hidden');
    }
</script>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.style.fontFamily = "'Prompt', sans-serif";
        });
    </script>

    <!-- PHP Functions -->
    <?php
    function getTotalCars() {
        return 120;
    }

    function getActiveCarstodays() {
        return 45;
    }

    function getLeaveCarstoday() {
        return 20;
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
    function openModals() {
    document.getElementById("crud-modals").classList.remove("hidden");
    }

    function closeModals() {
        document.getElementById("crud-modals").classList.add("hidden");
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
    // ปิดการใช้งานการคลิกขวา
    document.addEventListener('contextmenu', event => event.preventDefault());

    // ปิดการใช้งานการคัดลอก ตัด และวางโดยใช้แป้นพิมพ์
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && (event.key === 'c' || event.key === 'v' || event.key === 'x' || event.key === 'a')) {
            event.preventDefault();
        }
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
        const currentLang = localStorage.getItem('language') || 'en';
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
// เปิด Modal แก้ไข
function showEditModal(reportId, status) {
    document.getElementById('editReportId').value = reportId;
    document.getElementById('editStatus').value = status;
    document.getElementById('editModal').classList.remove('hidden');
}

// ปิด Modal แก้ไข
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<script>
    let reportIdToDelete = null;

function confirmDelete(reportId) {
    reportIdToDelete = reportId;  // กำหนดค่า Report ID ที่จะลบ
    document.getElementById("delete-modal").classList.remove("hidden");  // แสดง Modal การลบ
}

function closeDeleteModal() {
    reportIdToDelete = null;  // ล้างค่า Report ID
    document.getElementById("delete-modal").classList.add("hidden");  // ซ่อน Modal
}

function deleteReport() {
    if (!reportIdToDelete) return;

    // ส่งคำขอเพื่อดำเนินการลบที่ delete_report.php
    fetch('delete_report.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reportId: reportIdToDelete })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();  // โหลดหน้าใหม่หลังลบเสร็จ
        } else {
            alert("Error deleting report");
        }
        closeDeleteModal();  // ปิด Modal
    })
    .catch(error => console.error("Error:", error));
}
</script>

</body>
</html>
