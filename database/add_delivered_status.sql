-- Migration: Add 'delivered' status to requests
-- Date: 2026-01-28

-- Modify status ENUM to include 'delivered'
ALTER TABLE requests 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'delivered') DEFAULT 'pending';
