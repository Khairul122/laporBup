<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f6fa;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            text-align: center;
            background: #fff;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }
        .box h1 {
            font-size: 64px;
            margin: 0;
            color: #2F58CD;
        }
        .box p {
            font-size: 16px;
            margin: 10px 0 20px;
        }
        .box a {
            display: inline-block;
            padding: 10px 24px;
            background: #2F58CD;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>404</h1>
        <p><?= htmlspecialchars($message ?? 'Halaman atau aksi yang Anda tuju tidak ditemukan.') ?></p>
        <a href="<?= url('index.php') ?>">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>
