<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Rekomendasi Tata Letak Produk | Apriori') ?></title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts (Tailwind & Alpine.js) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js" defer></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #F0F9FF;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.45s, visibility 0.45s;
        }

        #page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .spinner {
            border: 6px solid #BAE6FD;
            border-top: 6px solid #0284C7;
            border-radius: 50%;
            width: 64px;
            height: 64px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#F0F7FF] text-slate-800 antialiased min-h-screen overflow-x-hidden">

    <!-- Page Loader -->
    <div id="page-loader">
        <div class="spinner"></div>
    </div>

    <div class="min-h-screen flex flex-col">

        <!-- =================================================================== -->
        <!-- NAVBAR MODERN BIRU MUDA CLEAN -->
        <!-- =================================================================== -->
        <nav x-data="{ mobileMenuOpen: false, profileMenuOpen: false }" class="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-sky-100">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Logo & Brand (Kiri) -->
                    <div class="flex items-center">
                        <a href="<?= base_url('/') ?>" class="flex-shrink-0 flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-[#0284C7] text-white flex items-center justify-center p-2 shadow-sm shadow-sky-500/20 group-hover:scale-105 transition">
                                <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                                </svg>
                            </div>
                            <div class="hidden sm:block">
                                <span class="text-slate-900 text-lg font-extrabold tracking-tight block leading-none">Tata Letak Produk</span>
                                <span class="text-[11px] font-bold text-[#0284C7] tracking-wider uppercase">Algoritma Apriori</span>
                            </div>
                        </a>
                    </div>

                    <!-- Grup Kanan -->
                    <div class="flex items-center">
                        
                        <!-- Navigasi Desktop -->
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-center space-x-2">
                                <a href="<?= base_url('/') ?>" class="transition duration-200 px-3.5 py-2 rounded-xl text-sm font-semibold <?= ($active_menu ?? '') === 'home' ? 'bg-[#0284C7] text-white shadow-sm shadow-sky-500/25' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Home</a>
                                <a href="<?= base_url('/dataset') ?>" class="transition duration-200 px-3.5 py-2 rounded-xl text-sm font-semibold <?= ($active_menu ?? '') === 'dataset' ? 'bg-[#0284C7] text-white shadow-sm shadow-sky-500/25' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Dataset</a>
                                <a href="<?= base_url('/rekomendasi') ?>" class="transition duration-200 px-3.5 py-2 rounded-xl text-sm font-semibold <?= in_array(($active_menu ?? ''), ['rekomendasi', 'analisis']) ? 'bg-[#0284C7] text-white shadow-sm shadow-sky-500/25' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Rekomendasi</a>
                                <a href="<?= base_url('/history') ?>" class="transition duration-200 px-3.5 py-2 rounded-xl text-sm font-semibold <?= ($active_menu ?? '') === 'history' ? 'bg-[#0284C7] text-white shadow-sm shadow-sky-500/25' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">History</a>
                            </div>
                        </div>

                        <!-- Dropdown Profil Desktop -->
                        <div class="hidden md:block ml-5">
                            <div class="relative">
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="max-w-xs bg-sky-50 rounded-full p-0.5 border border-sky-200 flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                    <span class="sr-only">Buka menu pengguna</span>
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                         src="<?= base_url('uploads/foto_profil/' . esc(session()->get('foto'))) ?>" 
                                         alt="Foto Profil"
                                         onerror="this.src='https://placehold.co/32x32/0284C7/FFFFFF?text=U'; this.onerror=null;">
                                </button>
                                <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg py-1.5 bg-white ring-1 ring-sky-100 focus:outline-none border border-sky-100" x-cloak>
                                    <div class="px-4 py-2 border-b border-sky-50">
                                        <p class="text-sm font-bold text-slate-800"><?= esc(session()->get('username')) ?></p>
                                        <p class="text-xs text-sky-600 font-medium">Pengguna Sistem</p>
                                    </div>
                                    <a href="<?= base_url('/akun') ?>" class="block px-4 py-2 text-sm text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]">Pengaturan Akun</a>
                                    <a href="<?= site_url('logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</a>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Hamburger Mobile -->
                        <div class="ml-4 -mr-2 flex md:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="bg-sky-50 inline-flex items-center justify-center p-2 rounded-xl text-[#0284C7] hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <span class="sr-only">Buka menu utama</span>
                                <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Menu Mobile -->
            <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-sky-100 shadow-lg" x-cloak>
                <div class="px-3 pt-2 pb-3 space-y-1">
                    <a href="<?= base_url('/') ?>" class="transition duration-200 block px-3.5 py-2.5 rounded-xl text-base font-semibold <?= ($active_menu ?? '') === 'home' ? 'bg-[#0284C7] text-white' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Home</a>
                    <a href="<?= base_url('/dataset') ?>" class="transition duration-200 block px-3.5 py-2.5 rounded-xl text-base font-semibold <?= ($active_menu ?? '') === 'dataset' ? 'bg-[#0284C7] text-white' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Dataset</a>
                    <a href="<?= base_url('/rekomendasi') ?>" class="transition duration-200 block px-3.5 py-2.5 rounded-xl text-base font-semibold <?= in_array(($active_menu ?? ''), ['rekomendasi', 'analisis']) ? 'bg-[#0284C7] text-white' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">Rekomendasi</a>
                    <a href="<?= base_url('/history') ?>" class="transition duration-200 block px-3.5 py-2.5 rounded-xl text-base font-semibold <?= ($active_menu ?? '') === 'history' ? 'bg-[#0284C7] text-white' : 'text-slate-600 hover:bg-sky-50 hover:text-[#0284C7]' ?>">History</a>
                </div>
                <div class="pt-4 pb-3 border-t border-sky-100">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full object-cover border border-sky-200" 
                                 src="<?= base_url('uploads/foto_profil/' . esc(session()->get('foto'))) ?>" 
                                 alt="Foto Profil"
                                 onerror="this.src='https://placehold.co/40x40/0284C7/FFFFFF?text=U'; this.onerror=null;">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-bold leading-none text-slate-800"><?= esc(session()->get('username')) ?></div>
                            <div class="text-xs font-medium text-sky-600 mt-1">Pengguna Sistem</div>
                        </div>
                    </div>
                    <div class="mt-3 px-3 space-y-1">
                        <a href="<?= base_url('/akun') ?>" class="block px-3.5 py-2 rounded-xl text-base font-medium text-slate-600 hover:text-[#0284C7] hover:bg-sky-50">Pengaturan Akun</a>
                        <a href="<?= site_url('logout') ?>" class="block px-3.5 py-2 rounded-xl text-base font-medium text-red-600 hover:bg-red-50">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Konten Utama Aplikasi -->
        <main class="flex-1 w-full">
            <?= $this->renderSection('content') ?>
        </main>

    </div>

    <!-- Script untuk Page Loader dan Notifikasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('load', function() {
            document.getElementById('page-loader').classList.add('hidden');
        });

        <?php if ($success = session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '<?= esc($success, 'js') ?>',
                timer: 2500,
                showConfirmButton: false,
                background: '#fff',
                color: '#0F172A',
                iconColor: '#0284C7'
            });
        <?php endif; ?>
    </script>

</body>

</html>
