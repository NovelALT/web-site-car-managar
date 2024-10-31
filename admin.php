<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // กลับไปหน้า login หากยังไม่ได้ล็อกอิน
    exit();
}

// นำเข้าและเชื่อมต่อกับฐานข้อมูลก่อนใช้ฟังก์ชัน
include 'db_connection_userroles.php';

function getTotalUsers() {
    global $conn;

    // SQL นับจำนวนผู้ใช้ในตาราง Users
    $sql = "SELECT COUNT(*) AS total_users FROM Users";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total_users'];
    } else {
        return 0;
    }
}

function getTotalAdmins() {
    global $conn;

    // SQL นับจำนวนผู้ใช้ในตาราง Users ที่มี status เป็น 'Admin'
    $sql = "SELECT COUNT(*) AS total_admins FROM Users WHERE status = 'Admin'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total_admins'];
    } else {
        return 0;
    }
}

function getTotalManagers() {
    global $conn;

    // SQL นับจำนวนผู้ใช้ในตาราง Users ที่มี status เป็น 'Manager'
    $sql = "SELECT COUNT(*) AS total_managers FROM Users WHERE status = 'Manager'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total_managers'];
    } else {
        return 0;
    }
}

// เรียกใช้ฟังก์ชันเพื่อดึงจำนวนผู้ใช้, ผู้ดูแลระบบ และผู้จัดการทั้งหมด
$total_users = getTotalUsers();
$total_admins = getTotalAdmins();
$total_managers = getTotalManagers();
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
                <a href="admin.php" class="flex items-center py-2.5 px-4 rounded hover:bg-gray-700 bg-gray-700">
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
        <h2 class="text-3xl font-bold text-gray-700 lang-text" data-en="Dashboard" data-th="แดชบอร์ด">Dashboard</h2>
        <p class="text-gray-600 lang-text" data-en="Welcome to the Admin Panel" data-th="ยินดีต้อนรับสู่แผงควบคุมของผู้ดูแลระบบ">Welcome to the Admin Panel</p>

            <!-- Statistics Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
                    <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Cars" data-th="จำนวนรถทั้งหมด">Total Cars</h3>
                    <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                    <p class="text-gray-500 text-2xl"><?php echo getTotalCars(); ?></p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg></a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-green-200 transition duration-300 ease-in-out">
                <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Active Cars Today" data-th="รถที่ใช้งานวันนี้">Active Cars Today</h3>
                <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                    <p class="text-gray-500 text-2xl"><?php echo getActiveCarstodays(); ?></p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg></a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-red-200 transition duration-300 ease-in-out">
                <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Leave Cars Today" data-th="รถที่ออกวันนี้">Leave Cars Today</h3>
                <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                    <p class="text-gray-500 text-2xl"><?php echo getLeaveCarstoday(); ?></p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg></a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
                <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Users" data-th="ผู้ใช้ทั้งหมด">Total Users</h3>
                <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                <p class="text-gray-500 text-2xl"><?php echo getTotalUsers() . " คน"; ?></p> <!-- เรียกใช้ฟังก์ชัน -->
                <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                    </a>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
                <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Admins" data-th="ผู้ดูแลระบบทั้งหมด">Total Admins</h3>
                <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                <p class="text-gray-500 text-2xl"><?php echo getTotalAdmins() . " คน"; ?></p> 
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg></a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
                <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Managers" data-th="ผู้จัดการทั้งหมด">Total Managers</h3>
                <p class="text-gray-600">Data date at <?php echo date("Y-m-d H:i:s"); ?></p>
                <p class="text-gray-500 text-2xl"><?php echo getTotalManagers() . " คน"; ?></p> 
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 lang-text" data-en="Read more" data-th="อ่านเพิ่มเติม">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg></a>
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
// สำหรับ Car Management
const carManagementToggle = document.getElementById('carManagementToggle');
const carDropdown = document.getElementById('carDropdown');
const carDropdownArrow = document.getElementById('carDropdownArrow');

if (carManagementToggle && carDropdown && carDropdownArrow) {
    carManagementToggle.addEventListener('click', () => {
        if (carDropdown.classList.contains('hide-dropdown')) {
            carDropdown.classList.remove('hide-dropdown');
            carDropdown.classList.add('show-dropdown');
            carManagementToggle.classList.add('bg-gray-700');
        } else {
            carDropdown.classList.remove('show-dropdown');
            carDropdown.classList.add('hide-dropdown');
            carManagementToggle.classList.remove('bg-gray-700');
        }
        carDropdownArrow.classList.toggle('rotate-180');
    });
}
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
        document.addEventListener('DOMContentLoaded', function () {
            document.body.style.fontFamily = "'Prompt', sans-serif";
        });
</script>

</body>
</html>
