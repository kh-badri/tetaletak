<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Rekomendasi Tata Letak Produk (Apriori)</title>
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
    </style>
</head>

<!-- Latar belakang: #FFE1AF (Krem Hangat) -->
<body class="bg-[#FFE1AF] min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden">

    <!-- Ornamen Lingkaran Latar Belakang -->
    <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-[#E2B59A]/30 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-[#B77466]/20 blur-3xl pointer-events-none"></div>

    <!-- Container Utama -->
    <div class="w-full max-w-lg relative z-10">

        <!-- Kartu Login -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-[#E2B59A]/40 transition-all duration-300">
            
            <!-- Header Kartu -->
            <div class="p-6 sm:p-10 pb-4 text-center">
                
                <!-- Badge Aplikasi Tata Letak -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#FFE1AF]/60 border border-[#E2B59A] text-xs font-semibold text-[#957C62] mb-4">
                    <span class="w-2 h-2 rounded-full bg-[#B77466] animate-pulse"></span>
                    Algoritma Apriori • Market Basket Analysis
                </div>

                <!-- SVG Ilustrasi Tata Letak Produk & Pola Pembelian -->
                <div class="flex justify-center mb-4">
                    <div class="relative p-2 animate-float-slow">
                        <svg class="w-24 h-24 sm:w-28 sm:h-28" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background Circle with subtle ring -->
                            <circle cx="60" cy="60" r="56" fill="#FFF7ED" stroke="#E2B59A" stroke-width="2"/>
                            <circle cx="60" cy="60" r="48" fill="#FFE1AF" fill-opacity="0.5"/>
                            
                            <!-- Tiang Rak Toko (Tata Letak) -->
                            <rect x="25" y="27" width="4.5" height="66" rx="2.25" fill="#957C62"/>
                            <rect x="90.5" y="27" width="4.5" height="66" rx="2.25" fill="#957C62"/>
                            
                            <!-- Kanopi Atas Rak -->
                            <rect x="22" y="24" width="76" height="6" rx="3" fill="#B77466"/>
                            
                            <!-- Rak Tingkat 1 (Atas) -->
                            <rect x="27" y="45" width="66" height="3.5" rx="1.75" fill="#957C62"/>
                            <!-- Produk Rak Atas -->
                            <rect x="34" y="33" width="9" height="12" rx="2" fill="#B77466"/>
                            <rect x="46" y="35" width="8" height="10" rx="1.5" fill="#E2B59A"/>
                            <rect x="57" y="32" width="10" height="13" rx="2" fill="#B77466"/>
                            <rect x="70" y="34" width="8" height="11" rx="1.5" fill="#E2B59A"/>
                            
                            <!-- Rak Tingkat 2 (Tengah) -->
                            <rect x="27" y="67" width="66" height="3.5" rx="1.75" fill="#957C62"/>
                            <!-- Produk Rak Tengah (Asosiasi Tata Letak) -->
                            <rect x="33" y="54" width="11" height="13" rx="2" fill="#E2B59A"/>
                            <rect x="47" y="53" width="10" height="14" rx="2" fill="#B77466"/>
                            <!-- Produk Rekomendasi Berdampingan -->
                            <rect x="60" y="51" width="12" height="16" rx="2" fill="#B77466" stroke="#957C62" stroke-width="1.5"/>
                            
                            <!-- Rak Tingkat 3 (Bawah) -->
                            <rect x="27" y="89" width="66" height="3.5" rx="1.75" fill="#957C62"/>
                            <!-- Produk Rak Bawah -->
                            <rect x="33" y="76" width="13" height="13" rx="2" fill="#B77466"/>
                            <rect x="49" y="78" width="10" height="11" rx="2" fill="#E2B59A"/>
                            <rect x="62" y="76" width="9" height="13" rx="2" fill="#957C62"/>
                            
                            <!-- Jalur Asosiasi Apriori (Garis Hubung Pola Pembelian) -->
                            <path d="M 38 39 C 20 54, 28 64, 58 60" stroke="#B77466" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3"/>
                            <polygon points="61,60 55,57 56,63" fill="#B77466"/>

                            <!-- Badge Keranjang Belanja (Pola Pembelian Konsumen) -->
                            <g transform="translate(68, 68)">
                                <circle cx="22" cy="22" r="18" fill="#B77466" stroke="#ffffff" stroke-width="2.5"/>
                                <!-- Keranjang Belanja -->
                                <path d="M14 15h3l2.2 8h8.8l2-6H19" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="20.5" cy="25.5" r="1.5" fill="#ffffff"/>
                                <circle cx="26.5" cy="25.5" r="1.5" fill="#ffffff"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Judul Utama Halaman -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#957C62] tracking-tight">
                    Masuk ke Sistem
                </h1>
                <p class="text-xs sm:text-sm text-[#957C62]/80 mt-1 max-w-sm mx-auto leading-relaxed">
                    Rekomendasi Tata Letak Produk Berdasarkan Pola Pembelian Konsumen (Algoritma Apriori)
                </p>
            </div>

            <!-- Form Body -->
            <div class="px-6 sm:px-10 pb-8 sm:pb-10">
                <form action="<?= base_url('/login') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>

                    <!-- Input Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#957C62] mb-1.5">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#957C62]/60">
                                <!-- User Icon SVG -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   value="<?= old('username') ?>"
                                   class="w-full pl-11 pr-4 py-3 bg-[#FFE1AF]/10 border border-[#E2B59A] text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] focus:border-transparent transition placeholder-gray-400 text-sm"
                                   placeholder="Masukkan username Anda">
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#957C62] mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#957C62]/60">
                                <!-- Lock Icon SVG -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   class="w-full pl-11 pr-11 py-3 bg-[#FFE1AF]/10 border border-[#E2B59A] text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#B77466] focus:border-transparent transition placeholder-gray-400 text-sm"
                                   placeholder="Masukkan password Anda">
                            <!-- Toggle Show/Hide Password -->
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password', 'eye-icon-login')" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#957C62]/60 hover:text-[#B77466] focus:outline-none transition">
                                <svg id="eye-icon-login" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit"
                            class="w-full mt-2 bg-[#B77466] hover:bg-[#957C62] text-white py-3.5 px-4 rounded-xl transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2 group text-sm sm:text-base">
                        <span>Masuk ke Aplikasi</span>
                        <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>

                    <!-- Link ke Register -->
                    <div class="text-center pt-4 border-t border-[#E2B59A]/30 mt-4">
                        <p class="text-gray-600 text-sm">
                            Belum memiliki akun?
                            <a href="<?= site_url('register') ?>" class="text-[#B77466] hover:text-[#957C62] font-semibold underline underline-offset-2 transition">
                                Daftar di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer Ringkas -->
            <div class="bg-[#FFE1AF]/30 py-3 text-center border-t border-[#E2B59A]/30">
                <span class="text-xs text-[#957C62]/70 font-medium">
                    Sistem Tata Letak Toko • Algoritma Apriori
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

    <!-- SweetAlert Notifikasi -->
    <?php $successMessage = session()->getFlashdata('success'); ?>
    <?php if ($successMessage): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= esc($successMessage, 'js') ?>',
                timer: 2500,
                showConfirmButton: false,
                background: '#fff',
                color: '#957C62',
                iconColor: '#B77466'
            });
        </script>
    <?php endif; ?>

    <?php $errorMessage = session()->getFlashdata('error'); ?>
    <?php if ($errorMessage): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?= esc($errorMessage, 'js') ?>',
                background: '#fff',
                color: '#957C62',
                iconColor: '#B77466',
                confirmButtonColor: '#B77466'
            });
        </script>
    <?php endif; ?>

</body>
</html>
