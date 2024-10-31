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
    <h2 class="text-3xl font-bold text-gray-700 lang-text" data-en="Dashboard" data-th="แดชบอร์ด">Dashboard</h2>
    <p class="text-gray-600 lang-text" data-en="Welcome to the Admin Panel" data-th="ยินดีต้อนรับสู่แผงควบคุมของผู้ดูแลระบบ">Welcome to the Admin Panel</p>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white p-6 rounded-lg shadow-md hover:bg-blue-200 transition duration-300 ease-in-out">
            <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Users" data-th="จำนวนผู้ใช้ทั้งหมด">Total Users</h3>
            <p class="text-gray-600">Data date at %time%</p>
            <p class="text-gray-500 text-2xl"><?php echo getTotalCars(); ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md hover:bg-green-200 transition duration-300 ease-in-out">
            <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Admin" data-th="จำนวนแอดมิน">Total Admin</h3>
            <p class="text-gray-600">Data date at %time%</p>
            <p class="text-gray-500 text-2xl"><?php echo getActiveCarstodays(); ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md hover:bg-yellow-200 transition duration-300 ease-in-out">
            <h3 class="text-xl font-semibold text-gray-800 lang-text" data-en="Total Manager" data-th="จำนวนผู้ดูแล">Total Manager</h3>
            <p class="text-gray-600">Data date at %time%</p>
            <p class="text-gray-500 text-2xl"><?php echo getLeaveCarstoday(); ?></p>
        </div>
    </div>

    <!-- User Role Management Section -->
    <div class="mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 lang-text" data-en="Manage Roles" data-th="จัดการบทบาท">Manage Roles</h3>
        <br>
        <!-- Table -->
    <div class="overflow-x-auto mt-6">
        <table class="min-w-full bg-white shadow-md rounded-lg" id="usersTable">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="ID" data-th="ไอดี">ID</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="Name" data-th="ชื่อ">Name</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="Lastname" data-th="นามสกุล">Lastname</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="User ID" data-th="ยูสเชอร์ไอดี">User ID</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="Status" data-th="สถานะ">Status</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="Role" data-th="บทบาท">Role</th>
                    <th class="py-2 px-4 border-b-2 lang-text" data-en="Action" data-th="ปุ่ม">Actions</th>
                </tr>
            </thead>
            <tbody id="userData">
                <?php
                include 'db_connection_userroles.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

                $result = $conn->query("SELECT * FROM Users");

                while ($user = $result->fetch_assoc()) {
                    echo "<tr data-id='{$user['id']}'>";
                    echo "<td class='py-2 px-4 border-b'>{$user['id']}</td>";
                    echo "<td class='py-2 px-4 border-b'>{$user['name']}</td>";
                    echo "<td class='py-2 px-4 border-b'>{$user['lastname']}</td>";
                    echo "<td class='py-2 px-4 border-b'>{$user['user_id']}</td>";
                    echo "<td class='py-2 px-4 border-b'>{$user['status']}</td>";
                    echo "<td class='py-2 px-4 border-b'>{$user['role']}</td>";
                    echo "<td class='py-2 px-4 border-b text-center'>
                            <button onclick='editUser({$user['id']})' class='bg-yellow-500 text-white px-4 py-1 rounded mr-2 lang-text' data-en='Edit' data-th='แก้ไข'>Edit</button>
                            <button onclick='showDeleteConfirmation({$user['id']})' class='bg-red-500 text-white px-4 py-1 rounded lang-text' data-en='Delete' data-th='ลบผู้ใช้'>Delete</button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add User Button -->
    <div class="text-right mt-4">
        <button onclick="openAddUserModal()" class="bg-blue-500 text-white px-4 py-2 rounded lang-text" data-en="Add New User" data-th="เพิ่มผู้ใช้">Add New User</button>
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

 <!-- Modals Add User -->
 <div id="add-user-modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-auto">
        <h3 class="text-lg font-semibold mb-4 text-center lang-text" data-en="Add New User" data-th="เพิ่มผู้ใช้">Add New User</h3>
        <form id="addUserForm" class="p-4 md:p-5">
            <div class="grid gap-4 mb-4">
                <div class="col-span-2">
                    <input type="hidden" name="action" value="add">
                    
                    <label class="block mb-2 lang-text" data-en="Name" data-th="ชื่อ">Name</label>
                    <input type="text" name="name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type name" required>
                    
                    <label class="block mb-2 mt-4 lang-text" data-en="Lastname" data-th="นามสกุล">Lastname</label>
                    <input type="text" name="lastname" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type Lastname" required>
                    
                    <label class="block mb-2 mt-4 lang-text" data-en="User ID" data-th="ยูสเชอร์ไอดี">User ID</label>
                    <input type="text" name="user_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type User ID" required>
                    
                    <label class="block mb-2 mt-4 lang-text" data-en="Status" data-th="สถานะ">Status</label>
                    <select name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5">
                        <option selected>Select Status</option>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                    </select>

                    <label class="block mb-2 mt-4 lang-text" data-en="Role" data-th="บทบาท">Role</label>
                    <select name="role" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5" required>
                        <option selected>Select Roles</option>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                    </select>

                    <div class="text-right mt-4">
                        <button type="button" onclick="addUser()" class="bg-green-500 text-white px-4 py-2 rounded lang-text" data-en="Confirm" data-th="ตกลง">Save</button>
                        <button type="button" onclick="closeAddUserModal()" class="bg-gray-400 text-white px-4 py-2 rounded ml-2 lang-text" data-en="Cancel" data-th="ยกเลิก">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-auto">
        <h3 class="text-lg font-semibold mb-4 text-center">Edit User</h3>
        <form id="editUserForm" class="p-4 md:p-5">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit-user-id"> <!-- Hidden field to store user ID -->

            <div class="grid gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block mb-2">Name</label>
                    <input type="text" name="name" id="edit-name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type name" required>
                    
                    <label class="block mb-2 mt-4">Lastname</label>
                    <input type="text" name="lastname" id="edit-lastname" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type Lastname" required>
                    
                    <label class="block mb-2 mt-4">User ID</label>
                    <input type="text" name="user_id" id="edit-user_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full p-2.5" placeholder="Type User ID" required>
                    
                    <label class="block mb-2 mt-4">Status</label>
                    <select name="status" id="edit-status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5">
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                    </select>

                    <label class="block mb-2 mt-4">Role</label>
                    <select name="role" id="edit-role" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-2.5" required>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                    </select>

                    <div class="text-right mt-4">
                    <button type="button" onclick="showConfirmEdit()" class="bg-green-500 text-white px-4 py-2 rounded">Save Changes</button>
                    <button type="button" onclick="closeEditUserModal()" class="bg-gray-400 text-white px-4 py-2 rounded ml-2">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

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
                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Select status</option>
                            <option value="Admin">Admin</option>
                            <option value="status">Manager</option>

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

<script>
    function openAddUserModal() {
        document.getElementById('add-user-modal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('add-user-modal').classList.add('hidden');
    }

// ฟังก์ชันแสดงการแจ้งเตือน
function showAlert(message) {
    const alertBox = document.createElement('div');
    alertBox.className = "bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-4";
    alertBox.role = "alert";
    
    alertBox.innerHTML = `
        <p class="font-bold">Be Warned</p>
        <p>${message}</p>
    `;
    
    // เพิ่มการแจ้งเตือนไปยังด้านบนของฟอร์ม
    const formContainer = document.getElementById('addUserForm').parentElement;
    formContainer.insertBefore(alertBox, formContainer.firstChild);

    // ตั้งเวลาให้แจ้งเตือนหายไปหลังจาก 3 วินาที
    setTimeout(() => alertBox.remove(), 3000);
}

// ฟังก์ชัน addUser ที่มีการตรวจสอบและเรียกใช้งาน showAlert
function addUser() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    const name = formData.get('name').trim();
    const lastname = formData.get('lastname').trim();
    const userId = formData.get('user_id').trim();
    const status = formData.get('status');
    const role = formData.get('role');

    if (!name || !lastname || !userId || !status || !role) {
        showAlert("กรุณากรอกข้อมูลให้ครบทุกช่อง");
        return;
    }

    if (status !== 'Admin' && status !== 'Manager') {
        showAlert("กรุณาเลือก Status เป็น Admin หรือ Manager เท่านั้น");
        return;
    }

    if (role !== 'Student' && role !== 'Teacher') {
        showAlert("กรุณาเลือก Role เป็น Student หรือ Teacher เท่านั้น");
        return;
    }

    fetch('manage_users_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const newRow = `<tr data-id="${data.user.id}">
                                <td class="py-2 px-4 border-b">${data.user.id}</td>
                                <td class="py-2 px-4 border-b">${data.user.name}</td>
                                <td class="py-2 px-4 border-b">${data.user.lastname}</td>
                                <td class="py-2 px-4 border-b">${data.user.user_id}</td>
                                <td class="py-2 px-4 border-b">${data.user.status}</td>
                                <td class="py-2 px-4 border-b">${data.user.role}</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <button onclick="editUser(${data.user.id})" class="bg-yellow-500 text-white px-4 py-1 rounded mr-2">Edit</button>
                                    <button onclick="showDeleteConfirmation(${data.user.id})" class="bg-red-500 text-white px-4 py-1 rounded">Delete</button>
                                </td>
                            </tr>`;
            document.getElementById('userData').insertAdjacentHTML('beforeend', newRow);
            closeAddUserModal();
        } else {
            showAlert(data.message || "Failed to add user.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert("An error occurred while adding the user.");
    });
}



function editUser(id) {
    fetch(`manage_users_actions_edit.php?action=get_user&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // เติมข้อมูลในฟอร์มแก้ไข
            document.getElementById('edit-user-id').value = data.user.id;
            document.getElementById('edit-name').value = data.user.name;
            document.getElementById('edit-lastname').value = data.user.lastname;
            document.getElementById('edit-user_id').value = data.user.user_id;
            document.getElementById('edit-status').value = data.user.status;
            document.getElementById('edit-role').value = data.user.role;

            // แสดง modal
            document.getElementById('edit-user-modal').classList.remove('hidden');
        } else {
            alert("Error loading user data.");
        }
    });
}

