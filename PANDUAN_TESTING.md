# 🧪 PANDUAN TESTING SIMPEDAS

## 📋 CHECKLIST TESTING LENGKAP

### ✅ Testing Akses dan Login

#### Test 1: Login Admin Dinas

-   [ ] Buka halaman login
-   [ ] Masukkan email admin dinas
-   [ ] Masukkan password yang benar
-   [ ] Klik "Sign in"
-   [ ] Verifikasi redirect ke dashboard admin dinas
-   [ ] Verifikasi semua menu admin dinas muncul

#### Test 2: Login Admin Sekolah

-   [ ] Masukkan email admin sekolah
-   [ ] Masukkan password yang benar
-   [ ] Klik "Sign in"
-   [ ] Verifikasi redirect ke dashboard admin sekolah
-   [ ] Verifikasi menu terbatas sesuai role

#### Test 3: Login Guru

-   [ ] Masukkan email guru
-   [ ] Masukkan password yang benar
-   [ ] Klik "Sign in"
-   [ ] Verifikasi redirect ke dashboard guru
-   [ ] Verifikasi menu guru muncul

#### Test 4: Login Gagal

-   [ ] Masukkan email yang salah
-   [ ] Masukkan password yang benar
-   [ ] Klik "Sign in"
-   [ ] Verifikasi error message muncul
-   [ ] Masukkan email yang benar
-   [ ] Masukkan password yang salah
-   [ ] Verifikasi error message muncul

---

### ✅ Testing Fitur Publik

#### Test 5: Pencarian Sekolah

-   [ ] Buka halaman utama tanpa login
-   [ ] Klik "Data Sekolah"
-   [ ] Masukkan nama sekolah di search box
-   [ ] Klik "Cari"
-   [ ] Verifikasi hasil pencarian muncul
-   [ ] Klik salah satu kartu sekolah
-   [ ] Verifikasi detail sekolah muncul lengkap

#### Test 6: Pencarian Guru

-   [ ] Klik "Data Guru"
-   [ ] Masukkan nama guru di search box
-   [ ] Klik "Cari"
-   [ ] Verifikasi hasil pencarian muncul
-   [ ] Klik nama guru
-   [ ] Verifikasi detail guru muncul lengkap

#### Test 7: Pencarian Siswa

-   [ ] Klik "Data Siswa"
-   [ ] Masukkan nama siswa di search box
-   [ ] Klik "Cari"
-   [ ] Verifikasi hasil pencarian muncul
-   [ ] Klik nama siswa
-   [ ] Verifikasi detail siswa muncul lengkap

#### Test 8: Statistik Publik

-   [ ] Klik "Statistik"
-   [ ] Pilih jenis statistik "Sekolah"
-   [ ] Verifikasi grafik muncul
-   [ ] Pilih jenis statistik "Guru"
-   [ ] Verifikasi grafik berubah
-   [ ] Pilih filter jenjang pendidikan
-   [ ] Verifikasi grafik terfilter

---

### ✅ Testing Manajemen Data Sekolah (Admin Dinas)

#### Test 9: Tambah Sekolah

-   [ ] Login sebagai admin dinas
-   [ ] Klik menu "Data Sekolah"
-   [ ] Klik "Tambah Sekolah"
-   [ ] Isi form dengan data valid:
    -   Nama Sekolah: "SD Test"
    -   NPSN: "12345678"
    -   Alamat: "Jl. Test No. 1"
    -   Jenjang: "SD"
    -   Status: "Negeri"
    -   Wilayah: "Jakarta Pusat"
-   [ ] Klik "Simpan"
-   [ ] Verifikasi sekolah tersimpan
-   [ ] Verifikasi muncul di daftar sekolah

#### Test 10: Edit Sekolah

-   [ ] Klik ikon "Edit" pada sekolah yang baru dibuat
-   [ ] Ubah nama sekolah menjadi "SD Test Updated"
-   [ ] Klik "Update"
-   [ ] Verifikasi perubahan tersimpan
-   [ ] Verifikasi nama berubah di daftar

