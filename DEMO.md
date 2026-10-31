# Dokumentasi Akun Demo & Panduan Pengujian — Sabila Fun Learning

Dokumen ini berisi daftar akun demo untuk setiap peran (**Orang Tua**, **Guru**, dan **Admin**) beserta panduan skenario pengujian fitur pada aplikasi **Sabila Fun Learning**.

> **Catatan:** Seluruh navigasi cepat akun demo pada antarmuka aplikasi telah dihapus agar tampilan produksi bersih. Gunakan kredensial pada tabel di bawah ini untuk masuk melalui halaman `/login`.

---

## 1. Daftar Kredensial Akun Demo

Seluruh akun demo menggunakan kata sandi yang sama: **`password123`**

| Peran (Role) | Nama Pengguna | Email Login | Nomor HP Login | Kata Sandi | Keterangan Data Bawaan |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Orang Tua** (`parent`) | **Ibu Siti** | `siti@example.com` | `0812 3456 7890` | `password123` | Memiliki 2 anak aktif: **Aisyah** (*Kelompok Calistung A*) dan **Syifa** (*Kelompok Pracalis B*). |
| **Orang Tua 2** (`parent`) | **Ibu Rina** | `rina@example.com` | `0813 9876 5432` | `password123` | Memiliki 1 anak aktif (**Rafa** - *Kelompok Calistung B*) dan 1 anak menunggu persetujuan (**Zaki** - *Kelompok Calistung A*). |
| **Guru** (`teacher`) | **Bu Guru Nisa** | `guru@sabilafun.id` | `0811 2233 4455` | `password123` | Mengajar 2 kelas: **Kelompok Calistung A** dan **Kelompok Pracalis B**. |
| **Guru 2** (`teacher`) | **Bu Guru Tari** | `tari@sabilafun.id` | `0811 5566 7788` | `password123` | Mengajar 2 kelas: **Kelompok Calistung B** dan **Kelompok Pracalis A**. |
| **Admin** (`admin`) | **Admin Sabila** | `admin@sabilafun.id` | `0811 0000 1111` | `password123` | Akses penuh ke seluruh kelas, kuota, persetujuan pendaftaran, mapping guru, tagihan SPP, laporan, dan pengiriman notifikasi. |

---

## 2. Skenario Pengujian Berdasarkan Peran

### A. Peran Orang Tua (`siti@example.com` / `password123`)
1. **Beranda & Pilih Anak**:
   - Masuk ke `/login` menggunakan `siti@example.com` dan kata sandi `password123`.
   - Klik tombol pemilih anak di bawah sapaan **"Halo, Bunda"** atau tombol **"Ganti Anak"** pada kartu anak untuk berpindah antara **Aisyah** (*Kelompok Calistung A*) dan **Syifa** (*Kelompok Pracalis B*).
   - Klik di area mana saja di luar daftar untuk menutup *dropdown* pemilih anak.
2. **Daftarkan Anak Baru (Menunggu Persetujuan Admin)**:
   - Klik tombol **`+ Daftarkan Anak`** di Beranda.
   - Isi nama anak, tanggal lahir, dan pilih kelompok kelas.
   - Status anak baru akan otomatis tercatat sebagai **`Menunggu Persetujuan Admin`** hingga disetujui oleh Admin.
3. **Pengajuan Izin / Sakit**:
   - Klik tombol **`+ Ajukan Izin / Sakit`** di Beranda atau di halaman **Laporan Harian** (`/laporan-harian`).
   - Pilih tanggal, jenis keterangan (**Izin** atau **Sakit**), dan isi alasan.
   - Data otomatis masuk ke rekap **Statistik Kehadiran** pada **Laporan Harian** maupun **Laporan Bulanan**, serta mengirim notifikasi ke Guru dan Admin.
4. **Laporan Harian & Bulanan**:
   - Buka **Laporan Harian** (`/laporan-harian`) untuk melihat rekap **Hadir**, **Izin**, **Sakit**, kalender mingguan, materi, aktivitas, perilaku, catatan guru, serta galeri foto kegiatan (`/laporan-harian/{id}`).
   - Buka **Laporan Bulanan** (`/laporan-bulanan`) untuk melihat **Total Hadir**, **Total Izin**, dan **Total Sakit** yang dirujuk otomatis dari Laporan Harian bulan tersebut beserta capaian perkembangan anak.
5. **Pembayaran & Bukti Transfer**:
   - Buka **Pembayaran** (`/pembayaran`) dari Beranda, pilih tagihan bulan berjalan untuk melihat rincian biaya, status lunas, dan bukti pembayaran berproteksi *Temporary Signed URL*.

---

### B. Peran Guru (`guru@sabilafun.id` / `password123`)
1. **Melihat Kelas & Daftar Siswa**:
   - Buka menu **Kelas** (`/kelas`) dari *floating bottom navigation*.
   - Berpindah antar kelas yang diajar (**Kelompok Calistung A** dan **Kelompok Pracalis B**).
   - *Catatan:* Guru tidak dapat menambah kelas baru atau mengubah mapping kelas mengajar (fitur tersebut dikhususkan bagi Admin).
2. **Input Laporan Harian, Status Kehadiran, & Evaluasi Bulanan**:
   - Pilih salah satu siswa pada daftar kelas, lalu klik **Laporan Harian**, **Laporan Bulanan**, atau **Foto Kegiatan**.
   - Klik **`Edit` / `+ Input`** untuk mengisi materi, aktivitas, perilaku, status kehadiran (**Hadir**, **Izin**, **Sakit**), atau mengunggah foto kegiatan kelas.

---

### C. Peran Admin (`admin@sabilafun.id` / `password123`)
1. **Persetujuan Pendaftaran Siswa & Kuota Kelas**:
   - Buka menu **Kelas** (`/kelas`).
   - Pada bagian **Pendaftaran Siswa Baru (Menunggu Persetujuan)**, pilih kelas penempatan dan klik **`✓ Setujui Masuk Kelas`** atau **`Tolak`**. Sistem memvalidasi sisa kuota kelas secara otomatis.
   - Klik **`Atur Kuota`** pada kartu kelas untuk mengubah kuota maksimal siswa dan jadwal belajar.
2. **Tambah Kelas & Mapping Guru ke Kelas**:
   - Klik **`+ Kelas`** atau **`+ Tambah Kelas Baru`** untuk membuat kelompok belajar baru.
   - Klik **`Mapping Guru`** pada kartu kelas atau **`+ Tambah Kelas Mengajar`** pada bagian *Ringkasan Mapping Guru Pengajar* untuk menugaskan guru ke satu atau beberapa kelas.
3. **Kirim Notifikasi (Judul & Deskripsi)**:
   - Buka menu **Notifikasi** (`/notifikasi`).
   - Klik tombol **`+ Kirim Notifikasi`**, pilih target penerima (*Semua Pengguna*, *Semua Orang Tua*, *Semua Guru*, atau *Pengguna Tertentu*), pilih kategori, isi **Judul Notifikasi** serta **Deskripsi Notifikasi**, lalu kirim.
   - Klik item notifikasi mana pun untuk membuka modal **Detail Notifikasi**.

---

## 3. Perintah Reset Data Demo (Lokal / Server)

Jika Anda ingin mengembalikan seluruh data demo ke kondisi awal:

```bash
php artisan migrate:fresh --seed
```

