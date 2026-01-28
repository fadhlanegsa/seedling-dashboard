-- ============================================
-- Seedling Stock Dashboard Database Schema
-- Dashboard Stok Bibit Persemaian Indonesia
-- ============================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS request_history;
DROP TABLE IF EXISTS requests;
DROP TABLE IF EXISTS stock;
DROP TABLE IF EXISTS seedling_types;
DROP TABLE IF EXISTS bpdas;
DROP TABLE IF EXISTS provinces;
DROP TABLE IF EXISTS users;

-- ============================================
-- Table: provinces
-- Stores Indonesian provinces
-- ============================================
CREATE TABLE provinces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: users
-- Stores all user accounts (admin, bpdas, public)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    nik VARCHAR(16), -- NIK for public users
    address TEXT, -- User address
    role ENUM('admin', 'bpdas', 'public') NOT NULL DEFAULT 'public',
    bpdas_id INT NULL, -- Foreign key for BPDAS users
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: bpdas
-- Stores BPDAS (Balai Pengelolaan DAS) information
-- ============================================
CREATE TABLE bpdas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    province_id INT NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    contact_person VARCHAR(100),
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE RESTRICT,
    INDEX idx_province (province_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key to users table for BPDAS relationship
ALTER TABLE users 
ADD CONSTRAINT fk_users_bpdas 
FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL;

-- ============================================
-- Table: seedling_types
-- Stores 138 types of seedlings
-- ============================================
CREATE TABLE seedling_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    scientific_name VARCHAR(200),
    category ENUM('Pohon Hutan', 'Pohon Buah', 'Tanaman Obat', 'Bambu', 'Mangrove', 'Lainnya') DEFAULT 'Pohon Hutan',
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: stock
-- Stores seedling stock for each BPDAS
-- ============================================
CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bpdas_id INT NOT NULL,
    seedling_type_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    last_update_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE CASCADE,
    FOREIGN KEY (seedling_type_id) REFERENCES seedling_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_stock (bpdas_id, seedling_type_id),
    INDEX idx_bpdas (bpdas_id),
    INDEX idx_seedling (seedling_type_id),
    INDEX idx_quantity (quantity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: requests
-- Stores seedling requests from public users
-- ============================================
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    bpdas_id INT NOT NULL,
    seedling_type_id INT NOT NULL,
    quantity INT NOT NULL,
    purpose TEXT NOT NULL,
    land_area DECIMAL(10, 3) NOT NULL DEFAULT 0, -- in hectares, supports 3 decimal places
    latitude DECIMAL(10, 8) NULL, -- Latitude koordinat lokasi tanam
    longitude DECIMAL(11, 8) NULL, -- Longitude koordinat lokasi tanam
    proposal_file_path VARCHAR(255) NULL, -- Path to uploaded proposal PDF for requests >25 seedlings
    delivery_photo_path VARCHAR(255) NULL, -- Path to delivery proof photo (WebP format)
    status ENUM('pending', 'approved', 'rejected', 'completed', 'delivered') DEFAULT 'pending',
    approved_by INT NULL, -- user_id of BPDAS staff who approved
    approval_date TIMESTAMP NULL,
    approval_notes TEXT,
    rejection_reason TEXT,
    approval_letter_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE CASCADE,
    FOREIGN KEY (seedling_type_id) REFERENCES seedling_types(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_user (user_id),
    INDEX idx_bpdas (bpdas_id),
    INDEX idx_request_number (request_number),
    INDEX idx_coordinates (latitude, longitude),
    INDEX idx_proposal (proposal_file_path),
    INDEX idx_delivery_photo (delivery_photo_path)
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: request_history
-- Stores history/logs of request status changes
-- ============================================
CREATE TABLE request_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL,
    changed_by INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Default Admin User
-- Username: admin, Password: admin123 (hashed)
-- ============================================
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@seedling-dashboard.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Note: Default password is 'admin123' - CHANGE THIS IN PRODUCTION!
