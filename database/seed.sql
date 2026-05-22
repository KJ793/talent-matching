-- Sample seed data for development (post-migration).
-- Passwords below all hash from "password123" using PHP's password_hash().

USE talent_matching;

INSERT INTO users (email, password_hash, role, membership) VALUES
('alice@example.com',  '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'candidate', 'free'),
('bob@example.com',    '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'candidate', 'premium'),
('acme@example.com',   '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'employer',  'free'),
('globex@example.com', '$2y$10$2gvVVS5hITutpBVqLd/Clek0Palevv0coooj3cDRf4yY8lbD7KH8C', 'employer',  'premium');

INSERT INTO candidates (user_id, full_name, contact, education, field_of_study, years_experience,
                        skills, work_experience, preferred_work_mode, preferred_location) VALUES
(1, 'Alice Smith', '0400 000 001', 'Bachelor', 'Computer Science', 2,
    'PHP, JavaScript, SQL, HTML, CSS',
    'Junior Developer at Foo Pty Ltd (2 years)',
    'Hybrid', 'Sydney'),
(2, 'Bob Johnson', '0400 000 002', 'Master', 'Data Science', 5,
    'Python, SQL, Tableau, Machine Learning, Statistics',
    'Data Analyst at Bar Inc (3 years), Data Scientist at Baz (2 years)',
    'Remote', 'Melbourne');

INSERT INTO employers (user_id, company_name, company_info) VALUES
(3, 'Acme Corp',  'A medium-sized engineering company.'),
(4, 'Globex Ltd', 'A large data analytics consultancy.');

INSERT INTO jobs (employer_id, title, description, required_education, required_skills, years_experience, work_mode, location) VALUES
(3, 'Junior Software Engineer', 'Build and maintain web applications. PHP and JavaScript experience preferred.', 'Bachelor', 'PHP, JavaScript, SQL', 1, 'Hybrid', 'Sydney'),
(3, 'DevOps Engineer',          'Manage CI/CD pipelines and cloud infrastructure.', 'Bachelor', 'AWS, Docker, Linux', 3, 'Remote', 'Sydney'),
(4, 'Data Analyst',              'Work with large datasets to produce insights for clients.', 'Bachelor', 'SQL, Python, Tableau', 2, 'On-site', 'Melbourne'),
(4, 'Senior Data Scientist',     'Lead modelling projects across multiple client engagements.', 'Master', 'Python, ML, Statistics', 5, 'Hybrid', 'Melbourne');
