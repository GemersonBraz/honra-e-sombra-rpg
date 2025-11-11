-- Migration: Add avatar column to users table
ALTER TABLE users
  ADD COLUMN avatar VARCHAR(255) NULL AFTER senha;

-- Optionally index if needed later
-- ALTER TABLE users ADD INDEX idx_users_avatar (avatar);
