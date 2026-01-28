-- ============================================
-- Sample Data for Seedling Stock Dashboard
-- Dashboard Stok Bibit Persemaian Indonesia
-- ============================================

-- ============================================
-- Insert Indonesian Provinces (34 provinces)
-- ============================================
INSERT INTO provinces (name, code) VALUES
('Aceh', 'AC'),
('Sumatera Utara', 'SU'),
('Sumatera Barat', 'SB'),
('Riau', 'RI'),
('Jambi', 'JA'),
('Sumatera Selatan', 'SS'),
('Bengkulu', 'BE'),
('Lampung', 'LA'),
('Kepulauan Bangka Belitung', 'BB'),
('Kepulauan Riau', 'KR'),
('DKI Jakarta', 'JK'),
('Jawa Barat', 'JB'),
('Jawa Tengah', 'JT'),
('DI Yogyakarta', 'YO'),
('Jawa Timur', 'JI'),
('Banten', 'BT'),
('Bali', 'BA'),
('Nusa Tenggara Barat', 'NB'),
('Nusa Tenggara Timur', 'NT'),
('Kalimantan Barat', 'KB'),
('Kalimantan Tengah', 'KT'),
('Kalimantan Selatan', 'KS'),
('Kalimantan Timur', 'KI'),
('Kalimantan Utara', 'KU'),
('Sulawesi Utara', 'SA'),
('Sulawesi Tengah', 'ST'),
('Sulawesi Selatan', 'SN'),
('Sulawesi Tenggara', 'SG'),
('Gorontalo', 'GO'),
('Sulawesi Barat', 'SR'),
('Maluku', 'MA'),
('Maluku Utara', 'MU'),
('Papua', 'PA'),
('Papua Barat', 'PB');

-- ============================================
-- Insert Sample BPDAS (10 BPDAS across Indonesia)
-- ============================================
INSERT INTO bpdas (name, province_id, address, phone, email, contact_person) VALUES
('BPDAS Krueng Aceh', 1, 'Jl. Tgk. Chik Pante Kulu No. 4, Banda Aceh', '0651-23456', 'bpdas.aceh@menlhk.go.id', 'Ir. Ahmad Fauzi'),
('BPDAS Asahan Barumun', 2, 'Jl. Sisingamangaraja No. 1, Medan', '061-4567890', 'bpdas.sumut@menlhk.go.id', 'Dr. Siti Aminah'),
('BPDAS Batanghari', 5, 'Jl. Jenderal Sudirman No. 15, Jambi', '0741-123456', 'bpdas.jambi@menlhk.go.id', 'Ir. Bambang Sutrisno'),
('BPDAS Citarum Ciliwung', 12, 'Jl. Soekarno Hatta No. 628, Bandung', '022-7564321', 'bpdas.jabar@menlhk.go.id', 'Drs. Eko Prasetyo'),
('BPDAS Pemali Jratun', 13, 'Jl. Pemuda No. 142, Semarang', '024-8765432', 'bpdas.jateng@menlhk.go.id', 'Ir. Retno Wulandari'),
('BPDAS Brantas', 15, 'Jl. Raya Malang No. 1, Malang', '0341-234567', 'bpdas.jatim@menlhk.go.id', 'Dr. Agus Setiawan'),
('BPDAS Kapuas', 21, 'Jl. Ahmad Yani Km. 6, Banjarmasin', '0511-345678', 'bpdas.kalsel@menlhk.go.id', 'Ir. Hendra Gunawan'),
('BPDAS Mahakam Berau', 23, 'Jl. MT Haryono No. 25, Samarinda', '0541-456789', 'bpdas.kaltim@menlhk.go.id', 'Dra. Sari Indah'),
('BPDAS Jeneberang Walanae', 27, 'Jl. Perintis Kemerdekaan Km. 10, Makassar', '0411-567890', 'bpdas.sulsel@menlhk.go.id', 'Ir. Muhammad Yusuf'),
('BPDAS Mamberamo', 33, 'Jl. Raya Sentani No. 45, Jayapura', '0967-678901', 'bpdas.papua@menlhk.go.id', 'Ir. John Wanggai');

