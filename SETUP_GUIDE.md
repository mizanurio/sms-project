# Student Management System (SMS) - Setup Guide

This guide provides step-by-step instructions to set up and run the Student Management System on Windows using XAMPP.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [XAMPP Installation](#xampp-installation)
3. [Starting Services](#starting-services)
4. [Database Setup](#database-setup)
5. [Application Configuration](#application-configuration)
6. [Accessing the Application](#accessing-the-application)
7. [Default Credentials](#default-credentials)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

- **Operating System:** Windows 7 or later (Windows 10/11 recommended)
- **RAM:** Minimum 2GB (4GB recommended)
- **Disk Space:** At least 500MB free space
- **Web Browser:** Modern browser (Chrome, Firefox, Safari, or Edge)
- **Administrative Access:** Required to install XAMPP

---

## XAMPP Installation

### Step 1: Download XAMPP

1. Visit the official XAMPP website: https://www.apachefriends.org/
2. Click "Download" and select the **Windows** version with **PHP 8.x**
3. Choose the latest stable release (e.g., XAMPP 8.2.x or higher)

### Step 2: Run the Installer

1. Locate the downloaded file (usually in `Downloads` folder)
2. Right-click on the installer and select **"Run as administrator"**
3. Click **"Next"** on the welcome screen

### Step 3: Choose Installation Components

The following components are **required**:
- Apache
- MySQL
- PHP
- phpMyAdmin

**Optional but recommended:**
- Perl
- OpenSSL

Click **"Next"** to continue.

### Step 4: Select Installation Directory

1. The default location is: `C:\xampp\`
2. **Do NOT change this location** for simplicity
3. Click **"Next"**

### Step 5: Complete Installation

1. Click **"Finish"** to complete the installation
2. When asked, select **"Yes"** if prompted to start the XAMPP Control Panel

### Step 6: Windows Firewall (if prompted)

- Click **"Allow access"** if Windows Firewall prompts you
- This allows Apache to serve web pages locally

---

## Starting Services

### Method 1: XAMPP Control Panel (Recommended for Beginners)

1. **Open XAMPP Control Panel:**
   - Windows Start Menu → XAMPP → XAMPP Control Panel
   - Or navigate to: `C:\xampp\xampp-control.exe`

2. **Start Apache:**
   - Click the **"Start"** button next to "Apache"
   - Wait until the status changes to green and shows "Running"

3. **Start MySQL:**
   - Click the **"Start"** button next to "MySQL"
   - Wait until the status changes to green and shows "Running"

**Expected Status:**
```
Apache    [Running] [Pid: 1234]
MySQL     [Running] [Pid: 5678]
```

### Method 2: Command Line (Advanced)

1. Open Command Prompt as Administrator
2. Navigate to XAMPP: `cd C:\xampp`
3. Start Apache: `apache_start.bat`
4. Start MySQL: `mysql_start.bat`

---

## Database Setup

### Step 1: Access phpMyAdmin

1. Open your web browser
2. Navigate to: **http://localhost/phpmyadmin/**
3. You should see the phpMyAdmin login page
4. Default credentials (no password needed):
   - Username: `root`
   - Password: (leave blank)
5. Click **"Go"**

### Step 2: Create Database (if needed)

1. Click **"New"** on the left sidebar
2. Enter database name: `sms_database`
3. Select Collation: `utf8mb4_unicode_ci` (recommended)
4. Click **"Create"**

### Step 3: Import Database Schema

1. In phpMyAdmin, select your database: `sms_database`
2. Click the **"Import"** tab at the top
3. Click **"Choose File"** and navigate to:
   - `C:\xampp\htdocs\sms-project\database\sms_schema.sql`
4. Click **"Go"** to execute
5. You should see confirmation: "Successful" message

### Step 4: Import Sample Data

1. Still in phpMyAdmin with `sms_database` selected
2. Click the **"Import"** tab again
3. Click **"Choose File"** and navigate to:
   - `C:\xampp\htdocs\sms-project\database\sms_seed_data.sql`
4. Click **"Go"** to execute
5. You should see confirmation: "Successful" message

**Verification:**
- Click on the database name in the left sidebar
- You should see tables like: `users`, `students`, `courses`, `enrollments`, `grades`, `attendance`

---

## Application Configuration

### Step 1: Extract Project Files

1. Download or clone the SMS project
2. Extract the folder to: `C:\xampp\htdocs\sms-project\`

**Directory structure should look like:**
```
C:\xampp\htdocs\
├── sms-project/
│   ├── public/
│   ├── includes/
│   ├── controllers/
│   ├── views/
│   ├── database/
│   ├── README.md
│   └── SETUP_GUIDE.md
```

### Step 2: Configure Database Connection

1. Navigate to: `C:\xampp\htdocs\sms-project\includes\config.php`
2. Open with a text editor (Notepad, Visual Studio Code, etc.)
3. Verify database credentials match your setup:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Empty password is default
define('DB_NAME', 'sms_database');
define('DB_PORT', 3306);
```

**If you set a MySQL password during installation, update `DB_PASS` accordingly.**

### Step 3: Verify Base URL (Optional)

1. In the same `config.php` file, check:

```php
define('BASE_URL', 'http://localhost/sms-project/public/');
```

2. If you installed in a different location, update this path
3. Save the file

---

## Accessing the Application

### Step 1: Open Login Page

1. Open your web browser
2. Navigate to: **http://localhost/sms-project/public/**
3. You should see the SMS login page

### Step 2: Login with Default Credentials

Use one of the default accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@sms.edu.au | Password123! |
| Teacher | teacher1@sms.edu.au | Password123! |
| Student | student1@sms.edu.au | Password123! |

### Step 3: Verify Access

- **Admin:** Should see administrative dashboard with system-wide statistics
- **Teacher:** Should see course and grade management options
- **Student:** Should see enrolled courses and grades

---

## Default Credentials Reference

**Admin Account:**
- Email: `admin@sms.edu.au`
- Password: `Password123!`
- Permissions: Full system access

**Teacher Account:**
- Email: `teacher1@sms.edu.au`
- Password: `Password123!`
- Permissions: Course and grade management

**Student Account:**
- Email: `student1@sms.edu.au`
- Password: `Password123!`
- Permissions: View courses and grades

**IMPORTANT:** Change all default passwords immediately after first login in any production or shared environment.

---

## Troubleshooting

### Issue 1: Apache Won't Start

**Symptom:** Apache button won't turn green in XAMPP Control Panel

**Solutions:**

1. **Port 80 is in use:**
   - Another application is using port 80
   - Check XAMPP Config and change Apache port (e.g., to 8080)
   - Then access: `http://localhost:8080/sms-project/public/`

2. **XAMPP folder is in Program Files:**
   - XAMPP needs write permissions
   - Reinstall XAMPP to: `C:\xampp\` (not Program Files)

3. **Antivirus or Firewall blocking:**
   - Temporarily disable antivirus
   - Allow XAMPP through Windows Firewall
   - Check antivirus whitelist for Apache service

4. **Check Apache logs:**
   - Navigate to: `C:\xampp\apache\logs\error.log`
   - Look for specific error messages

### Issue 2: MySQL Won't Start

**Symptom:** MySQL button won't turn green

**Solutions:**

1. **Port 3306 is in use:**
   - Check if MySQL is already running on another instance
   - Use command line: `netstat -ano | findstr 3306`
   - Change MySQL port in XAMPP config if needed

2. **MySQL service corrupted:**
   - Go to XAMPP folder: `C:\xampp\mysql\`
   - Delete the `data` folder (backup first if important)
   - Restart MySQL to reinitialize

3. **Check MySQL logs:**
   - Navigate to: `C:\xampp\mysql\data\` and check log files
   - Common issues: disk space, corrupted tables

4. **Reset MySQL:**
   - Open Command Prompt as Administrator
   - Navigate to: `cd C:\xampp\mysql\bin\`
   - Run: `mysqld --skip-grant-tables`
   - Then in another prompt, reset root password if needed

### Issue 3: Cannot Access http://localhost/sms-project/public/

**Symptom:** Browser shows "Connection refused" or blank page

**Troubleshooting Steps:**

1. **Verify XAMPP Services:**
   - Check XAMPP Control Panel - are Apache and MySQL running?
   - Restart both if needed

2. **Check File Permissions:**
   - Right-click `C:\xampp\htdocs\sms-project\` folder
   - Properties → Security → Edit
   - Ensure "Everyone" or your user has "Read" and "Modify" permissions

3. **Enable PHP Errors:**
   - Edit `C:\xampp\htdocs\sms-project\includes\config.php`
   - Add: `error_reporting(E_ALL);` and `ini_set('display_errors', '1');`
   - Refresh the page to see specific error messages

4. **Check phpMyAdmin:**
   - Try accessing: `http://localhost/phpmyadmin/`
   - If this works, Apache is fine
   - Issue is likely in the SMS project configuration

### Issue 4: Blank Page or 500 Error

**Symptom:** Page loads but shows nothing or "500 Internal Server Error"

**Solutions:**

1. **Check error logs:**
   - Navigate to: `C:\xampp\apache\logs\error.log`
   - Open in text editor and look for recent PHP errors

2. **Verify database connection:**
   - Check `includes/config.php` credentials
   - Test database import was successful in phpMyAdmin

3. **Check file structure:**
   - Ensure all folders and files are in correct locations
   - No files should be missing or renamed

4. **PHP version compatibility:**
   - Verify XAMPP PHP version is 8.x or higher
   - Check XAMPP Control Panel → Config → Version

### Issue 5: Login Page Appears But Cannot Login

**Symptom:** Login form displays but credentials don't work

**Solutions:**

1. **Verify database has sample data:**
   - Open phpMyAdmin
   - Go to `sms_database` → `users` table
   - You should see at least 3 users
   - If empty, re-import `sms_seed_data.sql`

2. **Check database credentials in config.php:**
   - Verify `DB_USER`, `DB_PASS`, and `DB_NAME` are correct
   - Restart Apache and MySQL after any changes

3. **Clear browser cache:**
   - Press `Ctrl+Shift+Delete` to open cache clearing dialog
   - Clear all cached images and files
   - Refresh the page

4. **Check MySQL connection:**
   - In phpMyAdmin, verify you can connect
   - If you can't, MySQL settings are incorrect

### Issue 6: "Access Denied" When Accessing phpMyAdmin

**Symptom:** Cannot login to phpMyAdmin with root/blank password

**Solutions:**

1. **Default login should be:**
   - Username: `root`
   - Password: (leave blank)
   - Click "Go"

2. **If default doesn't work:**
   - You may have set a MySQL password during installation
   - Try the password you created
   - Or reinstall XAMPP and select "No password" option

3. **Reset MySQL root password:**
   - Stop MySQL in XAMPP
   - Use MySQL command line tools to reset
   - Documentation: [MySQL Password Reset Guide](https://dev.mysql.com/doc/refman/8.0/en/resetting-permissions.html)

### Issue 7: Database Import Fails

**Symptom:** "Import" button doesn't work or shows error

**Solutions:**

1. **File size too large:**
   - Check phpMyAdmin `Settings` → `Upload` settings
   - Increase `upload_max_filesize` if needed
   - Edit: `C:\xampp\php\php.ini`

2. **File encoding issue:**
   - Open SQL file in text editor
   - Ensure file is saved as UTF-8
   - Try importing again

3. **Syntax errors in SQL:**
   - Open `.sql` file in text editor
   - Check for any obvious syntax errors
   - Ensure file wasn't corrupted during download

4. **Database already has tables:**
   - Drop existing tables first
   - Then import schema
   - Then import seed data

---

## Next Steps

Once setup is complete:

1. **Change Default Passwords:**
   - Login as admin
   - Go to user management
   - Update all default account passwords

2. **Explore Features:**
   - Create new students, courses, enrollments
   - Test grade and attendance tracking
   - Verify role-based access works

3. **Customize Settings:**
   - Update institution name in config if needed
   - Adjust database timeout or other settings

4. **Review Documentation:**
   - See README.md for project overview
   - Check code comments for technical details

---

## Getting Help

If you encounter issues not covered here:

1. **Check XAMPP error logs:**
   - Apache: `C:\xampp\apache\logs\error.log`
   - MySQL: `C:\xampp\mysql\data\` (log files)

2. **Verify XAMPP Installation:**
   - Run XAMPP Control Panel as Administrator
   - Check all services start without errors

3. **Consult Official Documentation:**
   - XAMPP: https://www.apachefriends.org/
   - PHP: https://www.php.net/docs.php
   - MySQL: https://dev.mysql.com/doc/

4. **Common Solutions:**
   - Restart both Apache and MySQL completely
   - Clear browser cache (Ctrl+Shift+Delete)
   - Disable antivirus/firewall temporarily to test
   - Reinstall XAMPP if all else fails

---

**Last Updated:** April 2026
**Compatible with:** XAMPP 8.0+, Windows 7+
**Status:** Ready for Production Setup
