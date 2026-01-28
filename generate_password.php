<?php
// Ganti 'passwordbaru123' dengan password yang mau kamu pakai
$password_baru = 'passwordbaru123';

// Generate hash dari password baru
$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

// Tampilkan hash password nya
echo "Password: " . $password_baru . "<br>";
echo "Hashed Password: " . $hash_password;
?>