<?php helper(['url']); ?>
<?= $this->extend('layout/layout'); ?>
<?= $this->section('content'); ?>

<div class="w-full min-h-screen bg-[#F0F7FF] p-4 lg:p-8 text-slate-800">
    <div class="container mx-auto max-w-6xl">

        <!-- Judul Halaman -->
        <h1 class="text-3xl sm:text-4xl font-black mb-6 text-slate-900 border-b border-sky-200 pb-4">
            Pengaturan Akun
        </h1>

        <!-- Notifikasi Error Validasi -->
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-5 rounded-2xl mb-8 shadow-sm" role="alert">
                <p class="font-bold text-base">Gagal Memperbarui</p>
                <ul class="mt-2 list-disc list-inside text-xs sm:text-sm">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Layout Tab Modern -->
        <div x-data="{ tab: 'profil' }" class="bg-white rounded-3xl shadow-md shadow-sky-900/5 border border-sky-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-4">
                
                <!-- Kolom Kiri: Navigasi Tab -->
                <div class="md:col-span-1 border-b md:border-b-0 md:border-r border-sky-100 bg-sky-50/30">
                    <!-- Info User di Atas -->
                    <div class="p-6 text-center border-b border-sky-100">
                        <img class="h-24 w-24 rounded-full object-cover mx-auto border-4 border-[#38BDF8] shadow-md" 
                             src="<?= base_url('uploads/foto_profil/' . esc($user['foto'])) ?>" 
                             alt="Foto Profil"
                             onerror="this.src='https://placehold.co/96x96/0284C7/FFFFFF?text=<?= substr(esc($user['username']), 0, 1) ?>'; this.onerror=null;">
                        <h2 class="text-xl font-bold text-slate-900 mt-4"><?= esc($user['username']) ?></h2>
                        <p class="text-sm text-slate-500"><?= esc($user['email']) ?></p>
                    </div>
                    
                    <!-- Tombol Tab -->
                    <nav class="flex flex-col p-4 space-y-2">
                        <button @click="tab = 'profil'" 
                                :class="{ 'bg-[#0284C7] text-white shadow-sm': tab === 'profil', 'text-slate-600 hover:bg-sky-100/70 hover:text-[#0284C7]': tab !== 'profil' }"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-colors duration-200 text-left text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profil Saya
                        </button>
                        <button @click="tab = 'keamanan'"
                                :class="{ 'bg-[#0284C7] text-white shadow-sm': tab === 'keamanan', 'text-slate-600 hover:bg-sky-100/70 hover:text-[#0284C7]': tab !== 'keamanan' }"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-colors duration-200 text-left text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Keamanan
                        </button>
                    </nav>
                </div>

                <!-- Kolom Kanan: Konten Tab -->
                <div class="md:col-span-3 p-6 lg:p-10">
                    
                    <!-- Konten Tab 1: Informasi Profil -->
                    <div x-show="tab === 'profil'">
                        <h2 class="text-2xl font-black text-slate-900 mb-6">Informasi Profil</h2>
                        <form action="<?= site_url('akun/update_profil') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <!-- Foto Profil -->
                            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 mb-8">
                                <img class="h-20 w-20 rounded-full object-cover border-4 border-sky-200" 
                                     src="<?= base_url('uploads/foto_profil/' . esc($user['foto'])) ?>" 
                                     alt="Foto Profil"
                                     onerror="this.src='https://placehold.co/80x80/0284C7/FFFFFF?text=<?= substr(esc($user['username']), 0, 1) ?>'; this.onerror=null;">
                                <div class="flex-1">
                                    <label for="foto" class="block text-sm font-bold text-slate-700">Ganti Foto</label>
                                    <input type="file" name="foto" id="foto" class="mt-1 block w-full text-sm text-slate-600
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-xl file:border-0 file:text-sm file:font-semibold
                                        file:bg-[#0284C7] file:text-white
                                        hover:file:bg-[#0369A1] transition cursor-pointer">
                                    <p class="text-xs text-slate-400 mt-1">PNG, JPG, JPEG (MAX. 1MB)</p>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="space-y-4">
                                <div>
                                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Username</label>
                                    <input type="text" id="username" value="<?= esc($user['username']) ?>" class="w-full px-4 py-3 mt-1 border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed text-sm" disabled>
                                </div>
                                <div>
                                    <label for="nama_lengkap" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= esc($user['nama_lengkap']) ?>" class="w-full px-4 py-3 mt-1 bg-sky-50/40 border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition text-sm text-slate-900">
                                </div>
                                <div>
                                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Email</label>
                                    <input type="email" name="email" id="email" value="<?= esc($user['email']) ?>" class="w-full px-4 py-3 mt-1 bg-sky-50/40 border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition text-sm text-slate-900">
                                </div>
                            </div>

                            <button type="submit" class="w-full lg:w-auto mt-6 py-3 px-8 bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white font-bold rounded-xl transition duration-300 shadow-md shadow-sky-500/20 hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                                Simpan Perubahan Profil
                            </button>
                        </form>
                    </div>

                    <!-- Konten Tab 2: Keamanan / Ganti Password -->
                    <div x-show="tab === 'keamanan'" x-cloak>
                        <h2 class="text-2xl font-black text-slate-900 mb-6">Ganti Password</h2>
                        <form action="<?= site_url('akun/update_sandi') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="space-y-4 max-w-lg">
                                <div>
                                    <label for="password_lama" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Password Lama</label>
                                    <input type="password" name="password_lama" id="password_lama" class="w-full px-4 py-3 mt-1 bg-sky-50/40 border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition text-sm text-slate-900" required>
                                </div>
                                <div>
                                    <label for="password_baru" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Password Baru</label>
                                    <input type="password" name="password_baru" id="password_baru" class="w-full px-4 py-3 mt-1 bg-sky-50/40 border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition text-sm text-slate-900" required>
                                </div>
                                <div>
                                    <label for="konfirmasi_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Konfirmasi Password Baru</label>
                                    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="w-full px-4 py-3 mt-1 bg-sky-50/40 border border-sky-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition text-sm text-slate-900" required>
                                </div>
                            </div>
                            <button type="submit" class="w-full lg:w-auto mt-6 py-3 px-8 bg-gradient-to-r from-[#0EA5E9] to-[#0284C7] hover:from-[#0284C7] hover:to-[#0369A1] text-white font-bold rounded-xl transition duration-300 shadow-md shadow-sky-500/20 hover:shadow-lg transform hover:-translate-y-0.5 text-sm">
                                Ubah Password
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
