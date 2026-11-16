<?php
/**
 * Helper script to fix Linux file & directory permissions for vendor and writable
 * Access this via browser: https://tataletak.kbtech.id/fix_permissions.php
 * REMOVE THIS FILE AFTER RUNNING!
 */

echo "<h2>Fixing Permissions...</h2>";

function chmod_r($path, $dirPerm = 0755, $filePerm = 0644) {
    $count = 0;
    if (is_dir($path)) {
        chmod($path, $dirPerm);
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

if (is_dir(__DIR__ . '/vendor')) {
    $countV = chmod_r(__DIR__ . '/vendor', 0755, 0644);
    echo "<p style='color:green'>✔ Folder <strong>vendor/</strong> dan {$countV} berkas di dalamnya berhasil diatur ke 755 / 644.</p>";
} else {
    echo "<p style='color:red'>✖ Folder vendor/ tidak ditemukan.</p>";
}

if (is_dir(__DIR__ . '/writable')) {
    $countW = chmod_r(__DIR__ . '/writable', 0775, 0664);
    echo "<p style='color:green'>✔ Folder <strong>writable/</strong> dan {$countW} berkas di dalamnya berhasil diatur ke 775 / 664.</p>";
}

echo "<hr><p><strong>Selesai!</strong> Silakan hapus file ini (<code>fix_permissions.php</code>) dari cPanel lalu buka website utama.</p>";
