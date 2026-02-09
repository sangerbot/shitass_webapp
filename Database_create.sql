DROP DATABASE IF EXISTS notes_app;

CREATE DATABASE notes_app
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE notes_app;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE folders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
);

CREATE TABLE notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    folder_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id)
      ON DELETE SET NULL
);

CREATE TABLE kanban (
    note_id INT UNSIGNED NOT NULL,
    uncategorized BOOLEAN DEFAULT TRUE,
    todo BOOLEAN DEFAULT FALSE,
    doing BOOLEAN DEFAULT FALSE,
    done BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (note_id) REFERENCES notes(id)
      ON DELETE CASCADE
);

INSERT INTO users (username, email, password) VALUES 
('alice', 'alice@example.com', 'hash1'),
('bob', 'bob@example.com', 'hash2'),
('carol', 'carol@example.com', 'hash3'),
('dave', 'dave@example.com', 'hash4'),
('eve', 'eve@example.com', 'hash5'),
('frank', 'frank@example.com', 'hash6'),
('grace', 'grace@example.com', 'hash7'),
('heidi', 'heidi@example.com', 'hash8'),
('ivan', 'ivan@example.com', 'hash9'),
('judy', 'judy@example.com', 'hash10');

INSERT INTO notes (user_id, folder_id, title, content) VALUES
(1, NULL, 'Grocery List', 'Buy milk, eggs, bread'),
(2, NULL, 'Workout Plan', 'Monday: Chest, Tuesday: Back'),
(3, NULL, 'Project Ideas', 'Build a chatbot for school'),
(4, NULL, 'Meeting Notes', 'Discuss quarterly results'),
(5, NULL, 'Travel Plans', 'Visit Japan in spring'),
(6, NULL, 'Book List', 'Read 10 books this year'),
(7, NULL, 'Recipe', 'Pasta with homemade sauce'),
(8, NULL, 'Daily Journal', 'Today I learned SQL'),
(9, NULL, 'Budget', 'Track monthly expenses'),
(10, NULL, 'Learning Goals', 'Practice coding 2 hours/day');

INSERT INTO kanban (note_id, uncategorized, todo, doing, done) VALUES
(1, TRUE, FALSE, FALSE, FALSE),
(2, TRUE, TRUE, FALSE, FALSE),
(3, TRUE, TRUE, TRUE, FALSE),
(4, TRUE, FALSE, TRUE, FALSE),
(5, TRUE, FALSE, FALSE, TRUE),
(6, TRUE, TRUE, FALSE, TRUE),
(7, TRUE, FALSE, TRUE, TRUE),
(8, TRUE, TRUE, TRUE, TRUE),
(9, TRUE, FALSE, FALSE, FALSE),
(10, TRUE, FALSE, TRUE, FALSE);

INSERT INTO folders (user_id, name) VALUES
(1, 'Personal'),
(2, 'Work'),
(3, 'Projects'),
(4, 'Notes'),
(5, 'Travel');

UPDATE notes SET folder_id = 1 WHERE id IN (1, 6);
UPDATE notes SET folder_id = 2 WHERE id IN (2, 4);
UPDATE notes SET folder_id = 3 WHERE id IN (3, 7);
UPDATE notes SET folder_id = 4 WHERE id IN (8);
UPDATE notes SET folder_id = 5 WHERE id IN (5, 9, 10);
