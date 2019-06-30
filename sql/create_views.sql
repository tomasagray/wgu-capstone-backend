# Drop views
# =========================================
# Silos
DROP VIEW IF EXISTS student_term_courses;
DROP VIEW IF EXISTS student_data;
DROP VIEW IF EXISTS faculty_data;
DROP VIEW IF EXISTS course_data;
# Reporting
DROP VIEW IF EXISTS course_status_distribution;
DROP VIEW IF EXISTS enrollments_per_month;
DROP VIEW IF EXISTS students_per_course;


# Access silos
# =========================================
# Term courses
CREATE VIEW student_term_courses AS
SELECT
    t.student_id,
    tc.term_id,
    tc.course_id,
    tc.start_date,
    tc.end_date,
    cs.label AS course_status,
    c.title,
    c.course_number,
    c.credits,
    cm.mentor_id
FROM term_courses tc
    JOIN courses c
      ON tc.course_id = c.course_id
    JOIN terms t
      ON tc.term_id = t.term_id
    JOIN course_status cs
      ON tc.course_status_id = cs.course_status_id
    LEFT JOIN course_mentors cm
      ON c.course_id = cm.course_id;

# Student data
CREATE VIEW student_data AS
SELECT
u.user_id,
first_name,
last_name,
email,
phone,
building_number,
street,
unit_number,
city,
state,
post_code,
image_uri
FROM users u
JOIN user_types ut
    ON u.user_type_id = ut.type_id
LEFT JOIN addresses a
    ON u.user_id = a.user_id
LEFT JOIN user_images ui
    ON u.user_id = ui.user_id
WHERE ut.label = 'student';

# Faculty data
CREATE VIEW faculty_data AS
SELECT
    u.user_id,
    first_name,
    last_name,
    email,
    phone,
    building_number,
    street,
    unit_number,
    city,
    state,
    post_code,
    image_uri
FROM users u
JOIN user_types ut
  ON u.user_type_id = ut.type_id
LEFT JOIN addresses a
  ON u.user_id = a.user_id
LEFT JOIN user_images ui
  ON u.user_id = ui.user_id
WHERE ut.label = 'faculty';

# Course data
CREATE VIEW course_data AS
SELECT
    c.course_id,
    title,
    course_number,
    credits,
    mentor_id,
    CONCAT(first_name, ' ', last_name) AS mentor_name,
    email AS mentor_email,
    phone AS mentor_phone
FROM courses c
LEFT JOIN course_mentors cm
  ON c.course_id = cm.course_id
LEFT JOIN users u
  ON cm.mentor_id = u.user_id;


# Reporting
# ===================================================

# Student reports
# ------------------------------------
# Course status
CREATE VIEW course_status_distribution AS
SELECT
    SUM(
        CASE WHEN stc.course_status = 'planned'
             THEN 1
             ELSE 0
        END) AS 'planned',
    SUM(
        CASE WHEN stc.course_status = 'completed'
             THEN 1
             ELSE 0
        END) AS 'completed',
    SUM(
        CASE WHEN stc.course_status = 'in_progress'
             THEN 1
             ELSE 0
        END) AS 'in_progress',
    SUM(
        CASE WHEN stc.course_status = 'dropped'
             THEN 1
             ELSE 0
        END) AS 'dropped'
FROM student_term_courses AS stc;

# Enrollments / month
CREATE VIEW enrollments_per_month AS
SELECT
    MONTHNAME(start_date) AS 'month',
    COUNT(start_date) AS 'terms'
FROM terms
WHERE YEAR(start_date) = YEAR(CURDATE())
GROUP BY MONTH(start_date);

# Students / course
CREATE VIEW students_per_course AS
SELECT
    course_id,
    title,
    course_number,
    COUNT(student_id) AS 'students'
FROM student_term_courses
GROUP BY course_id;

# Security reports
# ---------------------------
