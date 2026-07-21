<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::public')] class extends Component {
    //
};
?>

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-mono lg:text-3xl">Kebijakan Privasi</h1>
        <p class="text-sm text-secondary-foreground">
            Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}
        </p>
    </div>

    <div class="prose-privacy flex flex-col gap-6 text-sm leading-relaxed text-secondary-foreground">
        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">1. Pendahuluan</h2>
            <p>
                Kebijakan Privasi ini menjelaskan bagaimana <strong class="text-foreground">Portal FMIPA</strong>
                (“kami”, “portal”) mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi Anda
                saat menggunakan layanan web dan aplikasi mobile portal pembelajaran Fakultas Matematika dan
                Ilmu Pengetahuan Alam.
            </p>
            <p>
                Dengan mendaftar atau menggunakan Portal FMIPA, Anda menyetujui praktik yang diuraikan dalam
                kebijakan ini.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">2. Data yang Kami Kumpulkan</h2>
            <p>Kami dapat mengumpulkan data berikut:</p>
            <ul class="list-disc space-y-1 ps-5">
                <li>
                    <strong class="text-foreground">Data akun:</strong> nama, username, alamat email, peran
                    (dosen/mahasiswa/admin), dan kata sandi (disimpan dalam bentuk terenkripsi/hash).
                </li>
                <li>
                    <strong class="text-foreground">Data profil:</strong> foto profil (opsional).
                </li>
                <li>
                    <strong class="text-foreground">Data pembelajaran:</strong> kelas, materi, tugas,
                    pengumpulan tugas, nilai, dan umpan balik terkait kegiatan e-learning.
                </li>
                <li>
                    <strong class="text-foreground">Data perangkat:</strong> token notifikasi push (FCM)
                    untuk mengirim pemberitahuan ke aplikasi mobile Anda.
                </li>
                <li>
                    <strong class="text-foreground">Data teknis:</strong> log akses sesi dan token autentikasi
                    yang diperlukan agar layanan berfungsi dengan aman.
                </li>
            </ul>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">3. Cara Kami Menggunakan Data</h2>
            <p>Data digunakan untuk:</p>
            <ul class="list-disc space-y-1 ps-5">
                <li>menyediakan dan mengelola akun serta proses persetujuan pendaftaran;</li>
                <li>menjalankan fitur e-learning (kelas, materi, tugas, penilaian, dan pengumuman);</li>
                <li>mengirim notifikasi in-app dan push terkait aktivitas pembelajaran;</li>
                <li>menjaga keamanan, mencegah penyalahgunaan, dan meningkatkan layanan;</li>
                <li>memenuhi kewajiban administratif atau akademik yang relevan.</li>
            </ul>
            <p>Kami tidak menjual data pribadi Anda kepada pihak ketiga.</p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">4. Penyimpanan & Keamanan</h2>
            <p>
                Data disimpan pada sistem yang kami kelola. File pembelajaran dapat disimpan pada penyimpanan
                cloud yang dikonfigurasi portal. Kami menerapkan langkah teknis yang wajar (termasuk hash kata
                sandi dan autentikasi berbasis token) untuk melindungi data Anda.
            </p>
            <p>
                Meskipun demikian, tidak ada metode transmisi atau penyimpanan elektronik yang sepenuhnya
                aman. Kami menganjurkan Anda menjaga kerahasiaan kredensial akun.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">5. Notifikasi Push</h2>
            <p>
                Jika Anda menggunakan aplikasi mobile dan mengizinkan notifikasi, portal dapat mengirim
                pemberitahuan terkait tugas baru, tenggat waktu, pengumpulan, atau penilaian. Anda dapat
                menonaktifkan notifikasi melalui pengaturan perangkat.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">6. Berbagi Data</h2>
            <p>
                Data dapat diakses oleh pihak yang berwenang di lingkungan fakultas sesuai peran
                (misalnya dosen melihat pengumpulan mahasiswa di kelasnya, admin mengelola persetujuan akun).
                Data juga dapat diproses oleh penyedia infrastruktur (hosting, penyimpanan file, layanan push)
                sejauh diperlukan untuk menjalankan portal.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">7. Hak Anda</h2>
            <p>Anda berhak untuk:</p>
            <ul class="list-disc space-y-1 ps-5">
                <li>mengakses dan memperbarui data profil melalui halaman Profil;</li>
                <li>menghapus foto profil atau mengganti kata sandi;</li>
                <li>
                    menghapus akun Anda secara permanen melalui fitur Hapus Akun (memerlukan konfirmasi
                    password). Penghapusan akun akan menghapus data terkait sesuai kebijakan sistem.
                </li>
            </ul>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">8. Retensi Data</h2>
            <p>
                Data akun dan pembelajaran disimpan selama akun aktif atau selama diperlukan untuk keperluan
                akademik/administratif. Setelah akun dihapus, data terkait akan dihapus atau dinonaktifkan
                sesuai mekanisme portal, kecuali ada kewajiban menyimpan catatan tertentu.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">9. Perubahan Kebijakan</h2>
            <p>
                Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan berlaku sejak
                tanggal pembaruan yang tercantum di halaman ini. Penggunaan layanan secara berkelanjutan
                setelah pembaruan dianggap sebagai penerimaan terhadap kebijakan yang diperbarui.
            </p>
        </section>

        <section class="flex flex-col gap-2">
            <h2 class="text-base font-semibold text-mono">10. Kontak</h2>
            <p>
                Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini atau pengelolaan data pribadi,
                silakan menghubungi administrator Portal FMIPA melalui saluran resmi fakultas.
            </p>
        </section>
    </div>

    <div class="flex flex-wrap items-center gap-2.5 border-t border-border pt-6">
        <a href="{{ route('login') }}" class="kt-btn kt-btn-primary" wire:navigate>
            Kembali ke Login
        </a>
        <a href="{{ route('register') }}" class="kt-btn kt-btn-outline" wire:navigate>
            Daftar
        </a>
    </div>
</div>