// ฟังก์ชันปิด modal แก้ไข
function closeEditUserModal() {
    document.getElementById('edit-user-modal').classList.add('hidden');
}

// ฟังก์ชัน updateUser สำหรับส่งข้อมูลที่แก้ไขแล้วกลับไปอัปเดต
function updateUser() {
    const formData = new FormData(document.getElementById('editUserForm'));
    formData.append('action', 'edit'); // ตั้งค่า action เป็น 'edit'

    fetch('manage_users_actions_edit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // อัปเดตข้อมูลในตารางหลังแก้ไข
            const row = document.querySelector(`tr[data-id='${data.user.id}']`);
            row.children[1].textContent = data.user.name;
            row.children[2].textContent = data.user.lastname;
            row.children[3].textContent = data.user.user_id;
            row.children[4].textContent = data.user.status;
            row.children[5].textContent = data.user.role;

            closeEditUserModal();
        } else {
            showAlert("Failed to update user."); // แสดงข้อความแจ้งเตือนหากการอัปเดตล้มเหลว
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert("An error occurred while updating the user.");
    });
}


function showConfirmEdit() {
    // แสดง modal การยืนยันการแก้ไข
    document.getElementById('confirmEdit').classList.remove('hidden');
}

function closeConfirmEdit() {
    // ซ่อน modal การยืนยันการแก้ไข
    document.getElementById('confirmEdit').classList.add('hidden');
}

