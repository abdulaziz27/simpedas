# 📚 MANUAL BOOK SIMPEDAS

## Sistem Informasi Pendidikan Dinas

---

## 📋 DAFTAR ISI

1. [Pengenalan Simpedas](#1-pengenalan-simpedas)
2. [Akses dan Login](#2-akses-dan-login)
3. [Dashboard dan Overview](#3-dashboard-dan-overview)
4. [Fitur Publik (Tanpa Login)](#4-fitur-publik-tanpa-login)
5. [Manajemen Data Sekolah](#5-manajemen-data-sekolah)
6. [Manajemen Data Guru](#6-manajemen-data-guru)
7. [Manajemen Data Siswa](#7-manajemen-data-siswa)
8. [Manajemen Tenaga Kependidikan](#8-manajemen-tenaga-kependidikan)
9. [Import/Export Data](#9-importexport-data)
10. [Laporan dan Statistik](#10-laporan-dan-statistik)
11. [Manajemen User](#11-manajemen-user)
12. [Fitur Guru](#12-fitur-guru)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. PENGENALAN SIMPEDAS

### Apa itu Simpedas?

Simpedas (Sistem Informasi Pendidikan Dinas) adalah aplikasi web yang dirancang untuk mengelola data pendidikan secara terintegrasi. Aplikasi ini memungkinkan:

-   **Admin Dinas**: Mengelola seluruh data sekolah, guru, siswa, dan tenaga kependidikan di wilayahnya
-   **Admin Sekolah**: Mengelola data sekolah masing-masing
-   **Guru**: Mengakses profil dan data siswa yang menjadi tanggung jawabnya
-   **Publik**: Mencari dan melihat informasi sekolah, guru, siswa, dan tenaga kependidikan

### Fitur Utama:

-   ✅ Manajemen data sekolah lengkap
-   ✅ Manajemen data guru dan dokumen
-   ✅ Manajemen data siswa dan rapor
-   ✅ Manajemen tenaga kependidikan
-   ✅ Import/Export data bulk
-   ✅ Laporan dan statistik
-   ✅ Pencarian data publik
-   ✅ Manajemen user dan role

---

## 2. AKSES DAN LOGIN

### 2.1 Mengakses Aplikasi

1. **Buka browser** dan kunjungi URL aplikasi Simpedas
2. **Halaman utama** akan menampilkan dashboard publik dengan menu:
    - Dashboard
    - Data Siswa
    - Data Guru
    - Data Sekolah
    - Login

### 2.2 Login ke Sistem

1. **Klik tombol "Login"** di halaman utama
2. **Masukkan kredensial**:
    - Username atau Email
    - Password
3. **Klik "Sign in"**

> ⚠️ **Catatan**: Registrasi akun baru hanya dapat dilakukan oleh admin sistem

### 2.3 Jenis User dan Hak Akses

| Role              | Hak Akses                               |
| ----------------- | --------------------------------------- |
| **Admin Dinas**   | Mengelola seluruh data di wilayah dinas |
| **Admin Sekolah** | Mengelola data sekolah masing-masing    |
| **Guru**          | Mengakses profil dan data siswa         |

---

## 3. DASHBOARD DAN OVERVIEW

### 3.1 Dashboard Admin Dinas

Setelah login sebagai Admin Dinas, Anda akan melihat:

-   **Statistik Utama**:

    -   Total Sekolah
    -   Total Guru
    -   Total Siswa Aktif
    -   Total Siswa Tamat
    -   Total Tenaga Kependidikan

-   **Menu Navigasi**:
    -   Data Sekolah
    -   Data Guru
    -   Data Siswa
    -   Tenaga Kependidikan
    -   Laporan
    -   Manajemen User

### 3.2 Dashboard Admin Sekolah

Dashboard Admin Sekolah menampilkan:

-   **Statistik Sekolah**:

    -   Total Guru di sekolah
    -   Total Siswa
    -   Total Tenaga Kependidikan

-   **Menu Terbatas**:
    -   Data Guru (sekolah sendiri)
    -   Data Siswa (sekolah sendiri)
    -   Tenaga Kependidikan (sekolah sendiri)
    -   Laporan Sekolah

### 3.3 Dashboard Guru

Dashboard Guru menampilkan:

-   **Menu Guru**:
    -   Profil
    -   Data Siswa
    -   Laporan
    -   Dokumen

---

## 4. FITUR PUBLIK (TANPA LOGIN)

### 4.1 Pencarian Data Sekolah

1. **Klik "Data Sekolah"** di menu utama
2. **Gunakan fitur pencarian**:
    - Masukkan nama sekolah, NPSN, kepala sekolah, atau wilayah
    - Klik "Cari"
3. **Lihat hasil pencarian** dalam bentuk kartu
4. **Klik kartu sekolah** untuk melihat detail lengkap

### 4.2 Pencarian Data Guru

1. **Klik "Data Guru"** di menu utama
2. **Gunakan fitur pencarian**:
    - Masukkan nama guru, NUPTK, atau NIP
    - Klik "Cari"
3. **Lihat daftar guru** dengan informasi:
    - Foto profil
    - Nama lengkap
    - NUPTK/NIP
    - Mata pelajaran
    - Sekolah
4. **Klik nama guru** untuk melihat detail lengkap

### 4.3 Pencarian Data Siswa

1. **Klik "Data Siswa"** di menu utama
2. **Gunakan fitur pencarian**:
    - Masukkan nama siswa atau NISN
    - Klik "Cari"
3. **Lihat daftar siswa** dengan informasi:
    - Foto profil
    - Nama lengkap
    - NISN
    - Kelas
    - Sekolah
4. **Klik nama siswa** untuk melihat detail lengkap

### 4.4 Pencarian Tenaga Kependidikan

1. **Klik "Data Non-Teaching Staff"** di menu utama
2. **Gunakan fitur pencarian**:
    - Masukkan nama, NIP/NIK, atau posisi
    - Klik "Cari"
3. **Lihat daftar tenaga kependidikan**
4. **Klik nama** untuk melihat detail lengkap

### 4.5 Statistik Pendidikan

1. **Klik "Statistik"** di menu utama
2. **Pilih jenis statistik**:
    - Sekolah
    - Guru
    - Siswa
    - Tenaga Kependidikan
3. **Lihat grafik dan data statistik** berdasarkan jenjang pendidikan

---

## 5. MANAJEMEN DATA SEKOLAH

### 5.1 Melihat Daftar Sekolah

1. **Login sebagai Admin Dinas**
2. **Klik menu "Data Sekolah"**
3. **Gunakan filter** (opsional):
    - Jenjang Pendidikan
    - Wilayah
    - Status
    - Pencarian nama/NPSN
4. **Klik "Filter"** untuk menerapkan

### 5.2 Menambah Data Sekolah Baru

1. **Klik tombol "Tambah Sekolah"**
2. **Isi form dengan data**:
    - **Identitas Sekolah**:
        - Nama Sekolah (wajib)
        - NPSN (wajib, unik)
        - Alamat (wajib)
        - Telepon
        - Email
        - Website
    - **Informasi Pendidikan**:
        - Jenjang Pendidikan (TK/SD/SMP/Non Formal)
        - Status (Negeri/Swasta)
        - Wilayah
    - **Kepala Sekolah**:
        - Nama Kepala Sekolah
        - NIP (jika ada)
3. **Klik "Simpan"**

### 5.3 Mengedit Data Sekolah

1. **Klik ikon "Edit"** pada sekolah yang ingin diubah
2. **Ubah data** sesuai kebutuhan
3. **Klik "Update"**

### 5.4 Menghapus Data Sekolah

1. **Klik ikon "Hapus"** pada sekolah yang ingin dihapus
2. **Konfirmasi penghapusan** di popup
3. **Klik "Ya, Hapus"**

### 5.5 Mencetak Data Sekolah

1. **Klik ikon "Print"** pada sekolah
2. **Data sekolah akan dicetak** dalam format PDF

---

## 6. MANAJEMEN DATA GURU

### 6.1 Melihat Daftar Guru

1. **Klik menu "Data Guru"**
2. **Gunakan filter** (opsional):
    - Sekolah
    - Mata Pelajaran
    - Status Kepegawaian
    - Pencarian nama/NUPTK/NIP
3. **Klik "Filter"**

### 6.2 Menambah Data Guru Baru

1. **Klik tombol "Tambah Guru"**
2. **Isi form dengan data**:
    - **Identitas Pribadi**:
        - Nama Lengkap (wajib)
        - NUPTK
        - NIP
        - Tempat, Tanggal Lahir
        - Jenis Kelamin
        - Agama
        - Alamat
        - Telepon
        - Email
    - **Informasi Kepegawaian**:
        - Sekolah (wajib)
        - Mata Pelajaran
        - Status Kepegawaian
        - Tanggal Mulai Bekerja
    - **Upload Foto** (opsional)
3. **Klik "Simpan"**

### 6.3 Mengedit Data Guru

1. **Klik ikon "Edit"** pada guru yang ingin diubah
2. **Ubah data** sesuai kebutuhan
3. **Klik "Update"**

### 6.4 Menghapus Data Guru

1. **Klik ikon "Hapus"** pada guru yang ingin dihapus
2. **Konfirmasi penghapusan**
3. **Klik "Ya, Hapus"**

### 6.5 Mencetak Data Guru

1. **Klik ikon "Print"** pada guru
2. **Data guru akan dicetak** dalam format PDF

---

## 7. MANAJEMEN DATA SISWA

### 7.1 Melihat Daftar Siswa

1. **Klik menu "Data Siswa"**
2. **Gunakan filter**:
    - Sekolah
    - Kelas
    - Status Siswa
    - Pencarian nama/NISN
3. **Klik "Filter"**

### 7.2 Menambah Data Siswa Baru

1. **Klik tombol "Tambah Siswa"**
2. **Isi form dengan data**:
    - **Identitas Pribadi**:
        - Nama Lengkap (wajib)
        - NISN (wajib, unik)
        - NIS (Nomor Induk Sekolah)
        - Tempat, Tanggal Lahir
        - Jenis Kelamin
        - Agama
        - Alamat
        - Nama Orang Tua
        - Telepon
    - **Informasi Sekolah**:
        - Sekolah (wajib)
        - Kelas
        - Status Siswa (Aktif/Tamat/Pindah)
    - **Upload Foto** (opsional)
3. **Klik "Simpan"**

### 7.3 Mengedit Data Siswa

1. **Klik ikon "Edit"** pada siswa yang ingin diubah
2. **Ubah data** sesuai kebutuhan
3. **Klik "Update"**

### 7.4 Mengelola Sertifikat Siswa

1. **Klik ikon "Detail"** pada siswa
2. **Klik tab "Sertifikat"**
3. **Upload sertifikat**:
    - Klik "Upload Sertifikat"
    - Pilih file
    - Masukkan nama sertifikat
    - Klik "Upload"
4. **Download atau hapus** sertifikat yang sudah ada

### 7.5 Mengelola Rapor Siswa

1. **Klik ikon "Detail"** pada siswa
2. **Klik tab "Rapor"**
3. **Tambah rapor**:
    - Klik "Tambah Rapor"
    - Isi data rapor
    - Upload file rapor
    - Klik "Simpan"
4. **Edit atau hapus** rapor yang sudah ada

### 7.6 Mencetak Data Siswa

1. **Klik ikon "Print"** pada siswa
2. **Data siswa akan dicetak** dalam format PDF

---

## 8. MANAJEMEN TENAGA KEPENDIDIKAN

### 8.1 Melihat Daftar Tenaga Kependidikan

1. **Klik menu "Tenaga Kependidikan"**
2. **Gunakan filter**:
    - Sekolah
    - Posisi
    - Status Kepegawaian
    - Pencarian nama/NIP-NIK
3. **Klik "Filter"**

### 8.2 Menambah Data Tenaga Kependidikan Baru

1. **Klik tombol "Tambah Staff"**
2. **Isi form dengan data**:
    - **Identitas Pribadi**:
        - Nama Lengkap (wajib)
        - NIP/NIK
        - NUPTK (jika ada)
        - Tempat, Tanggal Lahir
        - Jenis Kelamin
        - Agama
        - Alamat
        - Telepon
        - Email
    - **Informasi Pekerjaan**:
        - Sekolah (wajib)
        - Posisi/Jabatan
        - Status Kepegawaian
        - Tanggal Mulai Bekerja
    - **Upload Foto** (opsional)
3. **Klik "Simpan"**

### 8.3 Mengedit Data Tenaga Kependidikan

1. **Klik ikon "Edit"** pada staff yang ingin diubah
2. **Ubah data** sesuai kebutuhan
3. **Klik "Update"**

### 8.4 Menghapus Data Tenaga Kependidikan

1. **Klik ikon "Hapus"** pada staff yang ingin dihapus
2. **Konfirmasi penghapusan**
3. **Klik "Ya, Hapus"**

### 8.5 Mencetak Data Tenaga Kependidikan

1. **Klik ikon "Print"** pada staff
2. **Data akan dicetak** dalam format PDF

---

## 9. IMPORT/EXPORT DATA

### 9.1 Download Template Excel

#### Template Sekolah:

1. **Klik menu "Data Sekolah"**
2. **Klik "Download Template"**
3. **File Excel akan terdownload**
4. **Buka file** dan lihat format kolom yang diperlukan

#### Template Guru:

1. **Klik menu "Data Guru"**
2. **Klik "Download Template"**
3. **File Excel akan terdownload**

#### Template Siswa:

1. **Klik menu "Data Siswa"**
2. **Klik "Download Template"**
3. **File Excel akan terdownload**

#### Template Tenaga Kependidikan:

1. **Klik menu "Tenaga Kependidikan"**
2. **Klik "Download Template"**
3. **File Excel akan terdownload**

### 9.2 Mengisi Template Excel

#### Format Data yang Diperlukan:

**Template Sekolah**:

-   NPSN (wajib, unik)
-   NAMA_SEKOLAH (wajib)
-   ALAMAT (wajib)
-   TELEPON
-   EMAIL
-   WEBSITE
-   JENJANG_PENDIDIKAN (TK/SD/SMP/Non Formal)
-   STATUS (Negeri/Swasta)
-   WILAYAH
-   NAMA_KEPALA_SEKOLAH
-   NIP_KEPALA_SEKOLAH
-   AKSI (CREATE/UPDATE/DELETE)

**Template Guru**:

-   NPSN_SEKOLAH (wajib untuk admin dinas)
-   NAMA_LENGKAP (wajib)
-   NUPTK
-   NIP
-   TEMPAT_LAHIR
-   TANGGAL_LAHIR
-   JENIS_KELAMIN
-   AGAMA
-   ALAMAT
-   TELEPON
-   EMAIL
-   MATA_PELAJARAN
-   STATUS_KEPEGAWAIAN
-   TANGGAL_MULAI_BEKERJA
-   AKSI (CREATE/UPDATE/DELETE)

**Template Siswa**:

-   NPSN_SEKOLAH (wajib untuk admin dinas)
-   NAMA_LENGKAP (wajib)
-   NISN (wajib, unik)
-   NIS
-   TEMPAT_LAHIR
-   TANGGAL_LAHIR
-   JENIS_KELAMIN
-   AGAMA
-   ALAMAT
-   NAMA_ORANG_TUA
-   TELEPON
-   KELAS
-   STATUS_SISWA (Aktif/Tamat/Pindah)
-   AKSI (CREATE/UPDATE/DELETE)

**Template Tenaga Kependidikan**:

-   NPSN_SEKOLAH (wajib untuk admin dinas)
-   NAMA_LENGKAP (wajib)
-   NIP_NIK
-   NUPTK
-   TEMPAT_LAHIR
-   TANGGAL_LAHIR
-   JENIS_KELAMIN
-   AGAMA
-   ALAMAT
-   TELEPON
-   EMAIL
-   POSISI
-   STATUS_KEPEGAWAIAN
-   TANGGAL_MULAI_BEKERJA
-   AKSI (CREATE/UPDATE/DELETE)

### 9.3 Upload Data Excel

1. **Klik "Import Data"** di halaman yang sesuai
2. **Pilih file Excel** yang sudah diisi
3. **Klik "Upload"**
4. **Tunggu proses import** selesai
5. **Lihat hasil import**:
    - Jumlah data berhasil diimport
    - Jumlah data gagal
    - Detail error (jika ada)

### 9.4 Export Data

#### Export Laporan:

1. **Klik menu "Laporan"**
2. **Pilih jenis laporan** yang ingin diexport
3. **Klik "Export Excel"**
4. **File Excel akan terdownload**

---

## 10. LAPORAN DAN STATISTIK

### 10.1 Laporan Admin Dinas

1. **Klik menu "Laporan"**
2. **Pilih jenis laporan**:

    - **Laporan Sekolah**: Data semua sekolah
    - **Laporan Guru**: Data semua guru
    - **Laporan Siswa**: Data semua siswa
    - **Laporan Kelulusan**: Data siswa yang lulus
    - **Laporan Tenaga Kependidikan**: Data semua staff

3. **Gunakan filter** (opsional):

    - Jenjang Pendidikan
    - Wilayah
    - Periode waktu
    - Status

4. **Klik "Generate Laporan"**
5. **Export ke Excel** atau **Print**

### 10.2 Laporan Admin Sekolah

1. **Klik menu "Laporan"**
2. **Pilih jenis laporan**:

    - **Laporan Siswa Sekolah**: Data siswa di sekolah
    - **Laporan Guru Sekolah**: Data guru di sekolah
    - **Laporan Rapor**: Data rapor siswa

3. **Gunakan filter** sesuai kebutuhan
4. **Generate dan export** laporan

### 10.3 Statistik Pendidikan

1. **Klik menu "Statistik"** (publik)
2. **Pilih jenis statistik**:

    - Sekolah
    - Guru
    - Siswa
    - Tenaga Kependidikan

3. **Lihat grafik** berdasarkan:

    - Jenjang Pendidikan (TK, SD, SMP, Non Formal)
    - Distribusi data
    - Persentase

4. **Filter berdasarkan jenjang** untuk detail lebih spesifik

---

## 11. MANAJEMEN USER

### 11.1 Melihat Daftar User

1. **Login sebagai Admin Dinas**
2. **Klik menu "Manajemen User"**
3. **Lihat daftar user** dengan informasi:
    - Nama
    - Email
    - Role
    - Sekolah (jika applicable)
    - Status

### 11.2 Menambah User Baru

1. **Klik "Tambah User"**
2. **Isi form**:
    - Nama (wajib)
    - Email (wajib, unik)
    - Password (wajib)
    - Role (Admin Dinas/Admin Sekolah/Guru)
    - Sekolah (jika role Admin Sekolah atau Guru)
3. **Klik "Simpan"**

### 11.3 Mengedit User

1. **Klik ikon "Edit"** pada user
2. **Ubah data** sesuai kebutuhan
3. **Klik "Update"**

### 11.4 Menghapus User

1. **Klik ikon "Hapus"** pada user
2. **Konfirmasi penghapusan**
3. **Klik "Ya, Hapus"**

---

## 12. FITUR GURU

### 12.1 Akses Dashboard Guru

1. **Login dengan akun guru**
2. **Dashboard guru** menampilkan menu:
    - Profil
    - Data Siswa
    - Laporan
    - Dokumen

### 12.2 Mengelola Profil Guru

1. **Klik menu "Profil"**
2. **Lihat dan edit**:
    - Data pribadi
    - Informasi kepegawaian
    - Foto profil
3. **Upload dokumen**:
    - Klik "Tambah Dokumen"
    - Pilih file
    - Masukkan nama dokumen
    - Klik "Upload"
4. **Download atau hapus** dokumen yang sudah ada

### 12.3 Melihat Data Siswa

1. **Klik menu "Data Siswa"**
2. **Lihat daftar siswa** yang menjadi tanggung jawab
3. **Klik nama siswa** untuk detail lengkap
4. **Lihat rapor dan sertifikat** siswa

### 12.4 Mengelola Laporan

1. **Klik menu "Laporan"**
2. **Lihat laporan** terkait siswa
3. **Download laporan** dalam format Excel atau PDF

### 12.5 Mencetak Profil

1. **Klik menu "Profil"**
2. **Klik "Print Profil"**
3. **Data profil akan dicetak** dalam format PDF

---

## 13. TROUBLESHOOTING

### 13.1 Masalah Login

**Problem**: Tidak bisa login
**Solusi**:

-   Pastikan username/email dan password benar
-   Cek koneksi internet
-   Hubungi admin sistem untuk reset password

**Problem**: Lupa password
**Solusi**:

-   Hubungi admin sistem untuk reset password
-   Admin akan memberikan password baru

### 13.2 Masalah Upload File

**Problem**: File tidak bisa diupload
**Solusi**:

-   Pastikan format file sesuai (Excel untuk import, PDF/Image untuk dokumen)
-   Cek ukuran file (maksimal 10MB)
-   Pastikan koneksi internet stabil

**Problem**: Import Excel gagal
**Solusi**:

-   Pastikan format kolom sesuai template
-   Cek data yang diisi (tidak boleh ada kolom wajib yang kosong)
-   Pastikan NPSN/NISN/NUPTK unik dan valid

### 13.3 Masalah Akses Menu

**Problem**: Menu tidak muncul
**Solusi**:

-   Pastikan login dengan role yang tepat
-   Refresh halaman
-   Logout dan login kembali

**Problem**: Tidak bisa mengakses data sekolah tertentu
**Solusi**:

-   Admin Sekolah hanya bisa mengakses data sekolah sendiri
-   Admin Dinas bisa mengakses semua data
-   Pastikan role user sudah benar

### 13.4 Masalah Cetak/Export

**Problem**: Tidak bisa cetak PDF
**Solusi**:

-   Pastikan browser mendukung PDF
-   Cek popup blocker
-   Gunakan browser terbaru

**Problem**: Export Excel tidak berfungsi
**Solusi**:

-   Pastikan koneksi internet stabil
-   Cek ukuran data yang diexport
-   Refresh halaman dan coba lagi

### 13.5 Masalah Data

**Problem**: Data tidak muncul
**Solusi**:

-   Cek filter yang digunakan
-   Pastikan data sudah tersimpan
-   Refresh halaman

**Problem**: Data duplikat
**Solusi**:

-   Cek kolom unik (NPSN, NISN, NUPTK)
-   Hapus data duplikat
-   Import ulang dengan data yang benar

---

## 📞 KONTAK DUKUNGAN

Jika mengalami masalah yang tidak dapat diselesaikan dengan panduan ini, silakan hubungi:

-   **Admin Sistem**: [email admin]
-   **Tim IT**: [email tim IT]
-   **Telepon**: [nomor telepon]

---

## 📝 CATATAN PENTING

1. **Backup Data**: Selalu backup data penting sebelum melakukan operasi besar
2. **Validasi Data**: Pastikan data yang diinput sudah benar sebelum menyimpan
3. **Keamanan**: Jangan share kredensial login dengan orang lain
4. **Update**: Pastikan menggunakan browser terbaru untuk performa optimal
5. **Format File**: Gunakan format file yang didukung sistem

---

_Manual Book Simpedas v1.0 - Terakhir diupdate: [Tanggal]_
