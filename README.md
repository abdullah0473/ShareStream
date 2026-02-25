# ShareStream

ShareStream is a UK-based community web platform where users can share
ideas, vote on ideas, comment, bookmark posts, and report inappropriate
content. The platform includes an admin moderation system and user
dashboards with statistics.

------------------------------------------------------------------------

## Project Information

**Module:** SOC09109 2025--6 TR2 001 (Group Project)\
**Project Type:** Student Academic Project\
**Technology Stack:**
- Core PHP
- MySQL / MariaDB
- HTML5, CSS3
- JavaScript, jQuery
- Bootstrap 5
- Font Awesome
- Chart.js

------------------------------------------------------------------------

## Team Members

-   Abdullah Arshad (40692321) -- Project Manager
-   Rao Muhammad Safee (40738239) -- Front-End Developer
-   Raja Muhammad Bilal Nasir (40770469) -- Database Engineer
-   Muhammad Zohaib Azam (40738615) -- Backend Developer
-   Mirza Sikandar Tariq (40692209) -- Quality Assurance Engineer

------------------------------------------------------------------------

## Key Features

### User Features

-   User registration and secure login (password hashing)
-   Automatic update of last login date
-   Create, edit, and manage ideas
-   View idea details page
-   One vote per user per idea
-   Comment system
-   Bookmark ideas
-   Report inappropriate ideas or comments
-   Personal dashboard with statistics and charts

### Admin Features

-   Admin dashboard
-   View all reports
-   Change idea status (Active / Removed)
-   Moderate reported content
-   Platform statistics overview

------------------------------------------------------------------------

## Folder Structure

# ShareStream Project Structure

| Path | Description |
|------|------------|
| **ShareStream/** | Root project folder |
| ├── **admin/** | Admin panel files |
| │   ├── dashboard.php | Admin dashboard overview |
| │   ├── admin_dashboard.php | Main admin control panel |
| │   ├── create_idea.php | Create new idea (admin) |
| │   ├── edit_idea.php | Edit existing idea |
| │   └── profile.php | Admin profile management |
| ├── **public/** | Public-facing pages |
| │   ├── index.php | Homepage |
| │   ├── ideas.php | Ideas listing page |
| │   ├── idea.php | Single idea details page |
| │   ├── login.php | User login page |
| │   ├── register.php | User registration page |
| │   └── logout.php | User logout handler |
| ├── **ajax/** | AJAX handlers (vote, comment, bookmark, report, etc.) |
| ├── **includes/** | Shared layout files (header.php, footer.php) |
| ├── **assets/** | Static assets |
| │   ├── css/ | Stylesheets |
| │   └── js/ | JavaScript files |
| ├── **uploads/** | Uploaded content storage |
| │   └── ideas/ | Uploaded idea-related files |
| ├── **config/** | Configuration files |
| │   ├── config.php | App configuration |
| │   └── db.php | Database connection |
| └── **database/** | Database files |
|     └── sharestream.sql | Database schema file |

------------------------------------------------------------------------

## Installation Guide (Local Setup - XAMPP)

1.  Place project folder inside:
    C:`\xampp\htdocs\ShareStream`

2.  Start Apache and MySQL from XAMPP Control Panel.

3.  Create a database named: sharestream

4.  Import the SQL file located in: /database/sharestream.sql

5.  Update database credentials in: config/db.php

6.  Open in browser: http://localhost/ShareStream/public/index.php

------------------------------------------------------------------------

## Database Tables

-   users
-   categories
-   ideas
-   votes
-   comments
-   bookmarks
-   reports

------------------------------------------------------------------------

## Testing Checklist

-   Test user registration validation
-   Test secure login and session handling
-   Test idea creation and editing
-   Test voting restrictions (one vote per user)
-   Test commenting functionality
-   Test bookmarking
-   Test reporting system
-   Test admin moderation
-   Test dashboard statistics and charts

------------------------------------------------------------------------

## Project Development Approach

The project followed an Agile sprint-based approach:

# Project Sprints

- **Sprint 1** — Project Setup and Database Design  
- **Sprint 2** — Authentication System  
- **Sprint 3** — Idea Posting Feature  
- **Sprint 4** — Voting and Comments  
- **Sprint 5** — Bookmark and Reporting  
- **Sprint 6** — Search and Filtering  
- **Sprint 7** — User Dashboard  
- **Sprint 8** — Admin Moderation  
- **Sprint 9** — Testing and Bug Fixing  
- **Sprint 10** — Final Documentation and Deployment  

------------------------------------------------------------------------

## Quality Standards

-   Secure password hashing
-   Prepared SQL statements
-   Input validation
-   Role-based access control
-   Responsive design (Bootstrap 5)
-   Clean and modular code structure

------------------------------------------------------------------------

## License

This project is created for academic coursework submission purposes
only.
