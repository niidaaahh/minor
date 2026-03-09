-- ============================================================
--  Elderly Care App  –  Database Setup Script
--  Run this in phpMyAdmin or MySQL CLI before using the app
-- ============================================================

CREATE DATABASE IF NOT EXISTS elderly_care_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE elderly_care_db;

-- ──────────────────────────────────────────────
--  1. USERS  (core accounts & roles)
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(120) NOT NULL,
    role          ENUM('user','caregiver','admin') NOT NULL DEFAULT 'user',
    email         VARCHAR(150) DEFAULT NULL,
    phone         VARCHAR(20)  DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_username (username)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  2. USER PROFILES  (extended info)
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS user_profiles (
    id                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                INT UNSIGNED NOT NULL UNIQUE,
    date_of_birth          DATE         DEFAULT NULL,
    gender                 ENUM('male','female','other') DEFAULT NULL,
    address                TEXT         DEFAULT NULL,
    emergency_contact_name VARCHAR(120) DEFAULT NULL,
    emergency_contact_phone VARCHAR(20) DEFAULT NULL,
    blood_type             VARCHAR(5)   DEFAULT NULL,
    allergies              TEXT         DEFAULT NULL,
    medical_notes          TEXT         DEFAULT NULL,
    profile_photo_url      VARCHAR(500) DEFAULT NULL,
    created_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  3. CAREGIVER ASSIGNMENTS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS caregiver_assignments (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    caregiver_id  INT UNSIGNED NOT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    assigned_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assignment (user_id, caregiver_id),
    FOREIGN KEY (user_id)      REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (caregiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_caregiver (caregiver_id)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  4. MEDICINES
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS medicines (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL,
    medicine_name     VARCHAR(150) NOT NULL,
    dosage            VARCHAR(80)  NOT NULL,
    frequency         VARCHAR(80)  NOT NULL,   -- e.g. "twice daily", "every 8 hours"
    scheduled_times   VARCHAR(200) DEFAULT NULL, -- comma-separated: "08:00,20:00"
    start_date        DATE         DEFAULT NULL,
    end_date          DATE         DEFAULT NULL,
    instructions      TEXT         DEFAULT NULL,
    prescribing_doctor VARCHAR(120) DEFAULT NULL,
    is_active         TINYINT(1)   NOT NULL DEFAULT 1,
    added_by          INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_medicine (user_id)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  5. MEDICATION LOGS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS medication_logs (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    medicine_id   INT UNSIGNED NOT NULL,
    scheduled_time DATETIME    NOT NULL,
    taken_time    DATETIME     DEFAULT NULL,
    status        ENUM('taken','missed','pending','skipped') NOT NULL DEFAULT 'pending',
    notes         TEXT         DEFAULT NULL,
    logged_by     INT UNSIGNED DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_dose (medicine_id, scheduled_time),
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (logged_by)   REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_medicine_date (medicine_id, scheduled_time)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  6. HEALTH READINGS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS health_readings (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    reading_type  ENUM('blood_pressure','blood_sugar','weight','temperature','heart_rate','oxygen_level') NOT NULL,
    value         DECIMAL(8,2) DEFAULT NULL,   -- generic value (e.g. weight kg, sugar mg/dL)
    unit          VARCHAR(20)  DEFAULT NULL,
    systolic      DECIMAL(5,1) DEFAULT NULL,   -- blood pressure only
    diastolic     DECIMAL(5,1) DEFAULT NULL,   -- blood pressure only
    notes         TEXT         DEFAULT NULL,
    recorded_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    recorded_by   INT UNSIGNED DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_reading (user_id, reading_type, recorded_at)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  7. CHAT MESSAGES
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS chat_messages (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id    INT UNSIGNED NOT NULL,
    receiver_id  INT UNSIGNED NOT NULL,
    message      TEXT         NOT NULL,
    message_type ENUM('text','image','alert') NOT NULL DEFAULT 'text',
    is_read      TINYINT(1)   NOT NULL DEFAULT 0,
    read_at      DATETIME     DEFAULT NULL,
    sent_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation (sender_id, receiver_id, sent_at),
    INDEX idx_receiver_unread (receiver_id, is_read)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  8. SOS ALERTS
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sos_alerts (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL,
    alert_type       ENUM('sos','fall','medication','health') NOT NULL DEFAULT 'sos',
    message          TEXT         NOT NULL,
    latitude         DECIMAL(10,7) DEFAULT NULL,
    longitude        DECIMAL(10,7) DEFAULT NULL,
    status           ENUM('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
    triggered_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at      DATETIME     DEFAULT NULL,
    resolution_notes TEXT         DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sos (user_id, triggered_at),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────
--  9. SOS NOTIFICATIONS  (tracks per-caregiver)
-- ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sos_notifications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_id        INT UNSIGNED NOT NULL,
    caregiver_id    INT UNSIGNED NOT NULL,
    notified_at     DATETIME     DEFAULT NULL,
    is_acknowledged TINYINT(1)   NOT NULL DEFAULT 0,
    UNIQUE KEY uq_alert_cg (alert_id, caregiver_id),
    FOREIGN KEY (alert_id)     REFERENCES sos_alerts(id) ON DELETE CASCADE,
    FOREIGN KEY (caregiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  SEED DATA  –  demo accounts (change passwords in prod!)
-- ============================================================

-- Passwords are bcrypt hashes of the strings shown in comments

-- admin / Admin@1234
INSERT IGNORE INTO users (username, password_hash, full_name, role, email, phone) VALUES
('admin',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin',     'admin@elderlycare.local',     '0000000000');

-- NOTE: The hash above is the bcrypt of "password" (Laravel default).
-- Real seed values are inserted below with proper hashes.
DELETE FROM users WHERE username = 'admin';

-- Use PHP password_hash() equivalent values
-- admin123  →  $2y$10$...  (generated)
-- We insert via a safe placeholder and note to run setup_seed.php

INSERT IGNORE INTO users (id, username, password_hash, full_name, role, email, phone) VALUES
(1, 'admin',     '$2y$10$TKh8H1.PfbuSiOmqz4m46.q7LzONzH.VPPNZ9gLEt.IFGo.S1UxW2', 'System Administrator', 'admin',     'admin@elderlycare.local',     '0000000001'),
(2, 'caregiver1','$2y$10$TKh8H1.PfbuSiOmqz4m46.q7LzONzH.VPPNZ9gLEt.IFGo.S1UxW2', 'Sarah Johnson',        'caregiver', 'sarah@elderlycare.local',     '0000000002'),
(3, 'user1',     '$2y$10$TKh8H1.PfbuSiOmqz4m46.q7LzONzH.VPPNZ9gLEt.IFGo.S1UxW2', 'Robert Thompson',      'user',      'robert@elderlycare.local',    '0000000003');

-- NOTE: The hash above is for the password "secret" - run setup_seed.php to generate proper hashes

INSERT IGNORE INTO user_profiles (user_id, date_of_birth, gender, blood_type, medical_notes) VALUES
(3, '1950-04-15', 'male', 'O+', 'Diabetic, hypertension history. Regular monitoring required.');

INSERT IGNORE INTO caregiver_assignments (user_id, caregiver_id) VALUES (3, 2);

INSERT IGNORE INTO medicines (user_id, medicine_name, dosage, frequency, scheduled_times, instructions, prescribing_doctor, added_by) VALUES
(3, 'Metformin',  '500mg', 'Twice daily',  '08:00,20:00', 'Take with meals', 'Dr. Smith', 2),
(3, 'Lisinopril', '10mg',  'Once daily',   '09:00',       'Take in the morning', 'Dr. Smith', 2),
(3, 'Aspirin',    '81mg',  'Once daily',   '08:00',       'Take with food', 'Dr. Jones', 2);
