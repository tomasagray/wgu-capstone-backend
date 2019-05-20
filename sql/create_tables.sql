# Remove old tables
# =============================================
DROP TABLE IF EXISTS terms;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS term_enrollments;
DROP TABLE IF EXISTS course_enrollments;


# Base entities
# ============================================
CREATE TABLE terms (
    term_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    title TEXT NOT NULL ,
    start_date DATE NOT NULL ,
    end_date DATE NOT NULL
);

CREATE TABLE courses (
    course_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    title TEXT NOT NULL,
    course_number VARCHAR(6) NOT NULL ,
    credits INT NOT NULL ,
    start_date DATE,
    end_date DATE,
    status VARCHAR(255)
);

CREATE TABLE assessments (
    assessment_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    title TEXT NOT NULL ,
    type VARCHAR(255) NOT NULL ,
    start_date DATE,
    end_date DATE
);

CREATE TABLE addresses (
    address_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    building_number VARCHAR(10) NOT NULL ,
    street TEXT NOT NULL ,
    unit_number VARCHAR(10) ,
    city TEXT,
    state VARCHAR(3),
    country TEXT,
    post_code VARCHAR(255)
);

CREATE TABLE images (
    image_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    image_uri TEXT NOT NULL ,
    height INT,
    width INT,
    format VARCHAR(32)
);

CREATE TABLE students (
    student_id VARCHAR(36)  NOT NULL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name  VARCHAR(255) NOT NULL,
    email      VARCHAR(255),
    phone      VARCHAR(10),
    address_id VARCHAR(36),
    image_id   VARCHAR(36),
    CONSTRAINT `fk_student_address`
        FOREIGN KEY (address_id) REFERENCES addresses (address_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT `fk_student_image`
        FOREIGN KEY (image_id) REFERENCES images(image_id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);


CREATE TABLE faculty (
    faculty_id VARCHAR(36)  NOT NULL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name  VARCHAR(255) NOT NULL,
    email      VARCHAR(255),
    phone      VARCHAR(10),
    address_id VARCHAR(36),
    image_id   VARCHAR(36),
    CONSTRAINT `fk_faculty_address`
      FOREIGN KEY (address_id) REFERENCES addresses (address_id)
          ON DELETE CASCADE
          ON UPDATE CASCADE,
    CONSTRAINT `fk_faculty_image`
      FOREIGN KEY (image_id) REFERENCES images(image_id)
          ON UPDATE CASCADE
          ON DELETE CASCADE
);


# Association entities
# ====================================================
CREATE TABLE course_enrollments (
    enrollment_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    student_id VARCHAR(36) NOT NULL ,
    course_id VARCHAR(36) NOT NULL ,
    start_date DATE NOT NULL ,
    end_date DATE NOT NULL,
    CONSTRAINT `fk_course_enrollment_student`
            FOREIGN KEY (student_id) REFERENCES students(student_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
    CONSTRAINT `fk_course_enrollment_course`
        FOREIGN KEY (course_id) REFERENCES courses(course_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE term_enrollments (
    enrollment_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    student_id VARCHAR(36) NOT NULL ,
    term_id VARCHAR(36) NOT NULL ,
    start_date DATE NOT NULL ,
    end_date DATE NOT NULL ,
    CONSTRAINT `fk_term_enrollment_student`
        FOREIGN KEY (student_id) REFERENCES students(student_id)
              ON UPDATE CASCADE
              ON DELETE CASCADE ,
    CONSTRAINT `fk_term_enrollment_term`
        FOREIGN KEY (term_id) REFERENCES terms(term_id)
              ON DELETE CASCADE
              ON UPDATE CASCADE
);