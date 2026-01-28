-- ============================================
-- Add Geotagging Columns to Requests Table
-- Menambahkan kolom latitude dan longitude
-- ============================================

-- Add latitude and longitude columns to requests table
ALTER TABLE requests 
ADD COLUMN latitude DECIMAL(10, 8) NULL COMMENT 'Latitude koordinat lokasi tanam' AFTER land_area,
ADD COLUMN longitude DECIMAL(11, 8) NULL COMMENT 'Longitude koordinat lokasi tanam' AFTER latitude,
ADD INDEX idx_coordinates (latitude, longitude);

-- Display success message
SELECT 'Geotagging columns added successfully!' as message;
