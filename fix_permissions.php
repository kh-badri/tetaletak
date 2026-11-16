<?php
/**
 * Helper script to fix Linux file & directory permissions for vendor and writable
 * Access this via browser: https://tataletak.kbtech.id/fix_permissions.php
 * REMOVE THIS FILE AFTER RUNNING!
 */

echo "<h2>Fixing Permissions...</h2>";

function chmod_r($path, $dirPerm = 0777, $filePerm = 0666) {
    $count = 0;
    if (is_dir($path)) {
        @chmod($path, $dirPerm);
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            @chmod($item->getPathname(), $dirPerm);
        } else {
            @chmod($item->getPathname(), $filePerm);
        }
        $count++;
    }
    return $count;
}

// Ensure writable subdirectories exist
$writableDirs = ['cache', 'logs', 'session', 'uploads', 'debugbar'];
foreach ($writableDirs as $dir) {
    $full = __DIR__ . '/writable/' . $dir;
    if (!is_dir($full)) {
        @mkdir($full, 0777, true);
    }
    @chmod($full, 0777);
}

if (is_dir(__DIR__ . '/vendor')) {
    $countV = chmod_r(__DIR__ . '/vendor', 0755, 0644);
    echo "<p style='color:green'>✔ Folder <strong>vendor/</strong> dan {$countV} berkas di dalamnya berhasil diatur ke 755 / 644.</p>";
}

if (is_dir(__DIR__ . '/writable')) {
    $countW = chmod_r(__DIR__ . '/writable', 0777, 0666);
    echo "<p style='color:green'>✔ Folder <strong>writable/</strong> dan {$countW} berkas di dalamnya berhasil diatur ke 777 / 666.</p>";
}

// Test write to writable/cache
$testFile = __DIR__ . '/writable/cache/test_' . time() . '.txt';
$fp = @fopen($testFile, 'ab');
if ($fp) {
    fclose($fp);
    @unlink($testFile);
    echo "<p style='color:green; font-weight:bold'>✔ Test tulis ke writable/cache/ BERHASIL!</p>";
} else {
    echo "<p style='color:red; font-weight:bold'>✖ Test tulis ke writable/cache/ GAGAL! Periksa owner folder writable.</p>";
}

echo "<hr><p><strong>Selesai!</strong> Silakan hapus file ini (<code>fix_permissions.php</code>) dari cPanel lalu buka website utama.</p>";

