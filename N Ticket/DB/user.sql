ALTER TABLE users ADD COLUMN role ENUM('client', 'support') NOT NULL DEFAULT 'client' AFTER password;
