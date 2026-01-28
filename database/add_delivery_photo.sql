-- Migration: Add delivery_photo_path column
-- Date: 2026-01-28

-- Add delivery_photo_path column for proof of delivery photos
ALTER TABLE requests 
ADD COLUMN delivery_photo_path VARCHAR(255) NULL COMMENT 'Path to delivery proof photo (WebP format)';

-- Add index for delivery photo path
CREATE INDEX idx_delivery_photo ON requests(delivery_photo_path);
