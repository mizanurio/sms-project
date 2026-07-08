# Screenshot Naming Guide

This directory contains screenshots of the Student Management System application throughout its various pages and workflows.

## Naming Convention

All screenshots follow a consistent naming convention for easy identification and organization:

```
[number]_[page_name].png
```

Where:
- `[number]` — A two-digit sequential number (01-20+) indicating the order of the screenshots
- `[page_name]` — A brief, lowercase description of the page or feature (using underscores for spaces)
- `.png` — PNG format for optimal quality and compression

## Screenshot List

Below is the complete list of screenshots to capture for the application:

### Authentication & Landing
- `01_login.png` — User login page with email/password form
- `02_register.png` — New user registration page
- `03_forgot_password.png` — Password recovery/reset page
- `04_reset_password.png` — Password reset form after email verification

### Dashboard & Home
- `05_admin_dashboard.png` — Admin dashboard with key statistics and widgets
- `06_student_dashboard.png` — Student dashboard with personal information
- `07_teacher_dashboard.png` — Teacher dashboard with class overview

### Student Management
- `08_students_list.png` — List of all students with search and filter options
- `09_student_add.png` — Form to add a new student
- `10_student_edit.png` — Form to edit an existing student's information
- `11_student_profile.png` — Individual student profile view with full details

### Teacher Management
- `12_teachers_list.png` — List of all teachers with search functionality
- `13_teacher_add.png` — Form to add a new teacher
- `14_teacher_edit.png` — Form to edit teacher information
- `15_teacher_profile.png` — Individual teacher profile view

### Classes & Courses
- `16_classes_list.png` — List of all classes/courses
- `17_class_add.png` — Form to create a new class
- `18_class_details.png` — Class overview with enrolled students

### Enrollment
- `19_enrollment_list.png` — Student enrollment records with status
- `20_enrollment_add.png` — Form to enroll a student in a course

### Reports & Analytics
- `21_reports_attendance.png` — Attendance report view
- `22_reports_grades.png` — Grades and performance report
- `23_reports_enrollment.png` — Enrollment statistics report

### User & Profile
- `24_user_profile.png` — Current user profile settings
- `25_user_edit_profile.png` — Edit user account information
- `26_change_password.png` — Change password form

### Error States
- `27_error_404.png` — 404 Not Found error page
- `28_error_403.png` — 403 Forbidden access error page
- `29_error_500.png` — 500 Server error page
- `30_validation_error.png` — Form with validation error messages

## Guidelines for Screenshots

When capturing screenshots, follow these guidelines:

1. **Screen Resolution**: Use a consistent screen resolution (1920x1080 recommended for desktop views)
2. **Browser**: Use a modern browser (Chrome, Firefox, Safari, or Edge)
3. **Data**: Use realistic but non-sensitive sample data
4. **UI State**: Capture both empty and populated states where relevant
5. **Responsive**: Include mobile versions (768px width) for key pages
6. **Lighting**: Ensure proper contrast and readability
7. **Naming**: Follow the naming convention strictly for consistency

## Organization by Type

Screenshots can be further organized into subdirectories by type if needed:

```
screenshots/
├── authentication/
│   ├── 01_login.png
│   ├── 02_register.png
│   └── ...
├── dashboards/
│   ├── 05_admin_dashboard.png
│   ├── 06_student_dashboard.png
│   └── ...
├── forms/
│   ├── 09_student_add.png
│   ├── 10_student_edit.png
│   └── ...
└── errors/
    ├── 27_error_404.png
    └── ...
```

## Usage

These screenshots are used for:
- Documentation and user guides
- Feature demonstration and training
- Bug reporting and issue tracking
- UI/UX review and feedback
- Quality assurance testing
