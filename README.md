# Ben Shopping Portal

A simple PHP-based portal to manage serial numbers (codes) for products with two roles:

- **Super Admin:** Manages the system, imports Excel files, and views reports.
- **Seller:** Enters serial numbers to mark them as used, ensuring they're only used once.

## Features

- Import Excel files containing serial numbers.
- Login functionality for Super Admin and Seller.
- Seller can enter serial numbers to mark them as used.
- Super Admin can view reports on serial numbers entered by sellers.
- Basic authentication and authorization system.
- Built with PHP 8, PDO for MySQL, Bootstrap 5 for front-end design.

## Requirements

- PHP 8.0 or higher
- MySQL database
- Composer (for managing dependencies)
- Web server (e.g., Apache or Nginx)

## Setup

1. Clone the Repository

    ```bash
    git clone https://github.com/BaseMax/ben-shopping.git
    cd ben-shopping
    ```

2. Install Dependencies

Make sure Composer is installed. Run the following command to install PHP dependencies:

    ```bash
    composer install
    ```

3. Configure Database

Create a new database in MySQL.

Import the provided `ben_shopping.sql` file (located in the database/ folder) to set up the required tables.

Example command:

    ```bash
    mysql -u username -p ben_shopping < database/ben_shopping.sql
    ```

4. Configure .env File

Create a `.env` file in the root of the project directory and add your database connection settings:

    ```env
    DB_HOST=localhost
    DB_NAME=ben_shopping
    DB_USER=root
    DB_PASS=your_password
    ```

5. Set Up the Web Server

Place the project in your web server's root directory (e.g., /var/www/html/ for Apache).

Ensure that your web server is configured to serve PHP files.

6. Access the Portal

Once everything is set up, navigate to your portal in a web browser:

    ```
    http://localhost/ben-shopping/
    ```

7. Login

You can log in with two roles:

Super Admin: The default credentials are:

- Username: `admin`
- Password: `admin`

Seller: A seller can log in using their credentials, which are managed in the database.

8. Import Serial Numbers

Super Admin can import serial numbers using an Excel file (must be in .xlsx format). This file should contain the following columns:

- Serial Number
- Name

The serial numbers are stored in the database and marked as used when a seller submits a number.

### Roles and Permissions

- Super Admin
    - View and manage all serial numbers.
    - Import Excel files to add serial numbers.
    - View reports for all sellers.

- Seller (فروشنده)
    - Enter serial numbers to mark them as used.
    - Only able to enter and submit serial numbers.

### License

This project is licensed under the MIT License - see the LICENSE file for details.

Copyright © 2024 Max Base.
