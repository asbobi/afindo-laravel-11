<?php

function generateHtaccess($dir) {
    // Daftar folder yang dikecualikan
    $excludedDirs = ['.git', 'node_modules', 'vendor', 'storage', 'public'];

    if (in_array(basename($dir), $excludedDirs)) {
        return;
    }

    // Path ke file .htaccess
    $htaccessFile = $dir . '/.htaccess';
    $newRule = "Options -Indexes";

    // Jika file htaccess sudah ada, cek apakah sudah ada rule Options -Indexes
    if (file_exists($htaccessFile)) {
        $currentContent = file_get_contents($htaccessFile);

        // Jika aturan Options -Indexes belum ada, tambahkan
        if (strpos($currentContent, $newRule) === false) {
            $currentContent .= "\n" . $newRule . "\n";
            file_put_contents($htaccessFile, $currentContent);
            echo "Merged: " . $htaccessFile . "\n";
        }
    } else {
        // Buat file baru dengan Options -Indexes jika belum ada
        file_put_contents($htaccessFile, $newRule . "\n");
        echo "Generated: " . $htaccessFile . "\n";
    }

    // Loop semua subfolder
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $path = $dir . '/' . $file;

        // Proses subfolder
        if (is_dir($path)) {
            generateHtaccess($path);
        }
    }
}

// Jalankan fungsi untuk root folder Laravel Anda
generateHtaccess(__DIR__);

