CREATE DATABASE cs;

USE cs;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE students (
    lrn VARCHAR(12) PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    email VARCHAR(100) NOT NULL UNIQUE,
    year VARCHAR(10),
    section VARCHAR(10),
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE parents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lrn_students VARCHAR(12) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    email VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (lrn_students) REFERENCES students(lrn)
);


CREATE TABLE teachers (
    id VARCHAR(12) PRIMARY KEY,   
    subject VARCHAR(100),
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);



CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    audience ENUM('students', 'parents', 'teachers', 'all') DEFAULT 'all'
);


CREATE TABLE subjects_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id VARCHAR(12) NOT NULL, 
    year_level VARCHAR(10) NOT NULL,
    section VARCHAR(10) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_lrn VARCHAR(12),
    subject_id INT,  
    date DATE,
    status ENUM('A', '') NOT NULL, 
    teacher_id VARCHAR(12),
    UNIQUE (student_lrn, subject_id, date, teacher_id),
    FOREIGN KEY (subject_id) REFERENCES subjects_sections(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);


CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_lrn VARCHAR(12) NOT NULL,
    subject_id INT NOT NULL,
    quarter ENUM('1st', '2nd', '3rd', '4th') NOT NULL,
    scores JSON NOT NULL,  -- Store scores in JSON format
    teacher_id VARCHAR(12) NOT NULL,
    FOREIGN KEY (student_lrn) REFERENCES students(lrn),
    FOREIGN KEY (subject_id) REFERENCES subjects_sections(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);


