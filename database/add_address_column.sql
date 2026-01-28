-- Add address column to users table
-- This column is needed to store user addresses for request details

ALTER TABLE users 
ADD COLUMN address TEXT NULL AFTER nik;
