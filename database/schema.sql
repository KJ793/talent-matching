-- Intelligent Talent Matching Platform — Full schema after migration 001.
-- For new installs, you can run this file directly. For existing Phase 1
-- installs, run database/migrations/001_*.sql instead to preserve data.

CREATE DATABASE IF NOT EXISTS talent_matching CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE talent_matching;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('candidate', 'employer') NOT NULL,
    membership    ENUM('free', 'premium') NOT NULL DEFAULT 'free',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE candidates (
    user_id              INT PRIMARY KEY,
    full_name            VARCHAR(255) NOT NULL,
    contact              VARCHAR(255),
    education            VARCHAR(255),
    field_of_study       VARCHAR(255),
    years_experience     INT DEFAULT 0,
    skills               TEXT,
    work_experience      TEXT,
    preferred_work_mode  ENUM('Remote','On-site','Hybrid','Any') NOT NULL DEFAULT 'Any',
    preferred_location   VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE employers (
    user_id      INT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    company_info TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jobs (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    employer_id       INT NOT NULL,
    title             VARCHAR(255) NOT NULL,
    description       TEXT NOT NULL,
    required_education VARCHAR(255),
    required_skills   TEXT,
    years_experience  INT DEFAULT 0,
    work_mode         ENUM('Remote', 'On-site', 'Hybrid') DEFAULT 'On-site',
    location          VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
