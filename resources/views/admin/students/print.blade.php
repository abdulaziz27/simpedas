<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Siswa - {{ $student->full_name }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0d524a;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .profile-section {
            display: flex;
            margin-bottom: 30px;
            align-items: flex-start;
        }

        .photo-section {
            width: 150px;
            margin-right: 30px;
        }

        .photo-section img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .photo-placeholder {
            width: 150px;
            height: 150px;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            color: #666;
            text-align: center;
            font-size: 12px;
        }

        .info-section {
            flex: 1;
        }

        .info-section h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #0d524a;
        }

        .info-section p {
            margin: 5px 0;
            font-size: 16px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: bold;
            color: #0d524a;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
        }

        .print-btn {
            background-color: #0d524a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .print-btn:hover {
            background-color: #17695a;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Cetak Dokumen</button>

    <div class="header">
        <h1>BIODATA SISWA</h1>
        <p>Dinas Pendidikan Kota Pematang Siantar</p>
        <p>Jl. Merdeka No.228c, Dwikora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146</p>
    </div>

    <div class="profile-section">
        <div class="photo-section">
            @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->full_name }}">
            @else
                <div class="photo-placeholder">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            @endif
        </div>

        <div class="info-section">
            <h2>{{ $student->full_name }}</h2>
            <p><strong>{{ $student->school->name ?? 'Sekolah tidak ditemukan' }}</strong></p>
            <p>NISN: {{ $student->nisn ?? '-' }}</p>
            <p>Status: {{ $student->student_status ?? '-' }}</p>
        </div>
    </div>

    <div class="details-grid">
        <div class="column-left">
            <div class="detail-item">
                <div class="detail-label">Nama Lengkap</div>
                <div class="detail-value">{{ $student->full_name ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">NISN</div>
                <div class="detail-value">{{ $student->nisn ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">NIS</div>
                <div class="detail-value">{{ $student->nis ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tempat Lahir</div>
                <div class="detail-value">{{ $student->birth_place ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tanggal Lahir</div>
                <div class="detail-value">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d F Y') : '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenis Kelamin</div>
                <div class="detail-value">{{ $student->gender ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Agama</div>
                <div class="detail-value">{{ $student->religion ?? '-' }}</div>
            </div>
        </div>

        <div class="column-right">
            <div class="detail-item">
                <div class="detail-label">Sekolah</div>
                <div class="detail-value">{{ $student->school->name ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tingkat Kelas</div>
                <div class="detail-value">{{ $student->grade_level ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jurusan</div>
                <div class="detail-value">{{ $student->major ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Status Siswa</div>
                <div class="detail-value">{{ $student->student_status ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Status Kelulusan</div>
                <div class="detail-value">{{ $student->graduation_status ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tahun Ajaran</div>
                <div class="detail-value">{{ $student->academic_year ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Prestasi</div>
                <div class="detail-value">{{ $student->achievements ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Sistem Pangkalan Data Dinas Pendidikan Kota Pematang Siantar</p>
    </div>
</body>
</html>
