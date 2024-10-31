CREATE DATABASE Car_Management;

USE Car_Management;

CREATE TABLE Manage_Cars (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL,
    Lastname VARCHAR(50) NOT NULL,
    Student_ID VARCHAR(20) NOT NULL,
    Grade VARCHAR(10),
    Room VARCHAR(10),
    Division VARCHAR(50),
    Province VARCHAR(50),
    Running_Number VARCHAR(10),
    Car_Band VARCHAR(50),
    Color_of_Vehicle VARCHAR(20),
    Vehicle_Type VARCHAR(50),
    Vehicle_Identification_Number VARCHAR(50),
    Engine_Number VARCHAR(50),
    Year_of_Manufacture INT,
    Vehicle_Weight DECIMAL(10,2),
    Fuel_Type VARCHAR(20),
    Engine_CC INT,
    Identification_Card_Document VARCHAR(255),
    Vehicle_Verification_Document VARCHAR(255),
    Creates_Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Car_Reports (
    Report_ID INT AUTO_INCREMENT PRIMARY KEY,
    Car_ID INT,
    License_Plate VARCHAR(100),
    Status ENUM('In Garage', 'Out Garage') DEFAULT 'Out Garage',
    Entry_Exit_Time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Car_ID) REFERENCES Manage_Cars(ID)
);

CREATE TABLE Car_Request (
    Request_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL,
    Lastname VARCHAR(50) NOT NULL,
    Student_ID VARCHAR(20) NOT NULL,
    Grade VARCHAR(10),
    Room VARCHAR(10),
    Division VARCHAR(50),
    Province VARCHAR(50),
    Running_Number VARCHAR(10),
    Car_Band VARCHAR(50),
    Color_of_Vehicle VARCHAR(20),
    Vehicle_Type VARCHAR(50),
    Vehicle_Identification_Number VARCHAR(50),
    Engine_Number VARCHAR(50),
    Year_of_Manufacture INT,
    Vehicle_Weight DECIMAL(10,2),
    Fuel_Type VARCHAR(20),
    Engine_CC INT,
    Identification_Card_Document VARCHAR(255),
    Vehicle_Verification_Document VARCHAR(255),
    Status ENUM('Sending Request', 'Accept Request', 'Passed') DEFAULT 'Sending Request',
    Request_Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO Manage_Cars (Name, Lastname, Student_ID, Grade, Room, Division, Province, Running_Number, Car_Band, Color_of_Vehicle, Vehicle_Type, Vehicle_Identification_Number, Engine_Number, Year_of_Manufacture, Vehicle_Weight, Fuel_Type, Engine_CC, Identification_Card_Document, Vehicle_Verification_Document)
VALUES 
('Somchai', 'Somsri', '123456', '4', '1', 'B', 'Bangkok', '101', 'Toyota', 'Red', 'Sedan', 'JTDBT123456789012', '4A123456', 2022, 1200.50, 'Petrol', 1800, 'path/to/id_card.pdf', 'path/to/vehicle_doc.pdf');

INSERT INTO Car_Reports (Car_ID, License_Plate, Status)
VALUES 
(1, '101 B Bangkok', 'In Garage');

INSERT INTO Car_Request (Name, Lastname, Student_ID, Grade, Room, Division, Province, Running_Number, Car_Band, Color_of_Vehicle, Vehicle_Type, Vehicle_Identification_Number, Engine_Number, Year_of_Manufacture, Vehicle_Weight, Fuel_Type, Engine_CC, Identification_Card_Document, Vehicle_Verification_Document, Status)
VALUES 
('Somchai', 'Somsri', '123456', '4', '1', 'B', 'Bangkok', '101', 'Toyota', 'Red', 'Sedan', 'JTDBT123456789012', '4A123456', 2022, 1200.50, 'Petrol', 1800, 'path/to/id_card.pdf', 'path/to/vehicle_doc.pdf', 'Sending Request');

SELECT * FROM Manage_Cars;

SELECT * FROM Car_Reports;

SELECT * FROM Car_Request;

UPDATE Car_Request SET Status = 'Accept Request' WHERE Request_ID = 1;

UPDATE Car_Reports SET Status = 'Out Garage' WHERE Report_ID = 1;
