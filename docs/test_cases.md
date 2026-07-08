# Student Management System (SMS) - Test Cases Documentation

## Project Overview

The Student Management System is a comprehensive web application designed to facilitate educational institution operations. The system supports multiple user roles including administrators, teachers, and students, each with distinct responsibilities and access levels. This document outlines test cases covering authentication, session management, CRUD operations across all major modules, role-based access control, and edge cases to ensure system reliability and security.

## Test Case Execution Standards

- **Test Environment:** Development/Staging environment
- **Browser Compatibility:** Chrome, Firefox, Safari, Edge (latest versions)
- **Database State:** Fresh test data loaded before execution
- **User Roles:** Admin, Teacher, Student
- **Security Requirements:** All sensitive operations validated with CSRF tokens

---

## Test Cases

| ID | Description | Steps | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| TC-01 | Login with valid credentials | 1. Navigate to login page. 2. Enter valid username and password. 3. Click "Sign In" button. | User is authenticated and redirected to dashboard. Session token is created. | | |
| TC-02 | Login with incorrect password | 1. Navigate to login page. 2. Enter valid username with incorrect password. 3. Click "Sign In" button. | Error message "Invalid credentials" is displayed. User remains on login page. | | |
| TC-03 | Login with empty required fields | 1. Navigate to login page. 2. Leave email and password fields empty. 3. Click "Sign In" button. | Validation error displayed for empty fields. Form is not submitted. | | |
| TC-04 | Register new student account | 1. Navigate to registration page. 2. Fill in first name, last name, email, student ID, and password. 3. Accept terms and conditions. 4. Click "Register" button. | Account is created successfully. Confirmation email is sent. Redirect to login page with success message. | | |
| TC-05 | User logout | 1. Log in with valid credentials. 2. Navigate to user menu. 3. Click "Logout" button. | Session is terminated. User is redirected to login page. Session cookie is cleared. | | |
| TC-06 | Session timeout after inactivity | 1. Log in with valid credentials. 2. Leave the application idle for configured timeout duration (e.g., 30 minutes). 3. Attempt to perform any action. | User is automatically logged out. Redirect to login page with message "Session expired." | | |
| TC-07 | Access dashboard without login | 1. Open browser and navigate directly to dashboard URL. 2. Do not provide any authentication. | User is redirected to login page. Dashboard content is not accessible. | | |
| TC-08 | CSRF token validation | 1. Log in with valid credentials. 2. Attempt to submit a form with missing or invalid CSRF token. | Form submission is rejected. Error message "Invalid request" is displayed. | | |
| TC-09 | List all students with pagination | 1. Log in as admin. 2. Navigate to Students section. 3. Verify first page displays 10 records. 4. Click "Next" button. | Students are displayed in table format with 10 records per page. Pagination controls are functional. Total count is accurate. | | |
| TC-10 | Add new student record | 1. Log in as admin. 2. Navigate to Students > Add New. 3. Fill in all required fields (name, email, student ID, enrollment date). 4. Click "Save" button. | Student record is created. System displays success message. New student appears in student list. | | |
| TC-11 | Edit existing student record | 1. Log in as admin. 2. Navigate to Students list. 3. Click edit icon on any student. 4. Modify student information (e.g., phone number). 5. Click "Save Changes" button. | Student record is updated successfully. Modification timestamp is recorded. Updated data is reflected in list view. | | |
| TC-12 | Soft-delete student record | 1. Log in as admin. 2. Navigate to Students list. 3. Click delete icon on a student record. 4. Confirm deletion in popup dialog. | Student record is marked as deleted (soft delete). Record is removed from active list but retained in database. Restoration option is available in archive. | | |
| TC-13 | Add new teacher record | 1. Log in as admin. 2. Navigate to Teachers > Add New. 3. Fill in required fields (name, email, employee ID, qualifications). 4. Click "Save" button. | Teacher record is created. Success notification is displayed. New teacher appears in teacher list. | | |
| TC-14 | Edit teacher record | 1. Log in as admin. 2. Navigate to Teachers list. 3. Select a teacher and click edit. 4. Modify information (e.g., department, qualifications). 5. Click "Save Changes" button. | Teacher record is updated. Change log entry is created. Updated information is immediately reflected in system. | | |
| TC-15 | Soft-delete teacher record | 1. Log in as admin. 2. Navigate to Teachers list. 3. Click delete icon for a teacher. 4. Confirm deletion. | Teacher is marked as inactive. Teacher cannot be assigned to new courses. Archived courses remain accessible for historical reference. | | |
| TC-16 | Add course with teacher assignment | 1. Log in as admin. 2. Navigate to Courses > Add New. 3. Enter course code, name, description, credits. 4. Assign teacher from dropdown. 5. Click "Create Course" button. | Course is created successfully. Teacher assignment is confirmed. Course appears in active courses list. | | |
| TC-17 | Edit course details | 1. Log in as admin. 2. Navigate to Courses list. 3. Click edit on a course. 4. Modify course details (e.g., capacity, description). 5. Click "Update" button. | Course information is updated. Change is logged. Updated details are visible to enrolled students. | | |
| TC-18 | Delete course with existing enrollments warning | 1. Log in as admin. 2. Navigate to Courses list. 3. Select a course with active enrollments. 4. Click delete button. | Warning dialog is displayed listing number of enrolled students. User must confirm deletion. Upon confirmation, course is archived and enrollments are preserved for records. | | |
| TC-19 | Enroll student in course | 1. Log in as student. 2. Navigate to Available Courses. 3. Select a course. 4. Click "Enroll" button. 5. Confirm enrollment. | Student is enrolled in course. Enrollment date is recorded. Course appears in "My Courses" section. | | |
| TC-20 | Prevent duplicate enrollment | 1. Log in as student already enrolled in a course. 2. Navigate to course details. 3. Attempt to click "Enroll" button. | "Enroll" button is disabled or hidden. System displays message "Already enrolled in this course." | | |
| TC-21 | Drop course enrollment | 1. Log in as student. 2. Navigate to My Courses. 3. Select an enrolled course. 4. Click "Drop Course" button. 5. Confirm action. | Enrollment is removed. Student is no longer listed as participant. Grades and attendance records are archived. | | |
| TC-22 | Add grade with auto-calculation | 1. Log in as teacher. 2. Navigate to Grades > Add Grade. 3. Select student and course. 4. Enter assessment scores (e.g., quiz 20%, assignment 30%, exam 50%). 5. Click "Calculate and Save". | Final grade is automatically calculated using weighted formula. Grade is saved and visible to student. Notification is sent to student. | | |
| TC-23 | Edit existing grade entry | 1. Log in as teacher. 2. Navigate to Grades for a course. 3. Click edit on a grade entry. 4. Modify component score (e.g., exam score). 5. Click "Update" button. | Grade is recalculated automatically. Modification timestamp is recorded. Updated grade is visible to student immediately. | | |
| TC-24 | Teacher views only assigned courses' grades | 1. Log in as teacher. 2. Navigate to Grades section. 3. Verify courses listed. | Teacher sees only courses assigned to them. Attempting to access grades for other courses results in "Access Denied" error. | | |
| TC-25 | Mark bulk attendance | 1. Log in as teacher. 2. Navigate to Attendance > Mark Attendance. 3. Select a course section and date. 4. Check/uncheck attendance boxes for students. 5. Click "Save Attendance" button. | Attendance records are created for all selected students. System displays success message. Attendance count is updated. | | |
| TC-26 | Update existing attendance record | 1. Log in as teacher. 2. Navigate to Attendance for a specific date/course. 3. Modify a student's attendance status (present to absent). 4. Click "Update" button. | Attendance record is modified. Last modified timestamp is updated. Change is reflected in attendance reports. | | |
| TC-27 | View and generate attendance report | 1. Log in as teacher. 2. Navigate to Reports > Attendance. 3. Select date range and course. 4. Click "Generate Report". | Comprehensive attendance report is displayed with attendance percentage per student. Export to PDF/Excel option is available. | | |
| TC-28 | Edit user profile | 1. Log in as any user. 2. Navigate to Profile settings. 3. Modify profile information (phone, address, profile picture). 4. Click "Save Profile" button. | Profile information is updated successfully. Changes are saved to database. Updated information is reflected system-wide. | | |
| TC-29 | Change password with security validation | 1. Log in as any user. 2. Navigate to Security settings. 3. Enter current password and new password (meeting complexity requirements). 4. Click "Change Password" button. | Password is updated. User is notified of successful change. Old session tokens are invalidated. User must re-authenticate with new password. | | |
| TC-30 | Student cannot access admin pages | 1. Log in as student. 2. Attempt to navigate to admin panel URL (/admin). 3. Alternatively, attempt to access sensitive admin functions. | Access is denied. User is redirected to dashboard. Error message "You do not have permission to access this page" is displayed. | | |

