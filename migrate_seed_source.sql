ALTER TABLE bahan_baku_transactions ADD COLUMN seed_source_id INT NULL;
ALTER TABLE bahan_baku_transactions ADD CONSTRAINT fk_bahan_baku_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL;

ALTER TABLE seed_sowings ADD COLUMN seed_source_id INT NULL;
ALTER TABLE seed_sowings ADD CONSTRAINT fk_seed_sowings_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL;

ALTER TABLE seedling_weanings ADD COLUMN seed_source_id INT NULL;
ALTER TABLE seedling_weanings ADD CONSTRAINT fk_weanings_seed_source FOREIGN KEY (seed_source_id) REFERENCES seed_sources(id) ON DELETE SET NULL;
