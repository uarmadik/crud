CREATE TABLE posts (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    header VARCHAR(255) NOT NULL,
    text TEXT,
    created_at (DATE )
);