---

## Test Case Categories Summary

### Authentication & Session Management (TC-01 to TC-08)
Tests cover login/logout functionality, session lifecycle, timeout mechanisms, and security measures including CSRF protection.

### Student Management Module (TC-09 to TC-12)
Tests validate CRUD operations for student records including listing with pagination, creation, modification, and soft deletion.

### Teacher Management Module (TC-13 to TC-15)
Tests cover teacher record management with proper lifecycle handling including creation, editing, and archival.

### Course Management Module (TC-16 to TC-18)
Tests validate course operations including creation with teacher assignment, modification, and safe deletion with enrollment warnings.

### Enrollment Management Module (TC-19 to TC-21)
Tests ensure proper enrollment workflows including creation, duplicate prevention, and proper course dropping.

### Grade Management Module (TC-22 to TC-24)
Tests validate grading functionality including automated calculations, modifications, and role-based access control.

### Attendance Management Module (TC-25 to TC-27)
Tests cover bulk attendance marking, modifications, and report generation capabilities.

### Profile & Access Control (TC-28 to TC-30)
Tests validate user profile management and enforce role-based access restrictions for sensitive areas.

---

## Notes for QA Team

- Execute tests in sequence for better dependency management
- Reset test data between major test suites
- Document any deviations from expected results with screenshots
- Verify error messages match specification documentation
- Confirm all timestamp fields are accurately recorded
- Test with multiple browsers for compatibility
- Validate responsive design on mobile devices where applicable

---

**Document Version:** 1.0  
**Last Updated:** 2026-04-28  
**Prepared for:** Student Management System QA Team
