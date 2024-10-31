<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include 'db_connection_userroles.php';

function getUserLogs() {
    global $conn;

    // ดึงข้อมูลการกระทำของผู้ใช้จาก UserRoles โดยใช้ JOIN กับ Users
    $sql = "SELECT UserRoles.id, 
                   UserRoles.action, 
                   UserRoles.action_time, 
                   Users.name, 
                   Users.lastname, 
                   Users.status, 
                   Users.role 
            FROM UserRoles 
            JOIN Users ON Users.id = UserRoles.user_id 
            ORDER BY UserRoles.id DESC"; // เรียงตาม id ล่าสุดก่อน
    $result = $conn->query($sql);

    $logs = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
    }
    return $logs;
}

// ดึงข้อมูล logs จากฐานข้อมูล
$logs = getUserLogs();
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
                    <a href="user_reports.php" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 bg-gray-700 rounded">User Reports</a>
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
    <h2 class="text-3xl font-bold text-gray-700 lang-text" data-en="User Reports" data-th="รายงานผู้ใช้">User Reports</h2>
    <p class="text-gray-600 lang-text" data-en="Track all actions performed on user accounts, including additions, deletions, and edits." data-th="ติดตามการกระทำทั้งหมดที่ดำเนินการกับบัญชีผู้ใช้ รวมถึงการเพิ่ม การลบ และการแก้ไข">Track all actions performed on user accounts, including additions, deletions, and edits.</p>
    <!-- Reports Table Section -->
    <div class="mt-8 overflow-x-auto">
        <table id="reportsTable" class="min-w-full bg-white rounded-lg shadow-md">

        <!-- Search and Rows Dropdown -->
    <div class="flex items-center mt-4 mb-4">
        <!-- Search Input -->
        <label for="searchInput" class="mr-2 text-gray-700">Search:</label>
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by ID, Action, Username, Role, Status, Timestamp" class="p-2 border rounded flex-1">

        <p>.  |  .</p>

    <!-- Dropdown for Rows Per Page -->
        <div class="mt-4 mb-4">
        <label for="rowsPerPage" class="text-gray-700">Rows per page:</label>
        <select id="rowsPerPage" class="ml-2 p-2 border rounded" onchange="updateRowsPerPage()">
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50</option>
            <option value="60">60</option>
            <option value="70">70</option>
            <option value="80">80</option>
            <option value="90">90</option>
            <option value="100">100</option>
            <option value="150">150</option>
        </select>
    </div>
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b-2 border-gray-200">ID</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Action</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Username</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Role</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Status</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Timestamp</th>
                    <th class="py-2 px-4 border-b-2 border-gray-200">Details</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP Loop -->
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?php echo $log['id']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $log['action']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $log['name'] . ' ' . $log['lastname']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $log['role']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $log['status']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $log['action_time']; ?></td>
                        <td class="py-2 px-4 border-b">
                            <button onclick="viewDetails(<?php echo $log['id']; ?>)" class="bg-blue-500 text-white py-1 px-3 rounded-lg hover:bg-blue-600">View</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex justify-between items-center mt-4">
        <button type="button" onclick="prevPage()" class="py-2.5 px-4 bg-indigo-500 text-white rounded-full hover:bg-indigo-700">Previous</button>
        
        <!-- Page Numbers Display -->
        <div class="text-gray-700">
            Page 
            <select id="pageSelect" onchange="goToPage(this.value)" class="border rounded p-2">
                <!-- Page options will be dynamically generated -->
            </select>
            of <span id="totalPages"></span>
        </div>

        <button type="button" onclick="nextPage()" class="py-2.5 px-4 bg-indigo-500 text-white rounded-full hover:bg-indigo-700">Next</button>
    </div>
</div>

<!-- Modal for Viewing Details -->
<div id="details-modal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50 backdrop-blur-sm">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Action Details</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" onclick="closeDetailsModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <p id="action-details" class="text-gray-700"></p>
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

