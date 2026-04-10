-- 1. Tabel Utama Penyapihan (Weaning / PE)
CREATE TABLE IF NOT EXISTS seedling_weanings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weaning_code VARCHAR(50) NOT NULL UNIQUE COMMENT 'PE-YYYYMMXXX',
    weaning_date DATE NOT NULL,
    harvest_id INT NOT NULL COMMENT 'Relasi ke seedling_harvests (PA)',
    result_item_id INT NOT NULL COMMENT 'Relasi ke seedling_types (Master Bibit)',
    weaned_quantity INT NOT NULL COMMENT 'Jumlah anakan yang disapih',
    location VARCHAR(255) COMMENT 'Teks lokasi penempatan',
    mandor VARCHAR(150),
    manager VARCHAR(150),
    notes TEXT,
    bpdas_id INT DEFAULT NULL,
    nursery_id INT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (harvest_id) REFERENCES seedling_harvests(id) ON DELETE CASCADE,
    FOREIGN KEY (result_item_id) REFERENCES seedling_types(id),
    FOREIGN KEY (bpdas_id) REFERENCES bpdas(id) ON DELETE SET NULL,
    FOREIGN KEY (nursery_id) REFERENCES nurseries(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- 2. Polybag Isi Media Tanam yang Dipakai
CREATE TABLE IF NOT EXISTS seedling_weaning_polybags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weaning_id INT NOT NULL,
    bag_filling_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (weaning_id) REFERENCES seedling_weanings(id) ON DELETE CASCADE,
    FOREIGN KEY (bag_filling_id) REFERENCES bag_fillings(id)
);

-- 3. Bahan Baku Pendukung yang Dipakai
CREATE TABLE IF NOT EXISTS seedling_weaning_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weaning_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (weaning_id) REFERENCES seedling_weanings(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id)
);
