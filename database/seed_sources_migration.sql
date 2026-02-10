-- ============================================
-- Seed Sources Migration
-- Dashboard Stok Bibit Persemaian Indonesia
-- Direktori Sumber Benih Nasional
-- ============================================

-- Create seed_sources table
CREATE TABLE IF NOT EXISTS seed_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Basic Information
    seed_source_name VARCHAR(200) NOT NULL COMMENT 'Nama Sumber Benih',
    local_name VARCHAR(200) COMMENT 'Nama Lokal',
    botanical_name VARCHAR(200) COMMENT 'Nama Botani',
    
    -- Location & Area
    area_hectares DECIMAL(10, 3) COMMENT 'Luas (Ha)',
    seed_class VARCHAR(100) COMMENT 'Kelas SB (e.g., TBT, KBT)',
    location TEXT COMMENT 'Lokasi SB',
    latitude DECIMAL(10, 8) COMMENT 'Titik Koordinat SB (Lintang)',
    longitude DECIMAL(11, 8) COMMENT 'Titik Koordinat SB (Bujur)',
    
    -- Owner Information
    owner_name VARCHAR(200) COMMENT 'Pemilik',
    owner_phone VARCHAR(20) COMMENT 'Nomor Telepon Pemilik',
    ownership_type ENUM('Perorangan', 'Perusahaan', 'Instansi') COMMENT 'Jenis Kepemilikan',
    
    -- Certificate Information
    certificate_number VARCHAR(100) COMMENT 'Nomor Sertifikat Sumber Benih',
    certificate_date DATE COMMENT 'Tanggal Sertifikat Sumber Benih',
    certificate_validity DATE COMMENT 'Masa Berlaku Sertifikat Sumber Benih',
    
    -- Production Information
    tree_count INT COMMENT 'Jumlah Pohon',
    flowering_season VARCHAR(100) COMMENT 'Musim Pembungaan',
    fruiting_season VARCHAR(100) COMMENT 'Musim Buah Masak',
    production_estimate_per_year DECIMAL(10, 3) COMMENT 'Estimasi Produksi Benih (Kg/tahun)',
    seed_quantity_estimate INT COMMENT 'Estimasi Jumlah Benih (butir)',
    
    -- Usage & Other
    utilization TEXT COMMENT 'Pemanfaatan',
    
    -- Foreign Keys
    province_id INT NOT NULL COMMENT 'ID Provinsi',
    seedling_type_id INT NULL COMMENT 'ID Jenis Bibit (optional)',
    
    -- Status
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Status aktif',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for search optimization
    INDEX idx_province (province_id),
    INDEX idx_seedling_type (seedling_type_id),
    INDEX idx_coordinates (latitude, longitude),
    INDEX idx_certificate (certificate_number),
    INDEX idx_owner (owner_name),
    INDEX idx_seed_class (seed_class),
    INDEX idx_active (is_active),
    
    -- Foreign Key Constraints
    FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE RESTRICT,
    FOREIGN KEY (seedling_type_id) REFERENCES seedling_types(id) ON DELETE SET NULL
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Direktori Sumber Benih Nasional';
