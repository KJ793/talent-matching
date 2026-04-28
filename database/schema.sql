-- Intelligent Talent Matching Platform — Initial schema (Phase 1)
-- This schema reflects the original project specification.
-- It will be modified later (see migrations/) when the Week 8 changes land.

CREATE DATABASE IF NOT EXISTS talent_matching CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE talent_matching;

-- Base users table. Both candidates and employers share this.
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('candidate', 'employer') NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Candidate-specific profile data (1:1 with users where role='candidate').
CREATE TABLE candidates (
    user_id           INT PRIMARY KEY,
    full_name         VARCHAR(255) NOT NULL,
    contact           VARCHAR(255),
    education         VARCHAR(255),
    field_of_study    VARCHAR(255),
    years_experience  INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Employer-specific profile data (1:1 with users where role='employer').
CREATE TABLE employers (
    user_id      INT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    company_info TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Job postings created by employers.
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
