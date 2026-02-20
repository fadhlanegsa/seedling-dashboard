-- FIX TOTAL: Mengubah Tipe Kolom Kategori dan Mengisi Datanya
-- Jalanin script ini di tab SQL phpMyAdmin di hostingan lo

-- 1. UBAH TIPE DATA KOLOM DULU (PENTING!)
-- Ini yang bikin error "Data truncated" kemarin karena tipe datanya ENUM terbatas.
-- Kita ubah jadi VARCHAR biar bisa nerima teks apa aja sesuai config baru.
ALTER TABLE seedling_types MODIFY COLUMN category VARCHAR(100);

-- 2. UPDATE KATEGORI (Sama kayak script sebelumnya, tapi sekarang pasti sukses)

-- Update 'Tanaman Kayu-Kayuan'
UPDATE seedling_types SET category = 'Tanaman Kayu-Kayuan' WHERE name IN (
    'Jati', 'Mahoni', 'Sengon', 'Akasia', 'Jabon', 'Meranti', 'Pinus', 'Cemara', 
    'Trembesi', 'Sonokeling', 'Kayu Putih', 'Gmelina', 'Karet', 'Kelapa Sawit', 
    'Eucalyptus', 'Agathis', 'Ampupu', 'Angsana', 'Balsa', 'Damar', 'Gaharu', 
    'Jati Mas', 'Jati Merah', 'Kenari', 'Kesambi', 'Ketapang', 'Klampok', 
    'Lamtoro', 'Mahoni Daun Besar', 'Mindi', 'Nyamplung', 'Pulai', 'Rasamala', 
    'Saga', 'Salam', 'Suren', 'Tanjung', 'Trembesi', 'Ulin'
);

-- Update 'HHBK'
UPDATE seedling_types SET category = 'HHBK' WHERE name IN (
    'Mangga', 'Rambutan', 'Durian', 'Jeruk', 'Jambu Air', 'Jambu Biji', 'Alpukat', 
    'Nangka', 'Kelengkeng', 'Sawo', 'Belimbing', 'Sirsak', 'Manggis', 'Salak', 
    'Sukun', 'Alpukat vegetatif', 'Aren', 'Asam Jawa', 'Cempedak', 'Duku', 
    'Jengkol', 'Kedondong', 'Kemiri', 'Kepel', 'Kopi', 'Matoa', 'Melinjo', 
    'Menteng', 'Namnam', 'Pati', 'Petai', 'Pinang', 'Sagu', 'Srikaya'
);

-- Update 'Tanaman Obat'
UPDATE seedling_types SET category = 'Tanaman Obat' WHERE name IN (
    'Jahe', 'Kunyit', 'Lengkuas', 'Kencur', 'Temulawak', 'Kumis Kucing', 
    'Sambiloto', 'Lidah Buaya', 'Sirih', 'Mengkudu', 'Kayu Manis', 'Kelor', 
    'Mahkota Dewa', 'Rosella', 'Sera'
);

-- Update 'Bambu'
UPDATE seedling_types SET category = 'Bambu' WHERE name LIKE 'Bambu%';

-- Update 'Mangrove'
UPDATE seedling_types SET category = 'Mangrove' WHERE name IN (
    'Bakau', 'Api-api', 'Nipah', 'Pedada', 'Tanjang', 'Bruguiera', 'Rhizophora', 
    'Avicennia', 'Sonneratia', 'Ceriops'
);

-- Update 'Estetika, Pakan, Dll'
UPDATE seedling_types SET category = 'Estetika, Pakan, Dll' WHERE name IN (
    'Asoka', 'Bougenville', 'Glodokan Tiang', 'Kaliandra', 'Ketapang Kencana', 
    'Palem', 'Pucuk Merah', 'Rumput Gajah', 'Tabebuya'
);
