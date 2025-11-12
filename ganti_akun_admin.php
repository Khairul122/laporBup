<?php
// File untuk mengganti akun admin
// Database configuration
$host = 'localhost';
$dbname = 'helpdesk';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Data admin baru
    $new_username = 'Aquamenbajubaru2025';
    $new_password_plain = 'Botolminumbelidipasar2025';
    $email = 'admin@gmail.com'; // Email yang sudah ada

    // Hash password baru
    $password_hash = password_hash($new_password_plain, PASSWORD_DEFAULT);

    // Update query untuk admin (id_user = 1)
    $sql = "UPDATE users SET
            username = :username,
            password = :password,
            jabatan = 'Administrator',
            updated_at = CURRENT_TIMESTAMP
            WHERE id_user = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $new_username);
    $stmt->bindParam(':password', $password_hash);

    // Execute query
    if ($stmt->execute()) {
        echo "<h2>Sukses!</h2>";
        echo "<p>Akun admin telah berhasil diperbarui:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> " . htmlspecialchars($new_username) . "</li>";
        echo "<li><strong>Password:</strong> " . htmlspecialchars($new_password_plain) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>";
        echo "</ul>";
        echo "<p><strong>Catatan:</strong> Simpan kredensial ini dengan aman!</p>";
        echo "<p><a href='index.php'>Klik di sini untuk login</a></p>";
    } else {
        echo "<h2>Error!</h2>";
        echo "<p>Gagal memperbarui akun admin.</p>";
    }

} catch(PDOException $e) {
    echo "<h2>Database Error!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Pastikan database helpdesk ada dan konfigurasi database benar.</p>";
}
?>