# ShareStream – Testing Notes

## Environment
- Local: XAMPP (PHP 8.x, MySQL)
- Browser: Chrome/Firefox
- OS: Windows/macOS

---

## Test Accounts

| Role | Email           | Password |
|------|------------------|----------|
| User | user@test.com    | test123  |
| Admin| admin@test.com   | admin123 |

---

## Core Feature Tests

### 1. Registration
- [ ] Valid registration creates user in database
- [ ] Invalid email is rejected
- [ ] Password mismatch shows error
- [ ] Duplicate email is blocked

**Expected**
- User inserted into `users`
- Password stored as hash

---

### 2. Login
- [ ] Valid credentials login successfully
- [ ] Invalid password rejected
- [ ] Blocked user cannot login

---

### 3. Create Idea
- [ ] Logged-in user can create idea
- [ ] Category required
- [ ] Title < 5 chars rejected
- [ ] Image upload accepts jpg/png/webp
- [ ] Status defaults to `active`

**Expected**
- Row inserted into `ideas`
- Image saved in `/uploads/ideas`

---

### 4. Voting
- [ ] Logged-in user can vote
- [ ] Same user cannot vote twice
- [ ] Vote counter updates instantly (AJAX)

**Expected**
- One row per user per idea in `votes`

---

### 5. Comments
- [ ] Logged-in user can comment
- [ ] Comment appears without page reload
- [ ] Comment saved with correct user

---

### 6. Bookmarks
- [ ] User can bookmark an idea
- [ ] Bookmark toggle works
- [ ] Appears in dashboard → My Bookmarks

---

### 7. Reports
- [ ] User can report idea once only
- [ ] Report reason required
- [ ] Report appears in dashboard → My Reports
- [ ] Admin sees report in admin dashboard

---

## Dashboard Tests

### User Dashboard
- [ ] My Ideas table loads correctly
- [ ] Edit button opens edit page
- [ ] Charts display user ideas over time
- [ ] Bookmarks & Reports tables load

---

### Admin Dashboard
- [ ] Admin-only access enforced
- [ ] Toggle idea status works on same page
- [ ] Removed ideas hidden from public listing
- [ ] Reports table lists all reports

---

## Security Checks
- [ ] Guest cannot access dashboard pages
- [ ] User cannot edit others’ ideas
- [ ] User cannot access admin dashboard
- [ ] SQL injection prevented (prepared statements)

---

## Known Issues / TODO
- [ ] Add pagination to tables
- [ ] Add automated tests later
- [ ] Improve error messages