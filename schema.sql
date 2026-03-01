DROP DATABASE IF EXISTS sharestream;
CREATE DATABASE sharestream CHARACTER SET utf8mb4;
USE sharestream;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    city VARCHAR(120),
    phone VARCHAR(120),
    avatar VARCHAR(255),
    bio TEXT,
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','blocked') DEFAULT 'active',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    icon VARCHAR(50),
    color VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    cover_image VARCHAR(255),
    views INT DEFAULT 0,
    status ENUM('active','removed') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    idea_id INT NOT NULL,
    value TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, idea_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (idea_id) REFERENCES ideas(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idea_id INT NOT NULL,
    user_id INT NOT NULL,
    body TEXT NOT NULL,
    edited_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idea_id) REFERENCES ideas(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    idea_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, idea_id)
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT,
    idea_id INT,
    comment_id INT,
    reason VARCHAR(255),
    resolved ENUM('no','yes') DEFAULT 'no',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);




INSERT INTO categories (name, icon, color) VALUES
('Technology', 'fa-microchip', '#4f46e5'),
('Startups', 'fa-rocket', '#7c3aed'),
('Business', 'fa-briefcase', '#0f766e'),
('Marketing', 'fa-bullhorn', '#db2777'),
('Design', 'fa-palette', '#f59e0b'),
('Programming', 'fa-code', '#2563eb'),
('AI & ML', 'fa-brain', '#9333ea'),
('Web Development', 'fa-globe', '#0284c7'),
('Mobile Apps', 'fa-mobile-screen', '#14b8a6'),
('SaaS', 'fa-cloud', '#6366f1'),

('E-commerce', 'fa-cart-shopping', '#ea580c'),
('Finance', 'fa-coins', '#16a34a'),
('Crypto', 'fa-bitcoin-sign', '#f97316'),
('Blockchain', 'fa-link', '#4b5563'),
('Education', 'fa-graduation-cap', '#1d4ed8'),
('Health', 'fa-heart-pulse', '#dc2626'),
('Fitness', 'fa-dumbbell', '#15803d'),
('Mental Health', 'fa-head-side-virus', '#7c2d12'),
('Wellness', 'fa-spa', '#0d9488'),
('Nutrition', 'fa-apple-whole', '#65a30d'),

('Travel', 'fa-plane', '#0284c7'),
('Tourism', 'fa-map-location-dot', '#0369a1'),
('Hospitality', 'fa-hotel', '#9333ea'),
('Food', 'fa-utensils', '#ea580c'),
('Restaurants', 'fa-burger', '#b45309'),
('Cooking', 'fa-kitchen-set', '#92400e'),
('Recipes', 'fa-book-open', '#a16207'),
('Lifestyle', 'fa-person-walking', '#334155'),
('Fashion', 'fa-shirt', '#be185d'),
('Beauty', 'fa-face-smile', '#ec4899'),

('Art', 'fa-paintbrush', '#7c3aed'),
('Photography', 'fa-camera', '#1f2937'),
('Videography', 'fa-video', '#111827'),
('Music', 'fa-music', '#4f46e5'),
('Podcasts', 'fa-microphone', '#4338ca'),
('Movies', 'fa-film', '#1e293b'),
('Gaming', 'fa-gamepad', '#6d28d9'),
('Esports', 'fa-trophy', '#a21caf'),
('Animation', 'fa-wand-magic-sparkles', '#9333ea'),
('Comics', 'fa-book', '#f59e0b'),

('Social Media', 'fa-hashtag', '#0ea5e9'),
('Content Creation', 'fa-pen-nib', '#2563eb'),
('Blogging', 'fa-pen-to-square', '#1d4ed8'),
('Copywriting', 'fa-quote-right', '#7c2d12'),
('SEO', 'fa-magnifying-glass', '#15803d'),
('Advertising', 'fa-chart-line', '#0f766e'),
('Sales', 'fa-handshake', '#047857'),
('Customer Support', 'fa-headset', '#0369a1'),
('CRM', 'fa-users', '#475569'),
('HR', 'fa-user-tie', '#334155'),

('Productivity', 'fa-list-check', '#0ea5e9'),
('Time Management', 'fa-clock', '#0284c7'),
('Remote Work', 'fa-laptop-house', '#2563eb'),
('Freelancing', 'fa-user-gear', '#4338ca'),
('Career', 'fa-briefcase', '#1f2937'),
('Personal Growth', 'fa-arrow-up-right-dots', '#16a34a'),
('Motivation', 'fa-fire', '#dc2626'),
('Leadership', 'fa-crown', '#7c2d12'),
('Team Building', 'fa-people-group', '#0f766e'),
('Communication', 'fa-comments', '#0369a1'),

('Environment', 'fa-leaf', '#15803d'),
('Sustainability', 'fa-recycle', '#166534'),
('Green Energy', 'fa-solar-panel', '#ca8a04'),
('Smart Cities', 'fa-city', '#334155'),
('IoT', 'fa-wifi', '#4f46e5'),
('Automation', 'fa-robot', '#6d28d9'),
('Data Science', 'fa-database', '#1d4ed8'),
('Analytics', 'fa-chart-pie', '#0284c7'),
('Cyber Security', 'fa-shield-halved', '#111827'),
('DevOps', 'fa-gears', '#475569');
