-- Fix Missing Categories
-- Run this in your hosting phpMyAdmin to fix missing categories

-- 1. Update known types to 'Tanaman Kayu-Kayuan'
UPDATE seedling_types SET category = 'Tanaman Kayu-Kayuan' WHERE name IN (
    'Jati', 'Mahoni', 'Sengon', 'Akasia', 'Jabon', 'Meranti', 'Pinus', 'Cemara', 
    'Trembesi', 'Sonokeling', 'Kayu Putih', 'Gmelina', 'Karet', 'Kelapa Sawit', 
    'Eucalyptus', 'Agathis', 'Ampupu', 'Angsana', 'Balsa', 'Damar', 'Gaharu', 
    'Jati Mas', 'Jati Merah', 'Kenari', 'Kesambi', 'Ketapang', 'Klampok', 
    'Lamtoro', 'Mahoni Daun Besar', 'Mindi', 'Nyamplung', 'Pulai', 'Rasamala', 
    'Saga', 'Salam', 'Suren', 'Tanjung', 'Trembesi', 'Ulin'
);

-- 2. Update known types to 'HHBK' (Hasil Hutan Bukan Kayu / Buah-buahan)
UPDATE seedling_types SET category = 'HHBK' WHERE name IN (
    'Mangga', 'Rambutan', 'Durian', 'Jeruk', 'Jambu Air', 'Jambu Biji', 'Alpukat', 
    'Nangka', 'Kelengkeng', 'Sawo', 'Belimbing', 'Sirsak', 'Manggis', 'Salak', 
    'Sukun', 'Alpukat vegetatif', 'Aren', 'Asam Jawa', 'Cempedak', 'Duku', 
    'Jengkol', 'Kedondong', 'Kemiri', 'Kepel', 'Kopi', 'Matoa', 'Melinjo', 
    'Menteng', 'Namnam', 'Pati', 'Petai', 'Pinang', 'Sagu', 'Srikaya'
);

-- 3. Update known types to 'Tanaman Obat'
UPDATE seedling_types SET category = 'Tanaman Obat' WHERE name IN (
    'Jahe', 'Kunyit', 'Lengkuas', 'Kencur', 'Temulawak', 'Kumis Kucing', 
    'Sambiloto', 'Lidah Buaya', 'Sirih', 'Mengkudu', 'Kayu Manis', 'Kelor', 
    'Mahkota Dewa', 'Rosella', 'Sera'
);

-- 4. Update known types to 'Bambu'
UPDATE seedling_types SET category = 'Bambu' WHERE name LIKE 'Bambu%';

-- 5. Update known types to 'Mangrove'
UPDATE seedling_types SET category = 'Mangrove' WHERE name IN (
    'Bakau', 'Api-api', 'Nipah', 'Pedada', 'Tanjang', 'Bruguiera', 'Rhizophora', 
    'Avicennia', 'Sonneratia', 'Ceriops'
);

-- 6. Update known types to 'Estetika, Pakan, Dll'
UPDATE seedling_types SET category = 'Estetika, Pakan, Dll' WHERE name IN (
    'Asoka', 'Bougenville', 'Glodokan Tiang', 'Kaliandra', 'Ketapang Kencana', 
    'Palem', 'Pucuk Merah', 'Rumput Gajah', 'Tabebuya'
);

-- 7. OPTIONAL: Set a default for ANY remaining empty categories
-- Remove the '--' at the beginning of the line below to run it
-- UPDATE seedling_types SET category = 'Tanaman Kayu-Kayuan' WHERE category IS NULL OR category = '';
