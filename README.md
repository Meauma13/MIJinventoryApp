# Inventory Management System

A comprehensive inventory management system built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- Product management (CRUD)
- Category and subcategory management
- Brand management
- Warehouse transfers
- Point of Sale (POS) with barcode scanning
- Audit logging
- CEO dashboard with business metrics
- Role-based access (Admin/CEO)
- Sales reports
- Customer management

## Setup

### Local Development

1. Install WAMP/XAMPP
2. Import `database_schema.sql` into MySQL
3. Update `admin/includes/dbconnection.php` with your DB credentials
4. Run on localhost

### Production Deployment

1. Choose a hosting platform that supports PHP and MySQL (e.g., Railway, Heroku, DigitalOcean)
2. Set environment variables:
   - DB_HOST
   - DB_USER
   - DB_PASS
   - DB_NAME
3. Import the database schema
4. Deploy the code

## Technologies

- PHP
- MySQL
- Bootstrap
- jQuery
- QuaggaJS (for barcode scanning)