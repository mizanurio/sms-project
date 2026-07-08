# Student Management System (SMS)

## Project Overview

A comprehensive web-based Student Management System designed for educational institutions to efficiently manage student records, courses, enrollments, grades, and attendance tracking. This system provides role-based access for administrators, teachers, and students, enabling streamlined academic operations and data management.

## Developer Information

- **Developer:** Mizanur

## Technology Stack

### Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

### Backend
- PHP 8.x
- MySQL 8.x
- Apache (via XAMPP)

## Features

### Core Functionality
- **User Authentication:** Secure login system with role-based access control
- **Role-Based Dashboards:** Customized interfaces for Admin, Teacher, and Student roles
- **Student Management:** Create, read, update, and delete student records
- **Course Management:** Manage course information, descriptions, and enrollment capacity
- **Enrollment Management:** Track student course enrollments and manage class lists
- **Grade Management:** Record and manage student grades and academic performance
- **Attendance Tracking:** Monitor and record student attendance
- **Profile Management:** Allow users to view and update their profile information

## System Requirements

- Windows Operating System (or XAMPP-compatible system)
- XAMPP (includes Apache, MySQL, PHP)
- Web Browser (Chrome, Firefox, Safari, Edge)
- Minimum 512MB RAM recommended

## Installation & Setup

### Quick Start

1. **Clone/Extract the Project**
   - Extract the SMS project to: `C:\xampp\htdocs\sms-project\`

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

3. **Initialize Database**
   - Open your browser and navigate to `http://localhost/phpmyadmin/`
   - Create a new database or use existing one
   - Import `sms_schema.sql` (creates tables)
   - Import `sms_seed_data.sql` (adds sample data)

4. **Configure Application**
   - Edit `includes/config.php` if needed
   - Update database connection details if different from defaults

5. **Access the Application**
   - Navigate to: `http://localhost/sms-project/public/`
   - Login with credentials provided below

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@sms.edu.au | Password123! |
| Teacher | teacher1@sms.edu.au | Password123! |
| Student | student1@sms.edu.au | Password123! |

**Important:** Change default passwords immediately after first login in a production environment.

## Project Structure

```
sms-project/
├── public/                 # Publicly accessible entry points
│   ├── index.php          # Login page
│   ├── dashboard.php       # Role-based dashboard
│   ├── register.php        # User registration
│   ├── profile.php         # Profile management
│   ├── logout.php
│   ├── students/           # Student CRUD
│   ├── teachers/           # Teacher CRUD
│   ├── courses/            # Course CRUD
│   ├── enrollments/         # Enrollment management
│   ├── grades/              # Grade management
│   └── attendance/          # Attendance marking & reports
├── includes/                # Shared backend logic
│   ├── config.php          # App + database configuration
│   ├── db.php               # Database connection
│   ├── auth.php              # Authentication & session handling
│   ├── csrf.php              # CSRF token helpers
│   ├── functions.php         # Shared helper functions
│   ├── header.php / footer.php
├── assets/                   # CSS, JS, images
├── database/                 # SQL schema and seed files
│   ├── sms_schema.sql        # Database structure
│   └── sms_seed_data.sql     # Sample data
├── docs/                      # Test cases, user manual, screenshots
├── SETUP_GUIDE.md
└── README.md                  # This file
```

## Key Workflows

### Administrator
- Manage user accounts (create, update, deactivate)
- View system-wide statistics and reports
- Configure system settings
- Monitor all activities

### Teacher
- View assigned courses and students
- Record and manage grades
- Track attendance
- Post course announcements

### Student
- View enrolled courses
- Check grades and attendance
- Update profile information
- Access course materials

## Troubleshooting

For detailed troubleshooting steps, refer to `SETUP_GUIDE.md`.

Common issues:
- **Cannot connect to database:** Check MySQL is running and credentials are correct
- **Blank page:** Enable error reporting in `config.php` or check Apache logs
- **Port conflicts:** Modify XAMPP port settings if 80 or 3306 are in use
- **Permission errors:** Ensure proper file permissions on the project directory

## Support & Documentation

For comprehensive setup instructions, see `SETUP_GUIDE.md` in this directory.

---

**Last Updated:** April 2026
**Status:** Active Development
