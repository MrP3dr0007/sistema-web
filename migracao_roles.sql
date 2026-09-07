ALTER TABLE usuarios
  ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user';
