CREATE DATABASE manager_users;
USE manager_users;

CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    user_id VARCHAR(20) UNIQUE NOT NULL, -- รหัสผู้ใช้ที่ไม่ซ้ำ เช่น รหัสนักเรียนหรือครู
    status ENUM('Admin', 'Manager', 'User') NOT NULL, -- สถานะการเข้าถึงระบบ
    role ENUM('Student', 'Teacher') NULL -- บทบาทการใช้งาน สามารถปล่อยว่างได้ในกรณีที่ไม่ใช่นักเรียนหรือครู
);

CREATE TABLE UserRoles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT, -- เชื่อมโยงกับ Users.id
    action VARCHAR(50) NOT NULL, -- การกระทำ เช่น "Login", "Logout", "Access Page"
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- บันทึกวันที่และเวลา
    FOREIGN KEY (user_id) REFERENCES Users(id)
);


INSERT INTO Users (name, lastname, user_id, status, role)
VALUES ('Dome', 'Srisuwan', 'S12345', 'User', 'Student'),
       ('Alice', 'Smith', 'T67890', 'User', 'Teacher'),
       ('Bob', 'Johnson', 'A00123', 'Admin', NULL),
       ('Charlie', 'Brown', 'M00456', 'Manager', NULL),
       ('David', 'Lee', 'U78901', 'User', NULL);

