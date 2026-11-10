<?php
// Script otomatis merapikan folder gambar produk di cPanel
header('Content-Type: text/html; charset=utf-8');

$base = __DIR__; // biasanya /home/rhst3547/public_html
$storageDir = $base . '/storage';
$targetDir = $storageDir . '/products';

if (!file_exists($storageDir)) {
    mkdir($storageDir, 0755, true);
}
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Cek folder 3..10 di berbagai kemungkinan lokasi
$possibleSources = [
    $storageDir,                     // /public_html/storage/3...
    $base,                           // /public_html/3...
    dirname($base) . '/laravel/storage/app/public', // /laravel/storage/app/public/3...
];

$moved = 0;
foreach ($possibleSources as $src) {
    if (!is_dir($src)) continue;
    $items = scandir($src);
    foreach ($items as $item) {
        if (in_array($item, ['.', '..', 'products'])) continue;
        // Jika item adalah angka (folder ID produk seperti 3, 4, 5, dst)
        if (is_numeric($item) && is_dir("$src/$item")) {
            $dest = "$targetDir/$item";
            if (!file_exists($dest)) {
                rename("$src/$item", $dest);
                $moved++;
            }
        }
    }
}

// Juga pastikan tersinkron ke /laravel/storage/app/public/products jika folder laravel ada
$laravelStorage = dirname($base) . '/laravel/storage/app/public/products';
if (is_dir(dirname($laravelStorage))) {
    if (!file_exists($laravelStorage)) {
        @mkdir($laravelStorage, 0755, true);
    }
}

echo '<div style="font-family:sans-serif;padding:30px;background:#101827;color:#fff;border-radius:12px;max-width:650px;margin:40px auto;text-align:center;">';
echo '<h2 style="color:#34d399;margin-bottom:10px;">✓ PENATAAN FOTO SELESAI!</h2>';
echo '<p style="color:#9ca3af;font-size:15px;line-height:1.6;">Folder foto produk telah diposisikan dengan benar di <code>public_html/storage/products/</code>.</p>';

// Cek foto yang ada di target
$foundImages = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $f) {
        if (is_numeric($f) && is_dir("$targetDir/$f")) {
            $sub = scandir("$targetDir/$f");
            foreach ($sub as $img) {
                if (str_ends_with($img, '.webp') || str_ends_with($img, '.jpg') || str_ends_with($img, '.png')) {
                    $foundImages[] = "storage/products/$f/$img";
                }
            }
        }
    }
}

echo '<p style="color:#60a5fa;">Ditemukan <b>' . count($foundImages) . ' file foto</b> di jalur penyimpanan.</p>';
echo '<a href="/katalog" style="display:inline-block;margin-top:20px;padding:12px 24px;background:#3b82f6;color:#fff;text-decoration:none;font-weight:bold;border-radius:8px;">Lihat Katalog Sekarang →</a>';
echo '</div>';
