USE sms_database;

-- 1 Admin user
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('admin', 'admin@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'admin', 1);

-- 3 Teacher users (IDs 2, 3, 4)
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('teacher1', 'teacher1@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'teacher', 1),
('teacher2', 'teacher2@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'teacher', 1),
('teacher3', 'teacher3@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'teacher', 1);

-- 10 Student users (IDs 5 through 14)
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('student1', 'student1@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student2', 'student2@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student3', 'student3@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student4', 'student4@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student5', 'student5@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student6', 'student6@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student7', 'student7@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student8', 'student8@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student9', 'student9@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1),
('student10', 'student10@sms.edu.au', '$2y$10$RhdUWV5z2Ut746OlGBi43.jTb7Q8JA2E4/udm/kd9HnfhSwEq/Bgu', 'student', 1);

-- 3 Teachers linked to users
INSERT INTO teachers (user_id, employee_number, first_name, last_name, department, phone, hire_date) VALUES
(2, 'EMP001', 'James', 'Wilson', 'Computer Science', '0412345678', '2020-02-15'),
(3, 'EMP002', 'Sarah', 'Chen', 'Information Technology', '0412345679', '2019-07-01'),
(4, 'EMP003', 'Michael', 'Brown', 'Business Analytics', '0412345680', '2021-01-10');

-- 10 Students linked to users
INSERT INTO students (user_id, student_number, first_name, last_name, date_of_birth, gender, phone, address, enrollment_year) VALUES
(5,  'STU001', 'Emma',    'Taylor',   '2002-03-15', 'female', '0423456781', '12 George St, Sydney NSW 2000', 2024),
(6,  'STU002', 'Liam',    'Anderson', '2001-07-22', 'male',   '0423456782', '45 Collins St, Melbourne VIC 3000', 2024),
(7,  'STU003', 'Olivia',  'Martinez', '2003-01-10', 'female', '0423456783', '8 Queen St, Brisbane QLD 4000', 2024),
(8,  'STU004', 'Noah',    'Thompson', '2002-11-05', 'male',   '0423456784', '23 King St, Perth WA 6000', 2024),
(9,  'STU005', 'Ava',     'Garcia',   '2001-09-18', 'female', '0423456785', '67 Flinders St, Adelaide SA 5000', 2023),
(10, 'STU006', 'William', 'Lee',      '2003-05-30', 'male',   '0423456786', '15 Murray St, Hobart TAS 7000', 2023),
(11, 'STU007', 'Sophia',  'Patel',    '2002-08-12', 'female', '0423456787', '34 Northbourne Ave, Canberra ACT 2600', 2023),
(12, 'STU008', 'James',   'Nguyen',   '2001-12-25', 'male',   '0423456788', '9 Elizabeth St, Sydney NSW 2000', 2025),
(13, 'STU009', 'Isabella','O''Brien', '2003-04-08', 'female', '0423456789', '56 Swanston St, Melbourne VIC 3000', 2025),
(14, 'STU010', 'Oliver',  'Kim',      '2002-06-20', 'male',   '0423456790', '22 Adelaide St, Brisbane QLD 4000', 2025);

-- 5 Courses assigned to teachers
INSERT INTO courses (course_code, course_name, description, credits, teacher_id, semester, year) VALUES
('ICT101', 'Introduction to Programming', 'Fundamentals of programming using Python. Covers variables, loops, functions, and basic data structures.', 3, 1, '1', 2025),
('ICT202', 'Database Systems', 'Design and implementation of relational databases using SQL and MySQL.', 4, 1, '1', 2025),
('ICT303', 'Web Development', 'Building dynamic web applications with HTML, CSS, JavaScript, and PHP.', 4, 2, '1', 2025),
('BUS101', 'Business Information Systems', 'Overview of how information systems support business processes and decision-making.', 3, 3, '2', 2025),
('ICT204', 'Networking Fundamentals', 'Introduction to computer networks, protocols, and network security basics.', 3, 2, '2', 2025);

-- 15 Enrollments (spread across students and courses)
INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES
(1, 1, '2025-02-15', 'active'),
(1, 2, '2025-02-15', 'active'),
(1, 3, '2025-02-15', 'active'),
(2, 1, '2025-02-16', 'active'),
(2, 3, '2025-02-16', 'active'),
(3, 2, '2025-02-17', 'active'),
(3, 4, '2025-02-17', 'active'),
(4, 1, '2025-02-18', 'active'),
(4, 5, '2025-02-18', 'active'),
(5, 3, '2025-02-19', 'active'),
(5, 4, '2025-02-19', 'completed'),
(6, 2, '2025-02-20', 'active'),
(7, 1, '2025-02-20', 'active'),
(8, 5, '2025-02-21', 'active'),
(9, 3, '2025-02-21', 'dropped');

-- 30 Grades spread across enrollments
INSERT INTO grades (enrollment_id, assessment_name, marks_obtained, max_marks, grade_letter, comments, recorded_at) VALUES
(1, 'Assignment 1', 88.00, 100.00, 'HD', 'Excellent work on all tasks.', '2025-03-15 10:00:00'),
(1, 'Mid-Term Exam', 76.00, 100.00, 'D', 'Good understanding of concepts.', '2025-04-10 14:00:00'),
(2, 'Assignment 1', 92.00, 100.00, 'HD', 'Outstanding database design.', '2025-03-16 10:00:00'),
(2, 'Quiz 1', 68.00, 100.00, 'C', 'Needs improvement on normalisation.', '2025-03-25 09:00:00'),
(3, 'Project Phase 1', 85.00, 100.00, 'HD', 'Well-structured HTML and CSS.', '2025-03-17 10:00:00'),
(3, 'Assignment 1', 72.00, 100.00, 'D', 'Good effort, minor issues with JS.', '2025-04-01 10:00:00'),
(4, 'Assignment 1', 65.00, 100.00, 'C', 'Satisfactory work.', '2025-03-15 11:00:00'),
(4, 'Mid-Term Exam', 58.00, 100.00, 'P', 'Passed but needs more practice.', '2025-04-10 14:30:00'),
(5, 'Project Phase 1', 90.00, 100.00, 'HD', 'Impressive web application.', '2025-03-18 10:00:00'),
(5, 'Assignment 1', 78.00, 100.00, 'D', 'Good PHP implementation.', '2025-04-02 10:00:00'),
(6, 'Assignment 1', 45.00, 100.00, 'F', 'Missing several requirements.', '2025-03-17 11:00:00'),
(6, 'Quiz 1', 82.00, 100.00, 'D', 'Much improved performance.', '2025-03-28 09:00:00'),
(7, 'Assignment 1', 71.00, 100.00, 'D', 'Good analysis of business systems.', '2025-03-19 10:00:00'),
(7, 'Mid-Term Exam', 55.00, 100.00, 'P', 'Basic understanding shown.', '2025-04-12 14:00:00'),
(8, 'Assignment 1', 88.00, 100.00, 'HD', 'Excellent programming logic.', '2025-03-15 12:00:00'),
(8, 'Quiz 1', 95.00, 100.00, 'HD', 'Perfect score on loops and arrays.', '2025-03-26 09:00:00'),
(9, 'Assignment 1', 62.00, 100.00, 'P', 'Meets minimum requirements.', '2025-03-18 12:00:00'),
(9, 'Mid-Term Exam', 70.00, 100.00, 'D', 'Improved from assignment.', '2025-04-10 15:00:00'),
(10, 'Project Phase 1', 80.00, 100.00, 'D', 'Good responsive design.', '2025-03-19 10:00:00'),
(10, 'Assignment 1', 73.00, 100.00, 'D', 'Solid JavaScript work.', '2025-04-03 10:00:00'),
(11, 'Assignment 1', 50.00, 100.00, 'P', 'Barely passed, needs more effort.', '2025-03-19 11:00:00'),
(12, 'Assignment 1', 85.00, 100.00, 'HD', 'Very good SQL queries.', '2025-03-20 10:00:00'),
(12, 'Quiz 1', 78.00, 100.00, 'D', 'Good understanding of joins.', '2025-03-29 09:00:00'),
(13, 'Assignment 1', 91.00, 100.00, 'HD', 'Excellent work.', '2025-03-20 11:00:00'),
(13, 'Mid-Term Exam', 87.00, 100.00, 'HD', 'Top performer.', '2025-04-10 16:00:00'),
(14, 'Assignment 1', 42.00, 100.00, 'F', 'Incomplete submission.', '2025-03-21 10:00:00'),
(14, 'Quiz 1', 55.00, 100.00, 'P', 'Some improvement needed.', '2025-03-30 09:00:00'),
(3, 'Mid-Term Exam', 83.00, 100.00, 'D', 'Solid web development skills.', '2025-04-11 14:00:00'),
(5, 'Quiz 1', 69.00, 100.00, 'C', 'Average performance on PHP quiz.', '2025-03-28 09:30:00'),
(10, 'Quiz 1', 76.00, 100.00, 'D', 'Good effort on responsive design quiz.', '2025-03-29 09:30:00');

-- 50 Attendance records spread over recent dates
-- Use enrollment IDs 1-14 and dates from March-April 2025
INSERT INTO attendance (enrollment_id, attendance_date, status, notes, recorded_at) VALUES
(1, '2025-03-03', 'present', NULL, '2025-03-03 09:00:00'),
(1, '2025-03-10', 'present', NULL, '2025-03-10 09:00:00'),
(1, '2025-03-17', 'absent', 'Medical certificate provided', '2025-03-17 09:00:00'),
(1, '2025-03-24', 'present', NULL, '2025-03-24 09:00:00'),
(1, '2025-03-31', 'late', 'Arrived 15 minutes late', '2025-03-31 09:15:00'),
(2, '2025-03-03', 'present', NULL, '2025-03-03 10:00:00'),
(2, '2025-03-10', 'present', NULL, '2025-03-10 10:00:00'),
(2, '2025-03-17', 'present', NULL, '2025-03-17 10:00:00'),
(2, '2025-03-24', 'absent', 'No reason given', '2025-03-24 10:00:00'),
(3, '2025-03-04', 'present', NULL, '2025-03-04 09:00:00'),
(3, '2025-03-11', 'present', NULL, '2025-03-11 09:00:00'),
(3, '2025-03-18', 'late', 'Bus delay', '2025-03-18 09:10:00'),
(3, '2025-03-25', 'present', NULL, '2025-03-25 09:00:00'),
(4, '2025-03-03', 'present', NULL, '2025-03-03 09:00:00'),
(4, '2025-03-10', 'absent', NULL, '2025-03-10 09:00:00'),
(4, '2025-03-17', 'present', NULL, '2025-03-17 09:00:00'),
(4, '2025-03-24', 'present', NULL, '2025-03-24 09:00:00'),
(5, '2025-03-04', 'present', NULL, '2025-03-04 09:00:00'),
(5, '2025-03-11', 'present', NULL, '2025-03-11 09:00:00'),
(5, '2025-03-18', 'present', NULL, '2025-03-18 09:00:00'),
(5, '2025-03-25', 'excused', 'Family emergency', '2025-03-25 09:00:00'),
(6, '2025-03-05', 'present', NULL, '2025-03-05 09:00:00'),
(6, '2025-03-12', 'absent', NULL, '2025-03-12 09:00:00'),
(6, '2025-03-19', 'present', NULL, '2025-03-19 09:00:00'),
(7, '2025-03-05', 'present', NULL, '2025-03-05 10:00:00'),
(7, '2025-03-12', 'present', NULL, '2025-03-12 10:00:00'),
(7, '2025-03-19', 'late', 'Traffic delay', '2025-03-19 10:15:00'),
(7, '2025-03-26', 'present', NULL, '2025-03-26 10:00:00'),
(8, '2025-03-03', 'present', NULL, '2025-03-03 09:00:00'),
(8, '2025-03-10', 'present', NULL, '2025-03-10 09:00:00'),
(8, '2025-03-17', 'present', NULL, '2025-03-17 09:00:00'),
(8, '2025-03-24', 'present', NULL, '2025-03-24 09:00:00'),
(9, '2025-03-05', 'absent', NULL, '2025-03-05 11:00:00'),
(9, '2025-03-12', 'present', NULL, '2025-03-12 11:00:00'),
(9, '2025-03-19', 'present', NULL, '2025-03-19 11:00:00'),
(10, '2025-03-04', 'present', NULL, '2025-03-04 09:00:00'),
(10, '2025-03-11', 'absent', 'Sick leave', '2025-03-11 09:00:00'),
(10, '2025-03-18', 'present', NULL, '2025-03-18 09:00:00'),
(10, '2025-03-25', 'present', NULL, '2025-03-25 09:00:00'),
(11, '2025-03-05', 'present', NULL, '2025-03-05 10:00:00'),
(11, '2025-03-12', 'present', NULL, '2025-03-12 10:00:00'),
(12, '2025-03-03', 'present', NULL, '2025-03-03 10:00:00'),
(12, '2025-03-10', 'present', NULL, '2025-03-10 10:00:00'),
(12, '2025-03-17', 'late', 'Arrived late', '2025-03-17 10:20:00'),
(12, '2025-03-24', 'present', NULL, '2025-03-24 10:00:00'),
(13, '2025-03-03', 'present', NULL, '2025-03-03 09:00:00'),
(13, '2025-03-10', 'excused', 'University event', '2025-03-10 09:00:00'),
(13, '2025-03-17', 'present', NULL, '2025-03-17 09:00:00'),
(14, '2025-03-05', 'absent', NULL, '2025-03-05 11:00:00'),
(14, '2025-03-12', 'present', NULL, '2025-03-12 11:00:00');