#### Test 11: Hapus Sekolah

-   [ ] Klik ikon "Hapus" pada sekolah test
-   [ ] Klik "Ya, Hapus" di konfirmasi
-   [ ] Verifikasi sekolah terhapus dari daftar

#### Test 12: Cetak Data Sekolah

-   [ ] Klik ikon "Print" pada salah satu sekolah
-   [ ] Verifikasi PDF terbuka
-   [ ] Verifikasi data sekolah lengkap di PDF

#### Test 13: Filter Sekolah

-   [ ] Klik menu "Data Sekolah"
-   [ ] Pilih filter "Jenjang Pendidikan: SD"
-   [ ] Klik "Filter"
-   [ ] Verifikasi hanya sekolah SD yang muncul
-   [ ] Reset filter
-   [ ] Verifikasi semua sekolah muncul kembali

---

### ✅ Testing Manajemen Data Guru (Admin Dinas)

#### Test 14: Tambah Guru

-   [ ] Klik menu "Data Guru"
-   [ ] Klik "Tambah Guru"
-   [ ] Isi form dengan data valid:
    -   Nama Lengkap: "Guru Test"
    -   NUPTK: "1234567890123456"
    -   Sekolah: Pilih salah satu sekolah
    -   Mata Pelajaran: "Matematika"
    -   Status Kepegawaian: "PNS"
-   [ ] Upload foto (opsional)
-   [ ] Klik "Simpan"
-   [ ] Verifikasi guru tersimpan
-   [ ] Verifikasi muncul di daftar guru

#### Test 15: Edit Guru

-   [ ] Klik ikon "Edit" pada guru test
-   [ ] Ubah mata pelajaran menjadi "IPA"
-   [ ] Klik "Update"
-   [ ] Verifikasi perubahan tersimpan

#### Test 16: Filter Guru

-   [ ] Gunakan filter berdasarkan sekolah
-   [ ] Verifikasi hanya guru di sekolah tersebut yang muncul
-   [ ] Gunakan filter berdasarkan mata pelajaran
-   [ ] Verifikasi filter bekerja dengan benar

---

### ✅ Testing Manajemen Data Siswa (Admin Dinas)

#### Test 17: Tambah Siswa

-   [ ] Klik menu "Data Siswa"
-   [ ] Klik "Tambah Siswa"
-   [ ] Isi form dengan data valid:
    -   Nama Lengkap: "Siswa Test"
    -   NISN: "1234567890"
    -   Sekolah: Pilih salah satu sekolah
    -   Kelas: "1A"
    -   Status: "Aktif"
-   [ ] Upload foto (opsional)
-   [ ] Klik "Simpan"
-   [ ] Verifikasi siswa tersimpan

#### Test 18: Upload Sertifikat Siswa

-   [ ] Klik ikon "Detail" pada siswa test
-   [ ] Klik tab "Sertifikat"
-   [ ] Klik "Upload Sertifikat"
-   [ ] Pilih file PDF
-   [ ] Masukkan nama sertifikat: "Sertifikat Test"
-   [ ] Klik "Upload"
-   [ ] Verifikasi sertifikat tersimpan
-   [ ] Test download sertifikat

#### Test 19: Tambah Rapor Siswa

-   [ ] Klik tab "Rapor"
-   [ ] Klik "Tambah Rapor"
-   [ ] Isi data rapor:
    -   Semester: "1"
    -   Tahun Ajaran: "2024/2025"
-   [ ] Upload file rapor
-   [ ] Klik "Simpan"
-   [ ] Verifikasi rapor tersimpan

---

### ✅ Testing Import/Export Data

#### Test 20: Download Template Sekolah

