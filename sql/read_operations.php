<?php
# Global read operations
# =====================================================
$_SQL_R =
[
    # User data
    'get_user_data_from_email' =>
        "SELECT * FROM users WHERE email = :email",
    'get_user_data_from_id' =>
        "SELECT * FROM users WHERE user_id = :user_id",

    # Student data
    # --------------------------------
    'get_all_students' =>
        "SELECT * FROM student_data",
    'get_student_data' =>
        "SELECT * FROM student_data WHERE user_id = :user_id",

    # Terms for student
    # ---------------------------
    'get_student_terms' =>
        "SELECT * FROM terms WHERE student_id = :student_id",

    # Courses for term for student
    # ---------------------------
    'get_term_courses' =>
        "SELECT * FROM student_term_courses stc
            WHERE stc.term_id = :term_id",

    # Faculty data
    # --------------------------------------
    'get_all_faculty' =>
        "SELECT * FROM faculty_data",
    'get_faculty_data' =>
        "SELECT * FROM faculty_data WHERE user_id = :user_id",

    # Get course data for courses with specified mentor
    'get_mentor_courses' =>
        "SELECT * FROM course_data WHERE mentor_id = :mentor_id",

    # Curriculum
    # -----------------------------------------
    # Courses with mentor data
    'get_all_courses' =>
        "SELECT * FROM course_data",
    'get_course' =>
        "SELECT * FROM course_data WHERE course_id = :course_id",

    # Assessments for course
    'get_course_assessments' =>
        "SELECT assessment_id, course_id, title, items, label AS type
         FROM assessments 
         LEFT JOIN assessment_types a on assessments.type_id = a.assessment_type_id
         WHERE course_id = :course_id",

    # Notes for course
    'get_course_notes' =>
        "SELECT * FROM notes WHERE course_id = :course_id",


    # Documents
    # --------------------------------------
    # Get all documents
    'get_all_documents' =>
        "SELECT * FROM documents ORDER BY uploaded DESC",
    # Get a specific document
    'get_document' =>
        "SELECT * FROM documents WHERE document_id = :document_id",


    # Reports
    # -------------------------------------
    'get_course_status_report' =>
        "SELECT * FROM course_status_distribution",
    'get_students_per_course_report' =>
        "SELECT * FROM students_per_course",
    'get_enrollments_month_report' =>
        "SELECT * FROM enrollments_per_month"
];