function confirmEditUser() {
    // เรียกใช้ฟังก์ชัน updateUser เพื่อบันทึกข้อมูลที่แก้ไข
    updateUser();
    // ปิด modal การยืนยันการแก้ไข
    closeConfirmEdit();
}



    let userIdToDelete = null; // Store the user ID to delete

    function showDeleteConfirmation(userId) {
    // สร้างป๊อปอัปยืนยันการลบ
    const confirmationBox = document.createElement('div');
    confirmationBox.innerHTML = `
        <div id="confirmationBox" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-amber-500 font-semibold">Warning</h3>
                <p class="text-gray-600">Are you sure you want to delete this user?</p>
                <div class="flex items-center gap-6 mt-4">
                    <button onclick="confirmDeleteUser(${userId})" class="bg-red-500 text-white px-4 py-1 rounded">Yes</button>
                    <button onclick="cancelDeleteUser()" class="bg-gray-300 px-4 py-1 rounded">No</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(confirmationBox);
}

function cancelDeleteUser() {
    // ลบป๊อปอัปเมื่อกดปุ่ม "No" หรือหลังลบสำเร็จ
    const confirmationBox = document.getElementById('confirmationBox');
    if (confirmationBox) {
        confirmationBox.remove();
    }
}
function confirmDeleteUser(userId) {
    fetch('manage_users_actions.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'delete', id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ลบแถวผู้ใช้ในตาราง
            document.querySelector(`tr[data-id='${userId}']`).remove();
            cancelDeleteUser(); // ปิดป๊อปอัปเมื่อยืนยันการลบเสร็จ
        }
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


</body>
</html>