-   [ ] Klik menu "Data Sekolah"
-   [ ] Klik "Download Template"
-   [ ] Verifikasi file Excel terdownload
-   [ ] Buka file Excel
-   [ ] Verifikasi format kolom sesuai dokumentasi

#### Test 21: Import Data Sekolah

-   [ ] Buka template Excel yang didownload
-   [ ] Isi beberapa baris data sekolah dengan format:
    -   NPSN: "87654321"
    -   NAMA_SEKOLAH: "SMP Test"
    -   ALAMAT: "Jl. Import Test"
    -   JENJANG_PENDIDIKAN: "SMP"
    -   STATUS: "Negeri"
    -   AKSI: "CREATE"
-   [ ] Simpan file Excel
-   [ ] Klik "Import Data" di sistem
-   [ ] Pilih file Excel yang sudah diisi
-   [ ] Klik "Upload"
-   [ ] Verifikasi proses import berhasil
-   [ ] Verifikasi data sekolah muncul di daftar

#### Test 22: Import Data Guru

-   [ ] Download template guru
-   [ ] Isi data guru dengan format yang benar
-   [ ] Import data guru
-   [ ] Verifikasi data guru tersimpan

#### Test 23: Import Data Siswa

-   [ ] Download template siswa
-   [ ] Isi data siswa dengan format yang benar
-   [ ] Import data siswa
-   [ ] Verifikasi data siswa tersimpan

#### Test 24: Export Laporan

-   [ ] Klik menu "Laporan"
-   [ ] Pilih "Laporan Sekolah"
-   [ ] Klik "Export Excel"
-   [ ] Verifikasi file Excel terdownload
-   [ ] Buka file dan verifikasi data lengkap

---

### ✅ Testing Laporan dan Statistik

#### Test 25: Laporan Admin Dinas

-   [ ] Klik menu "Laporan"
-   [ ] Test semua jenis laporan:
    -   Laporan Sekolah
    -   Laporan Guru
    -   Laporan Siswa
    -   Laporan Kelulusan
    -   Laporan Tenaga Kependidikan
-   [ ] Gunakan filter pada setiap laporan
-   [ ] Export laporan ke Excel
-   [ ] Verifikasi data laporan akurat

#### Test 26: Statistik Pendidikan

-   [ ] Buka halaman statistik publik
-   [ ] Test semua jenis statistik
-   [ ] Test filter berdasarkan jenjang
-   [ ] Verifikasi grafik menampilkan data yang benar

---

### ✅ Testing Manajemen User

#### Test 27: Tambah User Baru

-   [ ] Klik menu "Manajemen User"
-   [ ] Klik "Tambah User"
-   [ ] Isi form user baru:
    -   Nama: "User Test"
    -   Email: "test@example.com"
    -   Password: "password123"
    -   Role: "Admin Sekolah"
    -   Sekolah: Pilih salah satu sekolah
-   [ ] Klik "Simpan"
-   [ ] Verifikasi user tersimpan

#### Test 28: Edit User

-   [ ] Klik ikon "Edit" pada user test
-   [ ] Ubah role menjadi "Guru"
-   [ ] Klik "Update"
-   [ ] Verifikasi perubahan tersimpan

#### Test 29: Hapus User

-   [ ] Klik ikon "Hapus" pada user test
-   [ ] Konfirmasi penghapusan
-   [ ] Verifikasi user terhapus

---

### ✅ Testing Fitur Guru

#### Test 30: Login Guru

-   [ ] Logout dari admin
-   [ ] Login dengan akun guru
-   [ ] Verifikasi dashboard guru muncul

#### Test 31: Edit Profil Guru

-   [ ] Klik menu "Profil"
-   [ ] Edit data profil
-   [ ] Upload foto profil
-   [ ] Klik "Update"
-   [ ] Verifikasi perubahan tersimpan

#### Test 32: Upload Dokumen Guru

