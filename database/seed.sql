-- Sample seed data for development.
-- Passwords below all hash from "password123" using PHP's password_hash().
-- Generate fresh hashes if needed: php -r "echo password_hash('password123', PASSWORD_DEFAULT);"

USE talent_matching;

INSERT INTO users (email, password_hash, role) VALUES
('alice@example.com',  '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'candidate'),
('bob@example.com',    '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'candidate'),
('acme@example.com',   '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'employer'),
('globex@example.com', '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'employer');

INSERT INTO candidates (user_id, full_name, contact, education, field_of_study, years_experience) VALUES
(1, 'Alice Smith',  '0400 000 001', 'Bachelor', 'Computer Science', 2),
(2, 'Bob Johnson',  '0400 000 002', 'Master',   'Data Science',     5);

INSERT INTO employers (user_id, company_name, company_info) VALUES
(3, 'Acme Corp',   'A medium-sized engineering company.'),
(4, 'Globex Ltd',  'A large data analytics consultancy.');

INSERT INTO jobs (employer_id, title, description, required_education, required_skills, years_experience, work_mode, location) VALUES
(3, 'Junior Software Engineer', 'Build and maintain web applications. PHP and JavaScript experience preferred.', 'Bachelor', 'PHP, JavaScript, SQL', 1, 'Hybrid', 'Sydney'),
(3, 'DevOps Engineer',          'Manage CI/CD pipelines and cloud infrastructure.', 'Bachelor', 'AWS, Docker, Linux', 3, 'Remote', 'Sydney'),
(4, 'Data Analyst',              'Work with large datasets to produce insights for clients.', 'Bachelor', 'SQL, Python, Tableau', 2, 'On-site', 'Melbourne'),
(4, 'Senior Data Scientist',     'Lead modelling projects across multiple client engagements.', 'Master', 'Python, ML, Statistics', 5, 'Hybrid', 'Melbourne');
