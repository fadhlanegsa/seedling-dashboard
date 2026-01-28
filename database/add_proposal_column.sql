-- Migration: Add proposal_file_path column and update land_area to NOT NULL
-- Date: 2026-01-28

-- Add proposal_file_path column
ALTER TABLE requests 
ADD COLUMN proposal_file_path VARCHAR(255) NULL COMMENT 'Path to uploaded proposal PDF for requests >25 seedlings';

-- Update existing NULL land_area to 0 (for old data)
UPDATE requests SET land_area = 0 WHERE land_area IS NULL;

-- Update land_area to NOT NULL with 3 decimal places
ALTER TABLE requests 
MODIFY COLUMN land_area DECIMAL(10, 3) NOT NULL DEFAULT 0 COMMENT 'Land area in hectares, supports up to 3 decimal places';

-- Add index for proposal file path
CREATE INDEX idx_proposal ON requests(proposal_file_path);