-   [ ] Klik menu "Dokumen"
-   [ ] Klik "Tambah Dokumen"
-   [ ] Upload file dokumen
-   [ ] Masukkan nama dokumen
-   [ ] Klik "Upload"
-   [ ] Verifikasi dokumen tersimpan
-   [ ] Test download dokumen

#### Test 33: Lihat Data Siswa (Guru)

-   [ ] Klik menu "Data Siswa"
-   [ ] Verifikasi daftar siswa muncul
-   [ ] Klik nama siswa untuk detail
-   [ ] Verifikasi data siswa lengkap

#### Test 34: Cetak Profil Guru

-   [ ] Klik menu "Profil"
-   [ ] Klik "Print Profil"
-   [ ] Verifikasi PDF profil terbuka

---

### ✅ Testing Admin Sekolah

#### Test 35: Login Admin Sekolah

-   [ ] Logout dari guru
-   [ ] Login dengan akun admin sekolah
-   [ ] Verifikasi dashboard admin sekolah muncul

#### Test 36: Manajemen Data Sekolah Sendiri

-   [ ] Test tambah guru di sekolah sendiri
-   [ ] Test tambah siswa di sekolah sendiri
-   [ ] Test edit data guru
-   [ ] Test edit data siswa
-   [ ] Verifikasi tidak bisa akses data sekolah lain

#### Test 37: Laporan Sekolah

-   [ ] Klik menu "Laporan"
-   [ ] Test laporan siswa sekolah
-   [ ] Test laporan guru sekolah
-   [ ] Test laporan rapor
-   [ ] Export laporan

---

### ✅ Testing Error Handling

#### Test 38: Validasi Form

-   [ ] Test submit form dengan data kosong
-   [ ] Test submit form dengan format email salah
-   [ ] Test submit form dengan NPSN duplikat
-   [ ] Test submit form dengan NISN duplikat
-   [ ] Verifikasi error message muncul dengan benar

#### Test 39: File Upload Error

-   [ ] Test upload file dengan format tidak didukung
-   [ ] Test upload file dengan ukuran terlalu besar
-   [ ] Verifikasi error message muncul

#### Test 40: Import Error

-   [ ] Test import file dengan format kolom salah
-   [ ] Test import file dengan data duplikat
-   [ ] Test import file kosong
-   [ ] Verifikasi error handling bekerja dengan benar

---

### ✅ Testing Responsive Design

#### Test 41: Mobile View

-   [ ] Buka aplikasi di mobile browser
-   [ ] Test semua fitur utama di mobile
-   [ ] Verifikasi menu responsive
-   [ ] Verifikasi form responsive

#### Test 42: Tablet View

-   [ ] Buka aplikasi di tablet
-   [ ] Test semua fitur di tablet
-   [ ] Verifikasi layout responsive

---

### ✅ Testing Performance

#### Test 43: Load Testing

-   [ ] Test dengan data banyak (1000+ sekolah)
-   [ ] Test pencarian dengan data banyak
-   [ ] Test import file besar (1000+ baris)
-   [ ] Verifikasi performa masih baik

#### Test 44: Database Performance

-   [ ] Monitor query execution time
-   [ ] Test dengan concurrent users
-   [ ] Verifikasi tidak ada query yang lambat

---

## 📊 TEMPLATE TESTING REPORT

### Test Summary

-   **Total Tests**: 44
-   **Passed**: \_\_\_/44
-   **Failed**: \_\_\_/44
-   **Skipped**: \_\_\_/44

### Failed Tests

| Test ID | Test Name | Issue | Status |
| ------- | --------- | ----- | ------ |
|         |           |       |        |

### Recommendations

1.
2.
3.

---

## 🚨 CRITICAL ISSUES TO FIX

### High Priority

-   [ ]
-   [ ]
-   [ ]

### Medium Priority

-   [ ]
-   [ ]
-   [ ]

### Low Priority

-   [ ]
-   [ ]
-   [ ]

---

_Panduan Testing Simpedas v1.0 - Terakhir diupdate: [Tanggal]_
