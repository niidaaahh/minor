CREATE DATABASE IF NOT EXISTS careadmin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE careadmin;

-- ── admin_accounts ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_accounts (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username  VARCHAR(80)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL,        -- PHP password_hash() / bcrypt
  full_name VARCHAR(120) NOT NULL DEFAULT 'Super Admin',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default login: admin / admin123
-- Hash produced by: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO admin_accounts (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin')
ON DUPLICATE KEY UPDATE username = username;

-- ── users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    VARCHAR(20)  NOT NULL UNIQUE,
  name       VARCHAR(120) NOT NULL,
  role       ENUM('Elderly','Caregiver','Admin') NOT NULL,
  phone      VARCHAR(30)  NOT NULL DEFAULT '',
  status     ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (user_id, name, role, phone, status) VALUES
('RS-74021', 'Ramesh Sharma',    'Elderly',   '+91 98765 43210', 'Active'),
('PS-20031', 'Priya Sharma',     'Caregiver', '+91 97654 32109', 'Active'),
('EK-65012', 'Eleanor Kim',      'Elderly',   '+91 99001 12233', 'Inactive'),
('HT-30041', 'Harold Thompson',  'Elderly',   '+91 91234 56789', 'Active'),
('AF-55091', 'Agnes Fernandez',  'Elderly',   '+91 90001 23456', 'Active'),
('DC-44062', 'Dorothy Chang',    'Elderly',   '+91 88765 43210', 'Active'),
('JM-10073', 'John Mathew',      'Caregiver', '+91 77654 32109', 'Active')
ON DUPLICATE KEY UPDATE user_id = user_id;

-- ── alerts ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS alerts (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  patient_name VARCHAR(120) NOT NULL,
  type         VARCHAR(80)  NOT NULL,
  severity     ENUM('Critical','Warning','Low') NOT NULL,
  status       ENUM('Open','Resolved') NOT NULL DEFAULT 'Open',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at  DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO alerts (patient_name, type, severity, status, created_at) VALUES
('Eleanor Kim',     'Critical BP',       'Critical', 'Open',     '2025-02-25 10:02:00'),
('Harold Thompson', 'Missed Medication', 'Warning',  'Open',     '2025-02-25 09:15:00'),
('Ramesh Sharma',   'High Heart Rate',   'Warning',  'Resolved', '2025-02-24 18:40:00'),
('Agnes Fernandez', 'Fall Detection',    'Critical', 'Resolved', '2025-02-23 14:22:00'),
('John Mathew',     'Missed Medication', 'Low',      'Resolved', '2025-02-22 07:55:00'),
('Dorothy Chang',   'BP Spike',          'Critical', 'Open',     '2025-02-21 22:10:00');
