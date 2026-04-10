CREATE TABLE IF NOT EXISTS seedling_entres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entres_code VARCHAR(50) NOT NULL UNIQUE,
    entres_date DATE NOT NULL,
    harvest_id INT NOT NULL,
    result_item_id INT NOT NULL,
    used_quantity INT NOT NULL,
    location VARCHAR(100),
    mandor VARCHAR(100),
    manager VARCHAR(100),
    notes TEXT,
    bpdas_id INT NOT NULL,
    nursery_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (harvest_id) REFERENCES seedling_harvests(id),
    FOREIGN KEY (result_item_id) REFERENCES seedling_types(id),
    FOREIGN KEY (bpdas_id) REFERENCES bpdas(id),
    FOREIGN KEY (nursery_id) REFERENCES nurseries(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS seedling_entres_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entres_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (entres_id) REFERENCES seedling_entres(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES bahan_baku_master(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
