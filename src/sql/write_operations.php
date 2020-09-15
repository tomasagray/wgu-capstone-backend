<?php
/*
 * Copyright (c) 2020 Tomás Gray
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

# Write Operations
# ============================================================================
$_SQL_W =
[
    # Students
    # ----------------------------
    # Add
    'add_user' =>
        "INSERT INTO users(user_id, user_type_id, first_name, last_name, email, password, phone)
            VALUES (:user_id, (SELECT type_id FROM user_types WHERE label = :user_type), :first_name, :last_name, :email, :password, :phone)",
    # Add address
    'add_user_address' =>
        "INSERT INTO addresses(address_id, user_id, building_number, street, unit_number, city, state, post_code) 
            VALUES (:address_id, :user_id, :building_number, :street, :unit_number, :city, :state, :post_code)",
    # Remove
    'delete_user' =>
        "DELETE FROM users WHERE user_id = :user_id",
    # Update
    'update_user' =>
        "UPDATE users
            SET
                user_type_id = (SELECT type_id FROM user_types WHERE label = :user_type),
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                password = :password
            WHERE
                user_id = :user_id",
    # Update user address
    'update_user_address' =>
        "UPDATE addresses
            SET
                user_id = :user_id,
                building_number = :building_number,
                street = :street,
                unit_number = :unit_number,
                city = :city,
                state = :state,
                post_code = :post_code
            WHERE
                address_id = :address_id",
    # Update user image
    'update_user_image' =>
        "UPDATE user_images
            SET
                image_uri = :image_uri,
                height = :height,
                width = :width,
                format = :format
            WHERE
                user_id = :user_id",

    # Terms
    # -----------------------------
    # Enroll in term
    'enroll_student_in_term' =>
        "INSERT INTO terms(student_id, term_id, start_date, end_date, title)
            VALUES (:student_id, :term_id, :start_date, :end_date, :title)",

    # Update term
    'update_term' =>
        "UPDATE terms 
            SET
                start_date = :start_date,
                end_date = :end_date
            WHERE
                term_id = :term_id",

    # Un-enroll in term
    'unenroll_student_from_term' =>
        "DELETE FROM terms WHERE term_id = :term_id",

    # Associate class with term
    'assoc_class_with_term' =>
        "INSERT INTO term_courses(term_id, course_id, start_date, end_date, course_status_id)
         VALUES (:term_id, :course_id, :start_date, :end_date, 
         (SELECT course_status_id FROM course_status WHERE label = :status))",

    # Disassociate all classes from term
    'clear_term_courses' =>
        "DELETE FROM term_courses WHERE term_id = :term_id",

    # Disassociate class with term
    'disassoc_class_with_term' =>
        "DELETE FROM term_courses
        WHERE term_id = :term_id
          AND course_id = :course_id",

    # Faculty
    # ------------------------------------------------------
    # Assign to course mentorship
    'assign_mentor' =>
        "INSERT INTO course_mentors(course_id, mentor_id) VALUES (:course_id, :mentor_id)",
    # Remove from course mentorship
    'remove_course_mentors' =>
        "DELETE FROM course_mentors WHERE course_id = :course_id",
    # Remove all course mentorships
    'clear_mentorships' =>
        "DELETE FROM course_mentors WHERE mentor_id = :mentor_id",



    # Curriculum
    # ============================================================================
    # Courses
    # ----------------------
    # Add
    'add_course' =>
        "INSERT INTO courses(course_id, title, course_number, credits)
        VALUES (:course_id, :title, :course_number, :credits)",
    # Remove
    'remove_course' =>
        "DELETE FROM courses WHERE course_id = :course_id",
    # Update
    'update_course' =>
        "UPDATE courses
        SET
            title = :title,
            course_number = :course_number,
            credits = :credits
        WHERE
            course_id = :course_id",

    # Assessments
    # ------------------------
    # Add
    'add_assessment' =>
        "INSERT INTO assessments(assessment_id, course_id, title, items, type_id)
         VALUES (:assessment_id, :course_id, :title, :items, 
         (SELECT assessment_type_id FROM assessment_types WHERE label = :type))",
    # Remove
    'remove_assessment' =>
        "DELETE FROM assessments WHERE assessment_id = :assessment_id",
    # Update
    'update_assessment' =>
        "UPDATE assessments
        SET
            title = :title,
            items = :items,
            type_id = (SELECT assessment_type_id FROM assessment_types WHERE label = :type)
        WHERE
            assessment_id = :assessment_id
        AND 
            course_id = :course_id",

    # Notes
    # ----------------------------
    # TODO!

    # Documents
    # -------------------------------------
    # Add a new document
    'add_document' =>
        "INSERT INTO documents(document_id, title, description, file_name, file_type, file_size) 
            VALUES (:document_id, :title, :description, :file_name, :file_type, :file_size)",
    # Update existing document data
    'update_document' =>
        "UPDATE documents 
        SET
            title = :title,
            description = :description,
            file_name = :file_name,
            file_type = :file_type,
            file_size = :file_size
        WHERE document_id = :document_id",
    # Delete document
    'remove_document' =>
        "DELETE FROM documents WHERE document_id = :document_id"
];