-- ============================================
-- Insert BPDAS User Accounts
-- Password for all: bpdas123
-- ============================================
INSERT INTO users (username, email, password, full_name, phone, role, bpdas_id) VALUES
('bpdas_aceh', 'aceh@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. Ahmad Fauzi', '0651-23456', 'bpdas', 1),
('bpdas_sumut', 'sumut@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Siti Aminah', '061-4567890', 'bpdas', 2),
('bpdas_jambi', 'jambi@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. Bambang Sutrisno', '0741-123456', 'bpdas', 3),
('bpdas_jabar', 'jabar@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Drs. Eko Prasetyo', '022-7564321', 'bpdas', 4),
('bpdas_jateng', 'jateng@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. Retno Wulandari', '024-8765432', 'bpdas', 5),
('bpdas_jatim', 'jatim@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Agus Setiawan', '0341-234567', 'bpdas', 6),
('bpdas_kalsel', 'kalsel@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. Hendra Gunawan', '0511-345678', 'bpdas', 7),
('bpdas_kaltim', 'kaltim@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dra. Sari Indah', '0541-456789', 'bpdas', 8),
('bpdas_sulsel', 'sulsel@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. Muhammad Yusuf', '0411-567890', 'bpdas', 9),
('bpdas_papua', 'papua@bpdas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ir. John Wanggai', '0967-678901', 'bpdas', 10);

-- ============================================
-- Insert Sample Public Users
-- Password for all: user123
-- ============================================
INSERT INTO users (username, email, password, full_name, phone, nik, role) VALUES
('budi_santoso', 'budi.santoso@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', '081234567890', '3201012345670001', 'public'),
('siti_nurhaliza', 'siti.nurhaliza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza', '081234567891', '3301012345670002', 'public'),
('agus_wijaya', 'agus.wijaya@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Agus Wijaya', '081234567892', '3501012345670003', 'public');

-- ============================================
-- Insert 50 Seedling Types (from 138 total)
-- Categories: Pohon Hutan, Pohon Buah, Tanaman Obat, Bambu, Mangrove
-- ============================================
INSERT INTO seedling_types (name, scientific_name, category) VALUES
-- Pohon Hutan (Forest Trees)
('Jati', 'Tectona grandis', 'Pohon Hutan'),
('Mahoni', 'Swietenia macrophylla', 'Pohon Hutan'),
('Sengon', 'Falcataria moluccana', 'Pohon Hutan'),
('Akasia', 'Acacia mangium', 'Pohon Hutan'),
('Jabon', 'Anthocephalus cadamba', 'Pohon Hutan'),
('Meranti', 'Shorea spp.', 'Pohon Hutan'),
('Pinus', 'Pinus merkusii', 'Pohon Hutan'),
('Cemara', 'Casuarina equisetifolia', 'Pohon Hutan'),
('Trembesi', 'Samanea saman', 'Pohon Hutan'),
('Sonokeling', 'Dalbergia latifolia', 'Pohon Hutan'),
('Kayu Putih', 'Melaleuca leucadendra', 'Pohon Hutan'),
('Gmelina', 'Gmelina arborea', 'Pohon Hutan'),
('Karet', 'Hevea brasiliensis', 'Pohon Hutan'),
('Kelapa Sawit', 'Elaeis guineensis', 'Pohon Hutan'),
('Eucalyptus', 'Eucalyptus spp.', 'Pohon Hutan'),

-- Pohon Buah (Fruit Trees)
('Mangga', 'Mangifera indica', 'Pohon Buah'),
('Rambutan', 'Nephelium lappaceum', 'Pohon Buah'),
('Durian', 'Durio zibethinus', 'Pohon Buah'),
('Jeruk', 'Citrus spp.', 'Pohon Buah'),
('Jambu Air', 'Syzygium aqueum', 'Pohon Buah'),
('Jambu Biji', 'Psidium guajava', 'Pohon Buah'),
('Alpukat', 'Persea americana', 'Pohon Buah'),
('Nangka', 'Artocarpus heterophyllus', 'Pohon Buah'),
('Kelengkeng', 'Dimocarpus longan', 'Pohon Buah'),
('Sawo', 'Manilkara zapota', 'Pohon Buah'),
('Belimbing', 'Averrhoa carambola', 'Pohon Buah'),
('Sirsak', 'Annona muricata', 'Pohon Buah'),
('Manggis', 'Garcinia mangostana', 'Pohon Buah'),
('Salak', 'Salacca zalacca', 'Pohon Buah'),
('Sukun', 'Artocarpus altilis', 'Pohon Buah'),

