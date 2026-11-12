<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- RUTE YANG WAJIB LOGIN (Dijaga oleh 'auth') ---
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Rute utama aplikasi
    $routes->get('/', 'Home::index');
    $routes->addRedirect('home', '/'); // Alihkan 'home' ke '/'

    // Rute untuk halaman Akun
    $routes->get('akun', 'Akun::index');
    $routes->post('akun/update_profil', 'Akun::updateProfil');
    $routes->post('akun/update_sandi', 'Akun::updateSandi');

    // --- RUTE UNTUK DATASET TRANSAKSI & MASTER PRODUK ---
    $routes->get('dataset', 'Dataset::index');
    $routes->post('dataset/save', 'Dataset::save');               // Tambah transaksi manual
    $routes->post('dataset/upload', 'Dataset::upload');           // Upload CSV transaksi sekaligus
    $routes->get('dataset/export', 'Dataset::export');            // Export CSV transaksi
    $routes->delete('dataset/hapusSemua', 'Dataset::hapusSemua'); // Hapus semua transaksi
    $routes->delete('dataset/delete/(:num)', 'Dataset::delete/$1'); // Hapus satu baris transaksi

    // Rute Master Produk Sembako (CRUD 95 Produk)
    $routes->post('dataset/produk/save', 'Dataset::saveProduk');
    $routes->post('dataset/produk/update/(:num)', 'Dataset::updateProduk/$1');
    $routes->delete('dataset/produk/delete/(:num)', 'Dataset::deleteProduk/$1');

    // --- RUTE REKOMENDASI TATA LETAK PRODUK (ALGORITMA APRIORI) ---
    $routes->get('rekomendasi', 'Rekomendasi::index');
    $routes->post('rekomendasi/proses', 'Rekomendasi::proses');
    $routes->post('rekomendasi/simpan', 'Rekomendasi::simpan');

    // Rute Kompatibilitas / Alias Analisis ke Rekomendasi
    $routes->addRedirect('analisis', 'rekomendasi');
    $routes->post('analisis/proses', 'Rekomendasi::proses');
    $routes->post('analisis/simpan', 'Rekomendasi::simpan');

// Rute untuk Halaman History
$routes->get('history', 'History::index');
$routes->get('history/delete/(:num)', 'History::delete/$1'); // Hapus pakai method GET agar form-nya mudah
$routes->get('history/detail/(:num)', 'History::detail/$1');

    // ... (kode routes lainnya)


});


// --- RUTE UNTUK TAMU (Dijaga oleh 'guest') ---
$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('login', 'Auth::index', ['as' => 'login']);
    $routes->get('register', 'Auth::register', ['as' => 'register']);
});


// --- RUTE AKSI PUBLIK (Proses Login, Register, Logout) ---
$routes->post('login', 'Auth::login');
$routes->post('register', 'Auth::processRegister');
$routes->get('logout', 'Auth::logout');
