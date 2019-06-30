# Base entities
# =================================================

# Users
# ------------------------------------
CREATE TABLE user_types (
    type_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY ,
    label TEXT NOT NULL
);
# Add types
INSERT INTO user_types(label)
    VALUES ('admin'), ('student'), ('faculty');

CREATE TABLE users (
    user_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    user_type_id INT NOT NULL ,
    password TEXT NOT NULL ,
    first_name TEXT NOT NULL ,
    last_name TEXT NOT NULL ,
    email TEXT,
    phone VARCHAR(10),
    CONSTRAINT `fk_user_type`
       FOREIGN KEY (user_type_id) REFERENCES user_types(type_id)
       ON UPDATE CASCADE
       ON DELETE CASCADE
);

# Create admin
INSERT INTO users(user_id, user_type_id, password, first_name, last_name, email, phone)
    VALUES('fe8527df-9ed9-4be9-9d00-d0bb89cd1132', 1, 'admin', 'admin', 'admin', 'admin', '');


# Courses
# =================================================
CREATE TABLE course_status (
    course_status_id INT AUTO_INCREMENT PRIMARY KEY ,
    label TEXT NOT NULL
);
# Status values
INSERT INTO course_status(label)
    VALUES ('planned'),('dropped'),('in_progress'),('completed');

CREATE TABLE courses (
    course_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    title TEXT NOT NULL,
    course_number VARCHAR(6) NOT NULL ,
    credits INT NOT NULL
);

# University data
# =================================================
CREATE TABLE documents(
  document_id VARCHAR(36) PRIMARY KEY ,
  title TEXT NOT NULL ,
  description TEXT,
  file_name TEXT NOT NULL ,
  file_type TEXT NOT NULL ,
  file_size INT NOT NULL ,
  uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


# Dependent entities
# =================================================
CREATE TABLE assessment_types (
    assessment_type_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY ,
    label TEXT NOT NULL
);
INSERT INTO assessment_types(label)
    VALUES ('objective'),('performance');

CREATE TABLE assessments (
     assessment_id VARCHAR(36) NOT NULL PRIMARY KEY ,
     course_id VARCHAR(36) NOT NULL ,
     title TEXT NOT NULL ,
     items TEXT,
     type_id INT NOT NULL ,
     CONSTRAINT `fk_assessment_course`
         FOREIGN KEY (course_id) REFERENCES courses(course_id)
         ON DELETE CASCADE
         ON UPDATE CASCADE,
     CONSTRAINT `fk_assessment_type`
         FOREIGN KEY (type_id) REFERENCES assessment_types(assessment_type_id)
         ON DELETE CASCADE
         ON UPDATE CASCADE
);

CREATE TABLE notes (
   note_id VARCHAR(36) NOT NULL PRIMARY KEY ,
   course_id VARCHAR(36) NOT NULL ,
   text TEXT NOT NULL ,
   updateDate DATE,
   CONSTRAINT `fk_note_course`
       FOREIGN KEY (course_id) REFERENCES courses(course_id)
           ON UPDATE CASCADE
           ON DELETE CASCADE
);

CREATE TABLE addresses (
    address_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    user_id VARCHAR(36) NOT NULL ,
    building_number VARCHAR(10) NOT NULL ,
    street TEXT NOT NULL ,
    unit_number VARCHAR(10) ,
    city TEXT,
    state VARCHAR(10),
    post_code VARCHAR(255),
    CONSTRAINT `fk_user_address_id`
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE user_images (
    image_id VARCHAR(36) NOT NULL PRIMARY KEY ,
    user_id VARCHAR(36) NOT NULL ,
    image_uri TEXT NOT NULL ,
    height INT,
    width INT,
    format VARCHAR(32),
    CONSTRAINT `fk_user_image`
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

# Association entities
# =================================================
CREATE TABLE terms (
    student_id VARCHAR(36) NOT NULL,
    term_id VARCHAR(36) NOT NULL PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    title TEXT,
    CONSTRAINT `fk_term_student`
        FOREIGN KEY (student_id) REFERENCES users(user_id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

CREATE TABLE term_courses (
  term_id VARCHAR(36) NOT NULL ,
  course_id VARCHAR(36) NOT NULL ,
  start_date DATE NOT NULL ,
  end_date DATE NOT NULL ,
  course_status_id INT NOT NULL ,
  CONSTRAINT `pk_term_courses` PRIMARY KEY (term_id, course_id),
  CONSTRAINT `fk_course_status`
      FOREIGN KEY (course_status_id) REFERENCES course_status(course_status_id)
          ON UPDATE CASCADE
          ON DELETE CASCADE,
  CONSTRAINT `fk_term_courses`
      FOREIGN KEY (term_id) REFERENCES terms(term_id)
          ON DELETE CASCADE
          ON UPDATE CASCADE ,
  CONSTRAINT `fk_courses_term`
      FOREIGN KEY (course_id) REFERENCES courses(course_id)
          ON DELETE CASCADE
          ON UPDATE CASCADE
);

CREATE TABLE course_mentors(
    course_id VARCHAR(36) PRIMARY KEY ,
    mentor_id VARCHAR(36) NOT NULL ,
    CONSTRAINT `fk_mentor_course_id`
        FOREIGN KEY (course_id) REFERENCES courses(course_id)
            ON UPDATE CASCADE
            ON DELETE CASCADE ,
    CONSTRAINT `fk_mentor_user_id`
        FOREIGN KEY (mentor_id) REFERENCES users(user_id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);