-- Tanaman Obat (Medicinal Plants)
('Jahe', 'Zingiber officinale', 'Tanaman Obat'),
('Kunyit', 'Curcuma longa', 'Tanaman Obat'),
('Lengkuas', 'Alpinia galanga', 'Tanaman Obat'),
('Kencur', 'Kaempferia galanga', 'Tanaman Obat'),
('Temulawak', 'Curcuma xanthorrhiza', 'Tanaman Obat'),
('Kumis Kucing', 'Orthosiphon aristatus', 'Tanaman Obat'),
('Sambiloto', 'Andrographis paniculata', 'Tanaman Obat'),
('Lidah Buaya', 'Aloe vera', 'Tanaman Obat'),
('Sirih', 'Piper betle', 'Tanaman Obat'),
('Mengkudu', 'Morinda citrifolia', 'Tanaman Obat'),

-- Bambu (Bamboo)
('Bambu Petung', 'Dendrocalamus asper', 'Bambu'),
('Bambu Apus', 'Gigantochloa apus', 'Bambu'),
('Bambu Tali', 'Gigantochloa apus', 'Bambu'),
('Bambu Kuning', 'Bambusa vulgaris', 'Bambu'),
('Bambu Hitam', 'Gigantochloa atroviolacea', 'Bambu'),

-- Mangrove
('Bakau', 'Rhizophora mucronata', 'Mangrove'),
('Api-api', 'Avicennia marina', 'Mangrove'),
('Nipah', 'Nypa fruticans', 'Mangrove'),
('Pedada', 'Sonneratia caseolaris', 'Mangrove'),
('Tanjang', 'Bruguiera gymnorrhiza', 'Mangrove');

-- ============================================
-- Insert Stock Data (100+ entries across BPDAS)
-- ============================================

-- BPDAS Krueng Aceh Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(1, 1, 5000, '2024-01-15'),
(1, 2, 3500, '2024-01-15'),
(1, 3, 8000, '2024-01-15'),
(1, 16, 2000, '2024-01-15'),
(1, 17, 1500, '2024-01-15'),
(1, 31, 1000, '2024-01-15'),
(1, 41, 3000, '2024-01-15'),
(1, 46, 4000, '2024-01-15'),
(1, 47, 3500, '2024-01-15'),
(1, 48, 2500, '2024-01-15');

-- BPDAS Asahan Barumun Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(2, 1, 6000, '2024-01-16'),
(2, 4, 4500, '2024-01-16'),
(2, 5, 7000, '2024-01-16'),
(2, 13, 5000, '2024-01-16'),
(2, 14, 8000, '2024-01-16'),
(2, 16, 2500, '2024-01-16'),
(2, 18, 1800, '2024-01-16'),
(2, 19, 2200, '2024-01-16'),
(2, 31, 1200, '2024-01-16'),
(2, 32, 900, '2024-01-16');

-- BPDAS Batanghari Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(3, 1, 7500, '2024-01-17'),
(3, 2, 4000, '2024-01-17'),
(3, 3, 9000, '2024-01-17'),
(3, 6, 3000, '2024-01-17'),
(3, 13, 6000, '2024-01-17'),
(3, 16, 3000, '2024-01-17'),
(3, 20, 2000, '2024-01-17'),
(3, 21, 1500, '2024-01-17'),
(3, 33, 800, '2024-01-17'),
(3, 46, 5000, '2024-01-17');

-- BPDAS Citarum Ciliwung Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(4, 1, 10000, '2024-01-18'),
(4, 2, 8000, '2024-01-18'),
(4, 3, 12000, '2024-01-18'),
(4, 7, 5000, '2024-01-18'),
(4, 9, 4000, '2024-01-18'),
(4, 16, 4000, '2024-01-18'),
(4, 17, 3000, '2024-01-18'),
(4, 22, 2500, '2024-01-18'),
(4, 31, 2000, '2024-01-18'),
(4, 41, 6000, '2024-01-18');

-- BPDAS Pemali Jratun Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(5, 1, 8500, '2024-01-19'),
(5, 3, 10000, '2024-01-19'),
(5, 4, 6000, '2024-01-19'),
(5, 8, 4500, '2024-01-19'),
(5, 16, 3500, '2024-01-19'),
(5, 18, 2800, '2024-01-19'),
(5, 23, 2000, '2024-01-19'),
(5, 31, 1500, '2024-01-19'),
(5, 32, 1200, '2024-01-19'),
(5, 42, 5000, '2024-01-19');

-- BPDAS Brantas Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(6, 1, 9000, '2024-01-20'),
(6, 2, 7000, '2024-01-20'),
(6, 3, 11000, '2024-01-20'),
(6, 5, 8000, '2024-01-20'),
(6, 10, 3500, '2024-01-20'),
(6, 16, 4500, '2024-01-20'),
(6, 19, 3000, '2024-01-20'),
(6, 24, 2200, '2024-01-20'),
(6, 31, 1800, '2024-01-20'),
(6, 43, 4500, '2024-01-20');

