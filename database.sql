-- ============================================
-- Hospital Patient Management System - Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS hospital_mgmt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_mgmt;

-- Users Table (Admin / Doctor / Receptionist)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist') DEFAULT 'receptionist',
    specialization VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown') DEFAULT 'Unknown',
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    emergency_contact_name VARCHAR(100) DEFAULT NULL,
    emergency_contact_phone VARCHAR(20) DEFAULT NULL,
    department_id INT DEFAULT NULL,
    assigned_doctor_id INT DEFAULT NULL,
    status ENUM('active','discharged','critical','under_observation') DEFAULT 'active',
    photo VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    admitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    discharged_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_doctor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FULLTEXT INDEX ft_patient (first_name, last_name, patient_id, phone, city)
);

-- Medical Records
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT DEFAULT NULL,
    diagnosis TEXT DEFAULT NULL,
    prescription TEXT DEFAULT NULL,
    report_file VARCHAR(255) DEFAULT NULL,
    visit_date DATE NOT NULL,
    next_visit_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Activity Log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity VARCHAR(50) NOT NULL,
    entity_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- Seed Data
-- ============================================

INSERT INTO departments (name, description) VALUES
('Cardiology', 'Heart and cardiovascular system'),
('Neurology', 'Brain, spine and nervous system'),
('Orthopedics', 'Bones, joints and muscles'),
('Pediatrics', 'Medical care for children'),
('General Medicine', 'General health and common illnesses'),
('Emergency', 'Emergency and trauma care'),
('Gynecology', 'Women health and maternity');

-- Password for all: Admin@1234 (hash: password)
INSERT INTO users (name, email, password, role, specialization, phone) VALUES
('Dr. Admin', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '9876543210'),
('Dr. Ramesh Kumar', 'ramesh@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Cardiology', '9845678901'),
('Dr. Priya Nair', 'priya@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Neurology', '9823456789'),
('Receptionist Mary', 'mary@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'receptionist', NULL, '9812345678');

-- Sample patients
INSERT INTO patients (patient_id, first_name, last_name, date_of_birth, gender, blood_group, phone, email, city, state, department_id, assigned_doctor_id, status, created_by) VALUES
('PID-0001', 'Arjun', 'Sharma', '1985-06-15', 'male', 'O+', '9876543211', 'arjun@email.com', 'Chennai', 'Tamil Nadu', 1, 2, 'active', 1),
('PID-0002', 'Sunita', 'Patel', '1990-03-22', 'female', 'A+', '9845678902', 'sunita@email.com', 'Coimbatore', 'Tamil Nadu', 2, 3, 'under_observation', 1),
('PID-0003', 'Vikram', 'Singh', '1978-11-10', 'male', 'B+', '9823456780', NULL, 'Madurai', 'Tamil Nadu', 5, 2, 'active', 1),
('PID-0004', 'Lakshmi', 'Devi', '1995-08-05', 'female', 'AB+', '9812345679', NULL, 'Salem', 'Tamil Nadu', 3, 3, 'critical', 1),
('PID-0005', 'Ravi', 'Krishnan', '1960-01-30', 'male', 'O-', '9867891234', NULL, 'Trichy', 'Tamil Nadu', 1, 2, 'discharged', 1);
