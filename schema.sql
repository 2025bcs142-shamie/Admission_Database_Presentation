DROP DATABASE IF EXISTS admissions_db;
CREATE DATABASE admissions_db;
USE admissions_db;

CREATE TABLE Programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    duration_years INT NOT NULL,
    available_slots INT NOT NULL
);

CREATE TABLE Applicants (
    applicant_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    nationality VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Qualifications (
    qualification_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    school_name VARCHAR(150) NOT NULL,
    grade_average DECIMAL(5,2) NOT NULL,
    year_completed YEAR NOT NULL,
    certificate_type VARCHAR(100) NOT NULL,
    FOREIGN KEY (applicant_id) REFERENCES Applicants(applicant_id) ON DELETE CASCADE
);

CREATE TABLE Documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_name VARCHAR(150) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES Applicants(applicant_id) ON DELETE CASCADE
);

CREATE TABLE Applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    program_id INT NOT NULL,
    application_date DATE NOT NULL,
    status ENUM('Pending', 'Admitted') DEFAULT 'Pending',
    FOREIGN KEY (applicant_id) REFERENCES Applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES Programs(program_id) ON DELETE CASCADE
);

CREATE TABLE Admissions (
    admission_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    admission_date DATE NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    remarks TEXT,
    FOREIGN KEY (application_id) REFERENCES Applications(application_id) ON DELETE CASCADE
);

ALTER TABLE Applicants ADD password_hash VARCHAR(255) NOT NULL AFTER email;

CREATE TABLE Admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100)
);

ALTER TABLE Applications 
    MODIFY status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    ADD admin_remarks TEXT NULL;

ALTER TABLE Documents 
    ADD application_id INT AFTER applicant_id,
    ADD FOREIGN KEY (application_id) REFERENCES Applications(application_id) ON DELETE CASCADE;

INSERT INTO Programs (program_name, department, duration_years, available_slots) VALUES
('Computer Science', 'Science', 4, 50),
('Business Administration', 'Business', 3, 30);
