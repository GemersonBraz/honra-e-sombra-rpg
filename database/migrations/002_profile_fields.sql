-- Migration: Add basic profile fields to users
ALTER TABLE users
  ADD COLUMN display_title VARCHAR(40) NULL AFTER nome,
  ADD COLUMN bio TEXT NULL AFTER email;