<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Rekomendasi Tata Letak Produk (Apriori)</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .animate-float-slow {
            animation: floatSlow 4s ease-in-out infinite;
        }
        @keyframes dashMove {
            to { stroke-dashoffset: -12; }
        }
        .animate-dash {
            animation: dashMove 1.2s linear infinite;
        }
    </style>
</head>

<!-- Latar belakang: Biru Muda Modern Clean -->
<body class="bg-gradient-to-br from-[#F0F9FF] via-[#E0F2FE] to-[#EFF6FF] min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden text-slate-800">

    <!-- Ornamen Lingkaran Latar Belakang -->
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-sky-300/30 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-indigo-300/25 blur-3xl pointer-events-none"></div>

    <!-- Container Utama -->
    <div class="w-full max-w-lg relative z-10">

        <!-- Kartu Registrasi -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-xl shadow-sky-900/10 overflow-hidden border border-sky-100 transition-all duration-300">
            
            <!-- Header Kartu -->
            <div class="p-6 sm:p-10 pb-4 text-center">
                
                <!-- Badge Aplikasi Tata Letak -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-50 border border-sky-200 text-xs font-semibold text-sky-700 mb-4">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    Sistem Tata Letak &bull; Pendaftaran Pengguna
                </div>

                <!-- SVG Ilustrasi Registrasi Akun & Tata Letak Rak Toko (Biru Muda Modern) -->
                <div class="flex justify-center mb-4">
                    <div class="relative p-2 animate-float-slow">
                        <svg class="w-24 h-24 sm:w-28 sm:h-28 drop-shadow-md" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="56" fill="#F8FAFC" stroke="#BAE6FD" stroke-width="2"/>
                            <circle cx="60" cy="60" r="48" fill="#E0F2FE" fill-opacity="0.6"/>
                            
                            <!-- Tiang Rak Toko -->
                            <rect x="25" y="27" width="4.5" height="66" rx="2.25" fill="#334155"/>
                            <rect x="90.5" y="27" width="4.5" height="66" rx="2.25" fill="#334155"/>
                            
                            <!-- Kanopi Atas Rak -->
                            <rect x="22" y="24" width="76" height="6" rx="3" fill="#0284C7"/>
                            
                            <!-- Rak Tingkat 1 -->
                            <rect x="27" y="45" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="34" y="33" width="9" height="12" rx="2" fill="#0EA5E9"/>
                            <rect x="46" y="35" width="8" height="10" rx="1.5" fill="#7DD3FC"/>
                            <rect x="57" y="32" width="10" height="13" rx="2" fill="#6366F1"/>
                            <rect x="70" y="34" width="8" height="11" rx="1.5" fill="#BAE6FD"/>
                            
                            <!-- Rak Tingkat 2 -->
                            <rect x="27" y="67" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="33" y="54" width="11" height="13" rx="2" fill="#BAE6FD"/>
                            <rect x="47" y="53" width="10" height="14" rx="2" fill="#0284C7"/>
                            <rect x="60" y="51" width="12" height="16" rx="2" fill="#38BDF8" stroke="#0369A1" stroke-width="1.5"/>
                            
                            <!-- Rak Tingkat 3 -->
                            <rect x="27" y="89" width="66" height="3.5" rx="1.75" fill="#475569"/>
                            <rect x="33" y="76" width="13" height="13" rx="2" fill="#0284C7"/>
                            <rect x="49" y="78" width="10" height="11" rx="2" fill="#93C5FD"/>
                            <rect x="62" y="76" width="9" height="13" rx="2" fill="#6366F1"/>
                            
                            <!-- Jalur Asosiasi Apriori Animasi -->
                            <path d="M 38 39 C 20 54, 28 64, 58 60" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3" class="animate-dash"/>
                            <polygon points="61,60 55,57 56,63" fill="#0284C7"/>

                            <!-- Badge Registrasi User -->
                            <g transform="translate(68, 68)">
                                <circle cx="22" cy="22" r="18" fill="#0284C7" stroke="#ffffff" stroke-width="2.5"/>
                                <circle cx="20" cy="18" r="4" fill="#ffffff"/>
                                <path d="M13 27c0-3.3 3.1-6 7-6 .9 0 1.7.1 2.5.4" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M27 22v6M24 25h6" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Judul Halaman -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Buat Akun Baru
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                    Daftar untuk mengakses analisis pola pembelian dan rekomendasi tata letak produk
                </p>
            </div>

            <!-- Form Body -->
            <div class="px-6 sm:px-10 pb-8 sm:pb-10">

                <!-- Alert Notifikasi Error Validasi -->
                <?php 
                    $validation = \Config\Services::validation();
                    $errors = session()->getFlashdata('errors') ?? [];
                    $singleError = session()->getFlashdata('error');
                    $allErrors = array_merge(
                        $errors, 
                        $validation->getErrors(), 
                        $singleError ? [$singleError] : []
                    );
                ?>

                <?php if (!empty($allErrors)): ?>
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-red-800 text-sm shadow-sm">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold">Mohon periksa data formulir Anda:</p>
                                <ul class="list-disc list-inside mt-1.5 space-y-1 text-xs sm:text-sm">
                                    <?php foreach ($allErrors as $err): ?>
                                        <li><?= esc($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('register') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>

                    <!-- Input Username -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Username
                            </label>
                            <span class="text-[11px] text-slate-400">Min. 3 karakter</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sky-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   value="<?= old('username') ?>" 
                                   class="w-full pl-11 pr-4 py-3 bg-sky-50/40 border border-sky-200 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent focus:bg-white transition placeholder-slate-400 text-sm"
                                   placeholder="Pilih username unik">
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Password
                            </label>
                            <span class="text-[11px] text-slate-400">Min. 6 karakter</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sky-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   class="w-full pl-11 pr-11 py-3 bg-sky-50/40 border border-sky-200 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent focus:bg-white transition placeholder-slate-400 text-sm"
                                   placeholder="Buat kata sandi yang aman">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password', 'eye-icon-pass')" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-sky-600 focus:outline-none transition">
                                <svg id="eye-icon-pass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div>
                        <label for="password_confirm" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sky-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <input type="password" 
                                   id="password_confirm" 
                                   name="password_confirm" 
                                   required 
                                   class="w-full pl-11 pr-11 py-3 bg-sky-50/40 border border-sky-200 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent focus:bg-white transition placeholder-slate-400 text-sm"
                                   placeholder="Ulangi kata sandi Anda">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirm', 'eye-icon-confirm')" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-sky-600 focus:outline-none transition">
                                <svg id="eye-icon-confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Daftar -->
                    <button type="submit" 
                            class="w-full mt-2 bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white py-3.5 px-4 rounded-xl transition-all duration-300 font-bold shadow-lg shadow-sky-500/25 hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2 group text-sm sm:text-base">
                        <span>Daftar Akun Baru</span>
                        <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </button>

                    <!-- Link ke Login -->
                    <div class="text-center pt-4 border-t border-sky-100 mt-4">
                        <p class="text-slate-500 text-sm">
                            Sudah memiliki akun?
                            <a href="<?= site_url('login') ?>" class="text-[#0284C7] hover:text-[#0369A1] font-semibold underline underline-offset-2 transition">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer Ringkas -->
            <div class="bg-sky-50/70 py-3 text-center border-t border-sky-100">
                <span class="text-xs text-slate-500 font-medium">
                    Sistem Tata Letak Toko &bull; Algoritma Apriori
                </span>
            </div>
        </div>

    </div>

    <!-- Script Toggle Password -->
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>

</body>
</html>
