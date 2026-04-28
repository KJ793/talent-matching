# Database

The platform uses MySQL 8 with InnoDB.

## Tables (Phase 1)

| Table        | Purpose                                                    |
|--------------|------------------------------------------------------------|
| `users`      | Login credentials and role (candidate / employer)          |
| `candidates` | Candidate-specific profile data (1:1 with `users`)         |
| `employers`  | Employer-specific profile data (1:1 with `users`)          |
| `jobs`       | Job postings created by employers                          |

## Setup

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

Update `config/config.php` with your MySQL credentials before running the app.
