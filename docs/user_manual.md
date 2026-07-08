# Student Management System (SMS) - User Manual

## Table of Contents
1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Admin Guide](#admin-guide)
4. [Teacher Guide](#teacher-guide)
5. [Student Guide](#student-guide)
6. [Common Tasks](#common-tasks)
7. [Troubleshooting](#troubleshooting)

---

## Introduction

### What is the Student Management System (SMS)?

The Student Management System is a web-based application designed to manage students, teachers, courses, enrollments, grades, and attendance for an educational institution. This system provides a centralized platform for:

- **Administrators** to manage all users, courses, and system settings
- **Teachers** to manage their courses, assign grades, and track student attendance
- **Students** to view their enrolled courses, grades, and attendance records

### Purpose

The SMS helps streamline university operations by:
- Automating student and teacher management
- Providing real-time grade tracking and reporting
- Maintaining accurate attendance records
- Enabling easy course enrollment and management

### Accessing the System

To access the SMS, open your web browser and navigate to:

```
http://localhost/sms-project/public/
```

You will be presented with the login screen (See screenshot: 01_login.png).

---

## Getting Started

### Default Login Credentials

The system comes with three pre-configured user accounts for testing and initial setup:

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@sms.edu.au | Password123! |
| Teacher | teacher1@sms.edu.au | Password123! |
| Student | student1@sms.edu.au | Password123! |

**Important:** Change these default passwords immediately after your first login for security reasons.

### How to Log In

1. Navigate to the SMS login page at `http://localhost/sms-project/public/`
2. Enter your email address in the **Email** field
3. Enter your password in the **Password** field
4. Click the **Login** button
5. You will be redirected to your dashboard

See screenshot: 01_login.png

### How to Register as a Student

If you do not have an account, you can self-register:

1. On the login page, click the **Register** link (if available)
2. Fill in the registration form with your details:
   - Full Name
   - Email Address
   - Password (minimum 8 characters, with special characters recommended)
   - Confirm Password
3. Click the **Register** button
4. Your account will be created and you can now log in

**Note:** Registration may be restricted by your institution. If you cannot register, contact your administrator.

---

## Admin Guide

### Dashboard Overview

When you log in as an administrator, you will see the Admin Dashboard. This page displays:

- **Stat Cards**: Quick summaries showing:
  - Total Students
  - Total Teachers
  - Total Courses
  - Total Enrollments
  - Total Attendance Records

- **Recent Activity**: A list of the most recent system activities and changes

- **Quick Links**: Shortcuts to frequently used management sections

See screenshot: 02_admin_dashboard.png

### Managing Students

#### Viewing the Student List

1. From the admin dashboard, click **Students** in the navigation menu
2. You will see a list of all students in the system with their details:
   - Student ID
   - Full Name
   - Email Address
   - Registration Date

#### Searching for Students

1. On the Student list page, use the **Search** field at the top
2. Enter the student's name or email address
3. The list will filter in real-time to show matching results

#### Adding a New Student

1. On the Student list page, click the **Add Student** button
2. Fill in the student information form:
   - **Full Name**: Enter the student's complete name
   - **Email**: Enter a valid email address (must be unique)
   - **Password**: Create a temporary password for the student
   - **Date of Birth** (optional): Select the student's date of birth
   - **Phone Number** (optional): Enter the student's contact number
   - **Address** (optional): Enter the student's address details

3. Click **Save** to create the student account
4. The student can now log in using their email and the temporary password

See screenshot: 03_add_student.png

#### Editing Student Information

1. On the Student list page, find the student you want to edit
2. Click the **Edit** button next to their name
3. Update the student's information as needed
4. Click **Save** to apply the changes

#### Viewing Student Details

1. On the Student list page, click on the student's name
2. You will see a detailed profile page showing:
   - Personal information
   - Enrolled courses
   - Grades and attendance records
   - Account activity

#### Deleting a Student (Soft Delete)

1. On the Student list page, find the student you want to delete
2. Click the **Delete** button next to their name
3. Confirm the deletion when prompted
4. **Note:** This is a soft delete—the record is marked as deleted but can be restored if needed by your system administrator

### Managing Teachers

The teacher management section follows the same pattern as student management.

#### Viewing the Teacher List

1. From the admin dashboard, click **Teachers** in the navigation menu
2. You will see a list of all teachers with their details

#### Searching for Teachers

1. Use the **Search** field to find a teacher by name or email

#### Adding a New Teacher

1. Click the **Add Teacher** button
2. Fill in the teacher information:
   - **Full Name**: Enter the teacher's complete name
   - **Email**: Enter a valid email address (must be unique)
   - **Password**: Create a temporary password
   - **Department** (optional): Specify the teacher's department
   - **Office Location** (optional): Enter office details
   - **Specialization** (optional): List their subject specialization

3. Click **Save** to create the account

See screenshot: 04_add_teacher.png

#### Editing Teacher Information

1. Find the teacher on the list and click **Edit**
2. Update their information
3. Click **Save**

#### Viewing Teacher Details

1. Click on a teacher's name to view:
   - Assigned courses
   - Student count
   - Grade history
   - Attendance records

#### Deleting a Teacher

1. Click the **Delete** button next to the teacher's name
2. Confirm the deletion
3. This is a soft delete and can be reversed if needed

### Managing Courses

#### Viewing the Course List

1. From the admin dashboard, click **Courses** in the navigation menu
2. You will see a list of all courses with:
   - Course Code
   - Course Name
   - Assigned Teacher
   - Number of Enrolled Students
   - Semester/Session Information

#### Adding a New Course

1. Click the **Add Course** button
2. Fill in the course details:
   - **Course Code**: Enter a unique course identifier (e.g., CS101)
   - **Course Name**: Enter the full course name
   - **Description**: Provide a brief course description
   - **Credit Hours**: Specify the number of credit hours
   - **Assigned Teacher**: Select a teacher from the dropdown list
   - **Semester**: Select the academic semester
   - **Start Date**: Choose the course start date
   - **End Date**: Choose the course end date

3. Click **Save** to create the course

See screenshot: 05_add_course.png

#### Editing Course Information

1. Find the course on the list and click **Edit**
2. Update the course details as needed
3. Click **Save**

#### Viewing Enrolled Students

1. Click on a course name to view its details page
2. Scroll to the **Enrolled Students** section
3. You will see a list of all students enrolled in the course with their:
   - Student ID
   - Name
   - Email
   - Enrollment Date

#### Deleting a Course

1. Click the **Delete** button next to the course name
2. Confirm the deletion
3. Students will be automatically unenrolled from the course

**Note:** Only courses with no enrollments can be deleted without warning.

### Managing Enrollments

#### Viewing the Enrollment List

1. From the admin dashboard, click **Enrollments** in the navigation menu
2. You will see all student enrollments with filters for:
   - Course
   - Student
   - Semester
   - Status (Active/Inactive)

#### Filtering Enrollments

1. On the Enrollment list page, use the filter options at the top
2. Select criteria such as:
   - **Course**: Filter by specific course
   - **Semester**: Filter by academic period
   - **Status**: Show active or inactive enrollments

3. Click **Apply Filters** to view results

#### Enrolling a Student in a Course

1. Click the **Enroll Student** button
2. Select the **Student** from the dropdown list
3. Select the **Course** from the dropdown list
4. Set the **Enrollment Date**
5. Click **Save** to confirm the enrollment

See screenshot: 06_enroll_student.png

#### Dropping an Enrollment

1. Find the enrollment in the list that you want to remove
2. Click the **Drop** or **Unenroll** button
3. Confirm the action when prompted
4. The student will be removed from the course

### Grade Management

#### Understanding Grade Calculation

Grades are automatically calculated based on marks according to this scale:

| Grade | Marks | Description |
|-------|-------|-------------|
| HD | 85-100 | High Distinction |
| D | 75-84 | Distinction |
| C | 65-74 | Credit |
| P | 50-64 | Pass |
| F | 0-49 | Fail |

#### Adding Grades

1. From the admin dashboard, click **Grades** in the navigation menu
2. Click the **Add Grade** button
3. Fill in the grade information:
   - **Student**: Select the student from the dropdown
   - **Course**: Select the course they are enrolled in
   - **Marks**: Enter the numeric marks (0-100)
   - **Assessment Type** (optional): e.g., Final Exam, Assignment, Quiz

4. Click **Save**
5. The system will automatically calculate and assign the letter grade

See screenshot: 07_add_grade.png

#### Viewing Grades

1. On the Grades page, you can view all grades in the system
2. Use the search and filter options to find specific grades:
   - Filter by Student
   - Filter by Course
   - Filter by Semester

3. Click on a grade entry to view or edit it

#### Editing Grades

1. Find the grade entry you want to modify
2. Click the **Edit** button
3. Update the marks or other information
4. Click **Save**
5. The letter grade will automatically recalculate

#### Understanding the Auto-Grade Calculation

When you enter marks, the system automatically:
1. Checks the mark against the grading scale
2. Assigns the appropriate letter grade
3. Saves the grade to the student's record
4. Updates any reports that include this grade

Example: If you enter 87 marks for a student, the system automatically assigns "HD" (High Distinction).

### Attendance Management

#### Marking Bulk Attendance

1. From the admin dashboard, click **Attendance** in the navigation menu
2. Click the **Mark Attendance** button
3. Select the **Course** and **Date** for which you want to mark attendance
4. You will see a list of all students enrolled in that course with checkboxes
5. Check the boxes for students who were **present**
6. Leave unchecked the boxes for students who were **absent**
7. Click **Save** to record the attendance

See screenshot: 08_mark_attendance.png

#### Viewing Attendance Reports

1. On the Attendance page, click **View Reports**
2. Select filter options:
   - **Course**: Choose a specific course
   - **Student**: Choose a specific student (optional)
   - **Date Range**: Select the period for the report

3. Click **Generate Report**
4. You will see:
   - Total classes held
   - Total classes attended by each student
   - Attendance percentage (calculated as attended/total × 100)
   - Absences

#### Understanding Attendance Percentage

The attendance percentage is calculated as:

```
Attendance % = (Classes Attended / Total Classes) × 100
```

Example: If a student attended 18 out of 20 classes:
```
(18 / 20) × 100 = 90%
```

---

## Teacher Guide

### Dashboard Overview

When you log in as a teacher, you will see the Teacher Dashboard displaying:

- **My Courses**: List of courses you are assigned to teach with:
  - Course name and code
  - Number of enrolled students
  - Start and end dates

- **Student Count**: Total number of students across all your courses

- **Recent Grades**: Grades you have recently entered

- **Quick Actions**: Links to add grades or mark attendance

See screenshot: 09_teacher_dashboard.png

### Adding Grades

#### For Your Own Courses Only

Teachers can only add grades for courses they are assigned to teach.

1. From the teacher dashboard, click **Grades** in the navigation menu
2. You will see only courses assigned to you
3. Click **Add Grade** button
4. Fill in the grade information:
   - **Student**: Select from students enrolled in your courses
   - **Course**: The system will show only your courses
   - **Marks**: Enter the student's marks (0-100)
   - **Assessment Type** (optional): e.g., Exam 1, Assignment 2, Midterm

5. Click **Save**
6. The system will automatically calculate the letter grade

**Note:** You cannot add grades for courses taught by other teachers or for students not enrolled in your courses.

### Marking Attendance

#### For Your Own Courses Only

1. From the teacher dashboard, click **Attendance** in the navigation menu
2. Click **Mark Attendance**
3. Select the **Course** and **Date**
4. You will see only your courses and their enrolled students
5. Check boxes for students who were **present**
6. Leave unchecked for students who were **absent**
7. Click **Save**

**Note:** If a student was already marked present, you can update it by unchecking the box, or vice versa.

See screenshot: 10_teacher_attendance.png

### Viewing Attendance Reports

1. On the Attendance page, click **View Reports**
2. Filter by:
   - **Course**: Select one of your courses
   - **Student** (optional): Select a specific student
   - **Date Range**: Choose the period

3. Click **Generate Report**
4. The report will show:
   - Attendance percentage for each student
   - Total absences
   - Class dates and attendance status

Use this report to identify students who may need additional support or who are missing too many classes.

---

## Student Guide

### Dashboard Overview

When you log in as a student, you will see the Student Dashboard with:

- **Enrolled Courses**: List of courses you are currently taking with:
  - Course name and code
  - Instructor name
  - Start and end dates

- **My Attendance**: Your overall attendance percentage and breakdown by course

- **Recent Grades**: Your latest grades across all courses

- **Profile Summary**: Quick link to your profile information

See screenshot: 11_student_dashboard.png

### Viewing Your Profile

1. From the student dashboard, click **Profile** in the navigation menu
2. You will see your personal information:
   - Full name
   - Email address
   - Date of birth
   - Phone number
   - Address
   - Registration date
   - Account status

3. Your profile is read-only except for the sections noted below

### Editing Your Profile

#### Updating Contact Details

1. On your Profile page, click **Edit Profile** or **Edit Contact Details**
2. Update the following information:
   - **Phone Number**: Enter your contact number
   - **Address**: Update your residential address
   - **Emergency Contact**: Add or update emergency contact information

3. Click **Save** to apply changes

See screenshot: 12_student_profile.png

#### Changing Your Password

1. On your Profile page, click **Change Password**
2. Fill in the password fields:
   - **Current Password**: Enter your existing password
   - **New Password**: Create a new password (minimum 8 characters)
   - **Confirm New Password**: Re-enter the new password

3. Click **Update Password**
4. You will be logged out and need to log in again with your new password

**Note:** For security reasons, choose a strong password with a mix of uppercase, lowercase, numbers, and special characters.

---

## Common Tasks

### Changing Your Password

#### For All Users

1. Log in to your account
2. Click on your **Profile** or **Account** link (usually in the top-right corner)
3. Select **Change Password** or **Security Settings**
4. Enter your **Current Password**
5. Enter your **New Password** (minimum 8 characters recommended)
6. Confirm your **New Password** by typing it again
7. Click **Update Password** or **Save Changes**
8. You will be logged out and can log in with your new password

**Password Security Tips:**
- Use at least 8 characters
- Include uppercase letters (A-Z)
- Include lowercase letters (a-z)
- Include numbers (0-9)
- Include special characters (!@#$%^&*)
- Do not use words from a dictionary
- Do not share your password with others

### Understanding Grade Letters

The SMS uses a five-letter grading system:

| Grade | Mark Range | Meaning | GPA |
|-------|-----------|---------|-----|
| **HD** | 85-100 | High Distinction | 4.0 |
| **D** | 75-84 | Distinction | 3.5 |
| **C** | 65-74 | Credit | 3.0 |
| **P** | 50-64 | Pass | 2.0 |
| **F** | 0-49 | Fail | 0.0 |

**Interpretation:**
- **HD (High Distinction)**: Excellent performance, mastery of subject
- **D (Distinction)**: Very good performance, strong understanding
- **C (Credit)**: Good performance, solid understanding
- **P (Pass)**: Satisfactory performance, minimum requirement met
- **F (Fail)**: Performance below acceptable standard

### Reading Attendance Percentage

#### What Does Attendance % Mean?

Your attendance percentage shows what proportion of classes you have attended out of the total number of classes held.

#### How Is It Calculated?

```
Attendance % = (Classes You Attended / Total Classes Held) × 100
```

**Example:**
- Total classes held: 20
- Classes you attended: 18
- Attendance: (18 ÷ 20) × 100 = **90%**

#### Typical Expectations

- **90-100%**: Excellent attendance
- **80-89%**: Good attendance
- **70-79%**: Satisfactory attendance (may be monitored)
- **Below 70%**: Poor attendance (may require intervention)

**Note:** Some courses may have mandatory minimum attendance requirements. Check your course syllabus for specific policies.

---

## Troubleshooting

### Common Issues and Solutions

#### Issue 1: "Invalid Email or Password" Error

**Symptom:** You enter your credentials but receive an error message.

**Solution:**
1. Check that you are using the correct email address (not just your name)
2. Ensure CAPS LOCK is off when typing your password
3. Verify you are typing the password correctly (passwords are case-sensitive)
4. If you have forgotten your password, contact your administrator for a password reset
5. Ensure your account has been activated (check your email for activation links)

**See screenshot: 01_login.png**

#### Issue 2: "Access Denied" or "Permission Denied" Error

**Symptom:** You log in successfully but see an error saying you don't have permission to access a page.

**Solution:**
1. Verify you are using the correct account (admin, teacher, or student)
2. Teachers can only manage their own courses and grades
3. Students can only view their own enrollment and grades
4. Administrators have access to all sections
5. If you believe this is an error, contact your administrator

#### Issue 3: Page Loads Slowly or Times Out

**Symptom:** Pages take a long time to load or show "Request Timeout" error.

**Solution:**
1. Check your internet connection
2. Refresh the page using F5 or the browser refresh button
3. Clear your browser cache (Ctrl+Shift+Delete)
4. Try using a different browser (Chrome, Firefox, Safari, Edge)
5. Wait a few minutes and try again
6. If the problem persists, contact your system administrator

#### Issue 4: Cannot See My Grades or Attendance

**Symptom:** Your grades or attendance information is not displaying.

**Solution:**
1. Ensure you are logged in with the correct account
2. Check that you are enrolled in the course (visible on your dashboard)
3. Ask your teacher to verify they have entered your grades
4. Wait 24 hours for system updates to process
5. Try clearing your browser cache (Ctrl+Shift+Delete)
6. Log out and log back in
7. Contact your teacher or administrator if the issue persists

#### Issue 5: Cannot Add or Edit Records

**Symptom:** The "Save" button doesn't work or appears disabled.

**Solution:**
1. Ensure all required fields are filled in (marked with *)
2. Check that email addresses are in the correct format (user@domain.com)
3. Verify that passwords meet minimum requirements (8+ characters)
4. Ensure you have the correct role/permissions to make this change
5. Try refreshing the page and attempting again
6. Close other browser tabs and try again
7. Use a different browser if the problem continues

#### Issue 6: Enrollment Not Appearing

**Symptom:** A student enrolled in a course is not showing in the course list.

**Solution:**
1. Verify the enrollment was saved by checking the Enrollment list
2. Refresh the course page to see updated information
3. Check the course date range—the student should be enrolled within the course dates
4. Ensure the student is actively enrolled (status = Active)
5. Wait a few minutes for the system to process the change
6. If using filters, ensure they are showing all records

#### Issue 7: Grades Not Calculating Automatically

**Symptom:** You entered marks but the letter grade did not appear or is incorrect.

**Solution:**
1. Ensure marks are entered as a number between 0-100
2. Check the grading scale:
   - 85-100 = HD
   - 75-84 = D
   - 65-74 = C
   - 50-64 = P
   - 0-49 = F
3. Refresh the page to see the calculated grade
4. Log out and log back in
5. Contact your administrator if grades still don't calculate correctly

#### Issue 8: Forgotten Password

**Symptom:** You cannot remember your password and cannot log in.

**Solution:**
1. On the login page, look for a **"Forgot Password?"** link
2. Click the link and enter your email address
3. Check your email for a password reset link
4. Follow the link and create a new password
5. Log in with your new password
6. If no password reset link is available, contact your administrator to reset your password

See screenshot: 01_login.png

#### Issue 9: Cannot Enroll in a Course

**Symptom:** You try to enroll but get an error or the course doesn't appear in the list.

**Solution:**
1. Verify the course is currently open for enrollment (not closed or in the past)
2. Check that you are not already enrolled in the course
3. Ensure the course has available spots (some courses may be full)
4. Contact your administrator if:
   - The course is not appearing in the list
   - You receive an enrollment error
   - The course is full and you need to be added

#### Issue 10: Browser Compatibility Issues

**Symptom:** The SMS doesn't work properly in your browser.

**Solution:**
1. Try using a modern browser:
   - Google Chrome (recommended)
   - Mozilla Firefox
   - Microsoft Edge
   - Apple Safari
2. Update your browser to the latest version
3. Clear browser cache and cookies
4. Disable browser extensions that may interfere (ad blockers, password managers)
5. Try in private/incognito mode
6. Contact your administrator if issues persist

---

## Support and Contact

If you encounter issues not covered in this manual:

1. Check the **Troubleshooting** section above
2. Contact your course teacher or department
3. Reach out to your system administrator or IT department
4. Provide the error message and steps you took when reporting issues

---

**Document Version:** 1.0  
**Last Updated:** April 2026  
**System:** Student Management System (SMS) v1.0
