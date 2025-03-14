CREATE DATABASE imageboard;
USE imageboard;

CREATE TABLE boards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(10) UNIQUE
);

INSERT INTO boards (name) VALUES ('b'), ('tech'), ('news');

CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    board_id INT,
    message TEXT,
    image VARCHAR(255),
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
