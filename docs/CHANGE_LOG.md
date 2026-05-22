# Change Log

## 2026-04 — Week 8 requirement change

The project specification was updated mid-development with three new requirement
groups. This document captures the changes and the technical decisions made to
absorb them.

### What changed in the requirements

1. **Extended candidate profile.** Profiles must now include skills, work
   experience, preferred working mode, and preferred location.
2. **Membership tiers.** All users (candidates and employers) must have
   membership. Free users are capped at 10 recommendations; premium users get
   unlimited recommendations.
3. **Richer search.** Search must support keyword search across the whole
   profile/job (not just description), filter-only search, keyword + filter
   combined, and fuzzy matching for typos and synonyms.

### Impact analysis

| Area                     | Affected | Scope of change                                                   |
|--------------------------|----------|-------------------------------------------------------------------|
| Database schema          | yes      | New columns on `users` and `candidates` (additive, non-breaking)  |
| Authentication           | no       | No change                                                         |
| Candidate profile pages  | yes      | New form fields and display                                       |
| Job posting              | no       | Already captures the necessary fields                             |
| Recommendation engine    | yes      | Score function extended; cap is now membership-aware              |
| Search                   | yes      | Rewrite to support filters, combinations, and fuzzy matching      |
| Tests                    | yes      | New tests for membership-aware recommender and fuzzy helpers      |

### Approach

To keep the project history clean and to avoid breaking the existing Phase-1
database for any team member who had already loaded data, the schema change is
delivered as an **additive migration** (`migrations/001_*.sql`) rather than a
schema rewrite. The full `schema.sql` is also updated so that fresh installs
get the post-migration state in one step.

The recommendation cap was previously a hard-coded constant
(`Recommender::TOP_K = 10`); it remains the default for free users but the
function now accepts a per-call limit so premium users can bypass it.

The search rewrite is the largest change. Phase 1 only searched the job
description column. Phase 2 introduces a `Fuzzy` helper class
(Levenshtein-based) and rebuilds the search methods on `JobRepository` and
`CandidateRepository` to accept a structured filter array.

### Risks introduced

- **Migration drift.** If different team members are at different points in
  the commit history, they must apply the migration explicitly. The README
  documents this clearly.
- **Search performance.** Fuzzy matching is implemented in PHP rather than at
  the SQL layer because the dataset for this project is small. For a real
  production system this would need to move to a search index (e.g.
  Elasticsearch, MeiliSearch).
