-- Inventory Management System Database Schema
-- Run this on your production MySQL database

CREATE DATABASE IF NOT EXISTS inventorydb;
USE inventorydb;

-- Admin table
CREATE TABLE tbladmin (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    AdminName VARCHAR(120),
    UserName VARCHAR(120),
    MobileNumber VARCHAR(120),
    Email VARCHAR(200),
    Password VARCHAR(120),
    AdminRegdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) DEFAULT 'admin'
);

-- Site users table (customers / staff signups)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(120),
    lname VARCHAR(120),
    username VARCHAR(120) UNIQUE,
    email VARCHAR(200) UNIQUE,
    password VARCHAR(255),
    contactno VARCHAR(30),
    posting_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE tblcategory (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(125),
    CategoryCode VARCHAR(125),
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subcategories
CREATE TABLE tblsubcategory (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Categoryid INT,
    Subcategoryname VARCHAR(125),
    SubcategoryCode VARCHAR(125),
    Creationdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Categoryid) REFERENCES tblcategory(ID) ON DELETE CASCADE
);

-- Brands
CREATE TABLE tblbrand (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BrandName VARCHAR(125),
    BrandCode VARCHAR(125),
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE tblproducts (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(120),
    SubcategoryName VARCHAR(120),
    ProductName VARCHAR(120),
    ProductCode VARCHAR(120),
    ProductPrice DECIMAL(10,2),
    SellingPrice DECIMAL(10,2),
    Stock INT,
    Status INT DEFAULT 1,
    CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    BrandName VARCHAR(120)
);

-- Cart
CREATE TABLE tblcart (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ProductId INT,
    ProductQty INT,
    BillingId VARCHAR(120),
    CartDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IsCheckOut INT DEFAULT 0,
    FOREIGN KEY (ProductId) REFERENCES tblproducts(ID)
);

-- Customer
CREATE TABLE tblcustomer (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BillingNumber VARCHAR(120),
    CustomerName VARCHAR(120),
    CustomerEmail VARCHAR(120),
    CustomerMobileNumber VARCHAR(120),
    ModeofPayment VARCHAR(120),
    BillingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Warehouse
CREATE TABLE tblwarehouse (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    WarehouseStock INT,
    WarehouseName VARCHAR(120),
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProductID) REFERENCES tblproducts(ID)
);

-- Shipments
CREATE TABLE tblshipments (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ShipmentNumber VARCHAR(120),
    ShipmentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status VARCHAR(50) DEFAULT 'pending'
);

-- Shipment Items
CREATE TABLE tblshipmentitems (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ShipmentID INT,
    ProductID INT,
    Quantity INT,
    FOREIGN KEY (ShipmentID) REFERENCES tblshipments(ID),
    FOREIGN KEY (ProductID) REFERENCES tblproducts(ID)
);

-- Audit Log
CREATE TABLE tblauditlog (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    Action VARCHAR(255),
    Details TEXT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES tbladmin(ID)
);

-- Insert sample admin
INSERT INTO tbladmin (AdminName, UserName, MobileNumber, Email, Password, role) VALUES
('Admin User', 'admin', '1234567890', 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'admin'),
('CEO User', 'ceo', '0987654321', 'ceo@example.com', '2e53a4bcf9cba6f96620e39f3a5f0d0a', 'ceo');