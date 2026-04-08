-- 1. Seed Sowings Table (PC-XXX)
CREATE TABLE IF NOT EXISTS seed_sowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sowing_code VARCHAR(50) NOT NULL UNIQUE,
    sowing_date DATE NOT NULL,
    seed_item_id INT NOT NULL,
    seed_quantity DECIMAL(10,2) NOT NULL,
    mandor VARCHAR(150),
    manager VARCHAR(150),
    notes TEXT,
    bpdas_id INT DEFAULT NULL,
    nursery_id INT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seed_item_id) REFERENCES bahan_baku_master(id),
    FOREIGN KEY (bpdas_id) REFERENCES bpdas(id),
    FOREIGN KEY (nursery_id) REFERENCES nurseries(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- 2. Used Polybags Table (Links to PB-XXX)
CREATE TABLE IF NOT EXISTS seed_sowing_polybags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sowing_id INT NOT NULL,
    bag_filling_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sowing_id) REFERENCES seed_sowings(id) ON DELETE CASCADE,
    FOREIGN KEY (bag_filling_id) REFERENCES bag_fillings(id)
);

-- 3. Supporting Materials Table (Pupuk, Obat, etc)
CREATE TABLE IF NOT EXISTS seed_sowing_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sowing_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sowing_id) REFERENCES seed_sowings(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id)
);
