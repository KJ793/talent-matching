-- Migration 001: Add membership tier and extend candidate profile.
-- Triggered by the Week 8 requirement change.
--
-- Changes:
--   1. users: add `membership` column (free | premium)
--   2. candidates: add skills, work_experience, preferred_work_mode, preferred_location
--
-- This migration is additive and can be applied to an existing Phase 1 database
-- without dropping data. Run with:
--   mysql -u root -p talent_matching < database/migrations/001_add_membership_and_profile_fields.sql

USE talent_matching;

-- 1. Membership on the users table.
ALTER TABLE users
    ADD COLUMN membership ENUM('free', 'premium') NOT NULL DEFAULT 'free' AFTER role;

-- 2. Extended profile fields for candidates.
ALTER TABLE candidates
    ADD COLUMN skills              TEXT          AFTER years_experience,
    ADD COLUMN work_experience     TEXT          AFTER skills,
    ADD COLUMN preferred_work_mode ENUM('Remote','On-site','Hybrid','Any') NOT NULL DEFAULT 'Any' AFTER work_experience,
    ADD COLUMN preferred_location  VARCHAR(255)  AFTER preferred_work_mode;