-- BPDAS Kapuas Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(7, 1, 6500, '2024-01-21'),
(7, 3, 9500, '2024-01-21'),
(7, 6, 4000, '2024-01-21'),
(7, 13, 7000, '2024-01-21'),
(7, 16, 3200, '2024-01-21'),
(7, 20, 2500, '2024-01-21'),
(7, 25, 1800, '2024-01-21'),
(7, 46, 6000, '2024-01-21'),
(7, 47, 5500, '2024-01-21'),
(7, 48, 4000, '2024-01-21');

-- BPDAS Mahakam Berau Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(8, 1, 7000, '2024-01-22'),
(8, 2, 5500, '2024-01-22'),
(8, 3, 10500, '2024-01-22'),
(8, 6, 4500, '2024-01-22'),
(8, 13, 8000, '2024-01-22'),
(8, 16, 3800, '2024-01-22'),
(8, 21, 2800, '2024-01-22'),
(8, 26, 2000, '2024-01-22'),
(8, 46, 7000, '2024-01-22'),
(8, 49, 5000, '2024-01-22');

-- BPDAS Jeneberang Walanae Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(9, 1, 8000, '2024-01-23'),
(9, 3, 11500, '2024-01-23'),
(9, 4, 7000, '2024-01-23'),
(9, 8, 5000, '2024-01-23'),
(9, 16, 4200, '2024-01-23'),
(9, 17, 3500, '2024-01-23'),
(9, 27, 2500, '2024-01-23'),
(9, 31, 2200, '2024-01-23'),
(9, 41, 7000, '2024-01-23'),
(9, 44, 4000, '2024-01-23');

-- BPDAS Mamberamo Stock
INSERT INTO stock (bpdas_id, seedling_type_id, quantity, last_update_date) VALUES
(10, 1, 5500, '2024-01-24'),
(10, 3, 8500, '2024-01-24'),
(10, 6, 3500, '2024-01-24'),
(10, 7, 4000, '2024-01-24'),
(10, 16, 2800, '2024-01-24'),
(10, 18, 2200, '2024-01-24'),
(10, 28, 1800, '2024-01-24'),
(10, 46, 5500, '2024-01-24'),
(10, 47, 4500, '2024-01-24'),
(10, 50, 3500, '2024-01-24');

-- ============================================
-- Insert Sample Requests
-- ============================================
INSERT INTO requests (request_number, user_id, bpdas_id, seedling_type_id, quantity, purpose, land_area, status) VALUES
('REQ-2024-001', 11, 4, 1, 500, 'Penghijauan lahan pribadi untuk konservasi tanah', 2.5, 'pending'),
('REQ-2024-002', 12, 5, 3, 1000, 'Program penghijauan desa untuk reboisasi', 5.0, 'approved'),
('REQ-2024-003', 13, 6, 16, 200, 'Penanaman pohon buah di lahan pertanian', 1.5, 'pending'),
('REQ-2024-004', 11, 4, 2, 300, 'Reboisasi lahan kritis di daerah perbukitan', 3.0, 'rejected'),
('REQ-2024-005', 12, 6, 17, 150, 'Penanaman pohon buah untuk ketahanan pangan', 1.0, 'approved');

-- Update approved requests
UPDATE requests SET approved_by = 4, approval_date = '2024-01-25 10:30:00', approval_notes = 'Permohonan disetujui. Silakan ambil bibit pada tanggal yang telah ditentukan.' WHERE id = 2;
UPDATE requests SET approved_by = 6, approval_date = '2024-01-26 14:15:00', approval_notes = 'Permohonan disetujui. Harap membawa surat pengantar dari RT/RW.' WHERE id = 5;
UPDATE requests SET approved_by = 4, approval_date = '2024-01-25 16:45:00', rejection_reason = 'Stok bibit mahoni sedang habis. Silakan ajukan permohonan bulan depan.' WHERE id = 4;

-- ============================================
-- Insert Request History
-- ============================================
INSERT INTO request_history (request_id, status, changed_by, notes) VALUES
(2, 'approved', 4, 'Permohonan disetujui setelah verifikasi dokumen'),
(4, 'rejected', 4, 'Stok tidak mencukupi'),
(5, 'approved', 6, 'Permohonan disetujui untuk program ketahanan pangan');
