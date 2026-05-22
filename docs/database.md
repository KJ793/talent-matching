# Database

The platform uses MySQL 8 with InnoDB.

## Tables

| Table        | Purpose                                                    |
|--------------|------------------------------------------------------------|
| `users`      | Login credentials, role (candidate / employer), membership |
| `candidates` | Candidate profile data (1:1 with `users`)                  |
| `employers`  | Employer profile data (1:1 with `users`)                   |
| `jobs`       | Job postings created by employers                          |

## Setup

For a fresh install:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

For an existing Phase-1 database, apply the migration to preserve data:

```bash
mysql -u root -p talent_matching < database/migrations/001_add_membership_and_profile_fields.sql
```

## Migrations

| File                                                      | Notes                                                        |
|-----------------------------------------------------------|--------------------------------------------------------------|
| `migrations/001_add_membership_and_profile_fields.sql`    | Adds `users.membership`, `candidates.skills`, `candidates.work_experience`, `candidates.preferred_work_mode`, `candidates.preferred_location` |

Update `config/config.php` with your MySQL credentials before running the app.
