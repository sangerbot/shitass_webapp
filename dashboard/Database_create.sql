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
    admin Boolean default FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 

CREATE TABLE subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    is_subscribed BOOLEAN NOT NULL DEFAULT FALSE,
    started_at TIMESTAMP NULL DEFAULT NULL,
    expires_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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

DELIMITER //

CREATE TRIGGER after_note_insert
AFTER INSERT ON notes
FOR EACH ROW
BEGIN
    INSERT INTO kanban (note_id, uncategorized, todo, doing, done)
    VALUES (NEW.id, TRUE, FALSE, FALSE, FALSE);
END //

DELIMITER ;