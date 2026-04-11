USE sharestream;

-- =========================================
-- TEST 1: Insert valid user record
-- Expected: Success
-- =========================================
INSERT INTO users (full_name, email, password_hash, city, phone)
VALUES ('Ali Khan', 'saif@test.com', 'hashed_password_123', 'Edinburgh', '123456789');

-- Check inserted user
SELECT * FROM users WHERE email = 'saif@test.com';


-- =========================================
-- TEST 2: Insert duplicate email
-- Expected: Duplicate entry error
-- =========================================
INSERT INTO users (full_name, email, password_hash)
VALUES ('Ali Khan Duplicate', 'saif@test.com', 'hashed_password_456');


-- =========================================
-- TEST 3: Insert invalid ENUM value for role/status
-- Expected: Error or invalid value rejected
-- =========================================
INSERT INTO users (full_name, email, password_hash, role, status)
VALUES ('Enum Test', 'enum@test.com', 'hashed_password_enum', 'manager', 'deleted');


-- =========================================
-- TEST 4: Insert valid idea with valid user_id and category_id
-- Expected: Success
-- =========================================
INSERT INTO ideas (user_id, category_id, title, summary, description)
VALUES (1, 1, 'Test Idea Title', 'This is a short test summary', 'This is a full description for the test idea.');

-- Check inserted idea
SELECT * FROM ideas WHERE title = 'Test Idea Title';


-- =========================================
-- TEST 5: Insert idea with invalid foreign key
-- Expected: Foreign key constraint error
-- =========================================
INSERT INTO ideas (user_id, category_id, title, summary, description)
VALUES (9999, 9999, 'Invalid FK Idea', 'Invalid summary', 'Invalid description test');


-- =========================================
-- TEST 6: Insert valid vote record
-- Expected: Success
-- =========================================
INSERT INTO votes (user_id, idea_id)
VALUES (1, 1);

-- Check inserted vote
SELECT * FROM votes WHERE user_id = 1 AND idea_id = 1;


-- =========================================
-- TEST 7: Insert duplicate vote
-- Expected: Duplicate entry error because of UNIQUE(user_id, idea_id)
-- =========================================
INSERT INTO votes (user_id, idea_id)
VALUES (1, 1);


-- =========================================
-- TEST 8: Insert valid comment
-- Expected: Success
-- =========================================
INSERT INTO comments (idea_id, user_id, body)
VALUES (1, 1, 'This is a test comment for database checking.');

-- Check inserted comment
SELECT * FROM comments WHERE user_id = 1 AND idea_id = 1;


-- =========================================
-- TEST 9: Insert duplicate bookmark
-- Expected: First success, second duplicate entry error
-- =========================================
INSERT INTO bookmarks (user_id, idea_id)
VALUES (1, 1);

INSERT INTO bookmarks (user_id, idea_id)
VALUES (1, 1);


-- =========================================
-- TEST 10: Insert report without resolved value
-- Expected: Success, resolved should default to 'no'
-- =========================================
INSERT INTO reports (reporter_id, idea_id, reason)
VALUES (1, 1, 'This is a test report.');

-- Check default resolved value
SELECT * FROM reports WHERE reporter_id = 1 AND idea_id = 1;


-- =========================================
-- EXTRA CHECKS: DEFAULT VALUES
-- =========================================
SELECT id, full_name, role, status, created_at
FROM users
WHERE email = 'ali@test.com';

SELECT id, title, status, created_at
FROM ideas
WHERE title = 'Test Idea Title';

SELECT id, reporter_id, idea_id, resolved, created_at
FROM reports
WHERE reporter_id = 1 AND idea_id = 1;


-- =========================================
-- EXTRA CHECKS: DATA INTEGRITY
-- =========================================

-- Check if there are any votes pointing to missing users
SELECT * FROM votes
WHERE user_id NOT IN (SELECT id FROM users);

-- Check if there are any votes pointing to missing ideas
SELECT * FROM votes
WHERE idea_id NOT IN (SELECT id FROM ideas);

-- Check if there are any comments pointing to missing users
SELECT * FROM comments
WHERE user_id NOT IN (SELECT id FROM users);

-- Check if there are any comments pointing to missing ideas
SELECT * FROM comments
WHERE idea_id NOT IN (SELECT id FROM ideas);


-- =========================================
-- EXTRA CHECK: Parent-child relationship restriction
-- Expected: Delete should fail if user/idea is referenced
-- =========================================

-- Try deleting a user that is used in ideas/votes/comments
DELETE FROM users WHERE id = 1;

-- Try deleting an idea that is used in votes/comments
DELETE FROM ideas WHERE id = 1;


-- =============================
-- CLEANUP QUERIES (SAFE ORDER)
-- =============================

-- Delete reports related to the idea or user
DELETE FROM reports
WHERE reporter_id = 1 OR idea_id = 1 OR comment_id IN (
    SELECT id FROM comments WHERE user_id = 1 OR idea_id = 1
);

-- Delete bookmarks referencing the idea or user
DELETE FROM bookmarks
WHERE user_id = 1 OR idea_id = 1;

-- Delete votes referencing the idea or user
DELETE FROM votes
WHERE user_id = 1 OR idea_id = 1;

-- Delete comments referencing the idea or user
DELETE FROM comments
WHERE user_id = 1 OR idea_id = 1;

-- Delete ideas created by the user
DELETE FROM ideas
WHERE user_id = 1 OR id = 1;

-- Finally delete the user
DELETE FROM users
WHERE id = 1;