<!-- Main modal -->
<div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Create New Users
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="closeModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <form class="p-4 md:p-5">
                <div class="grid gap-2 mb-4 grid-cols-2">
                    <div class="col-span-2 sm:col-span-1">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type name" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lastname</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type Lastname" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID Student/Teacher</label>
                        <input type="number" name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="0000" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Roles</label>
                        <select id="roles" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Select Roles</option>
                            <option value="Admin">Student</option>
                            <option value="status">Teacher</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Select status</option>
                            <option value="Admin">Admin</option>
                            <option value="status">Manager</option>
                            <?php 
                            echo $user_id
                            ?>
                        </select>
                    </div>
                </div>
                <center>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Add User
                </button>
                </center>
            </form>
        </div>
    </div>
</div>

<!-- Main modal -->
<div id="crud-modals" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Create New Cars
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="closeModals()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <form class="p-4 md:p-5">
                <div class="grid gap-2 mb-4 grid-cols-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชื่อ</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="นายสมร" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">นามสกุล</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="ใหญ่มาก" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">รหัสนักเรียน</label>
                        <input type="number" name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="0000" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="grade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชั้น</label>
                        <input type="number" name="grade" id="grade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="3" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="room" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ห้อง</label>
                        <input type="number" name="room" id="room" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="11" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="division" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมายเลขแผนก</label>
                        <input type="text" name="division" id="division" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="1กค" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="province" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">จังหวัด</label>
                        <input type="text" name="province" id="province" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="พิษณุโลก" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="running_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมายเลขวิ่ง</label>
                        <input type="text" name="running_number" id="running_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="2456" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="vin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมายเลขตัวรถ</label>
                        <input type="text" name="vin" id="vin" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="JHMCM56557C004637" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="engine_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">หมายเลขตัวเครื่อง</label>
                        <input type="text" name="engine_number" id="engine_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="4G63T019765" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="body_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">สีถัง</label>
                        <select id="body_color" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">เลือกสีถัง</option>
                            <option value="ขาว">ขาว</option>
                            <option value="แดง">แดง</option>
                            <option value="น้ำเงิน">น้ำเงิน</option>
                            <option value="เขียว">เขียว</option>
                            <option value="เทา">เทา</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="type_of_car" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ประเภทรถ</label>
                        <select id="type_of_car" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">เลือกประเภทรถ</option>
                            <option value="White">รถจักรยานยนต์</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="body_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">รุ่นรถ</label>
                        <select id="body_color" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">เลือกรุ่นรถ</option>
                            <option value="Honda">Honda</option>
                            <option value="Yamaha">Yamaha</option>
                            <option value="Kawasaki">Kawasaki</option>
                            <option value="Ducati">Ducati</option>
                            <option value="Harley-Davidson">Harley-Davidson</option>
                            <option value="Suzuki">Suzuki</option>
                            <option value="KTM">KTM</option>
                            <option value="Aprilia">Aprilia</option>
                            <option value="Husqvarna">Husqvarna</option>
                            <option value="Indian_Motorcycle">Indian Motorcycle</option>
                            <option value="MV_Agusta">MV Agusta</option>
                            <option value="Zero_Motorcycles">Zero Motorcycles</option>
                            <option value="Benelli">Benelli</option>
                            <option value="CFMoto">CFMoto</option>
                            <option value="Bajaj">Bajaj</option>
                            <option value="SYM">SYM</option>


                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="vehicle_weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">น้ำหนักรถ</label>
                        <input type="text" name="vehicle_weight" id="vehicle_weight" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="1-1,200 kg" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="engine_capacity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">เครื่องยนต์ (cc)</label>
                        <input type="text" name="engine_capacity" id="engine_capacity" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="50-1500 cc" required="">
                    </div>
                </div>
                <center>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Add Cars
                </button>
                </center>
            </form>
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

</body>
</html>
