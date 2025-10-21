<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Guru - {{ $teacher->full_name }}</title>
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
            border-radius: 50%;
        }

        .photo-placeholder {
            width: 150px;
            height: 150px;
            border: 2px solid #ddd;
            border-radius: 50%;
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
        <h1>BIODATA GURU</h1>
        <p>Dinas Pendidikan Kota Pematang Siantar</p>
        <p>Jl. Merdeka No.228c, Dwikora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146</p>
    </div>

    <div class="profile-section">
        <div class="photo-section">
            @if($teacher->photo)
                <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Foto {{ $teacher->full_name }}">
            @else
                <div class="photo-placeholder">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
            @endif
        </div>

        <div class="info-section">
            <h2>{{ $teacher->full_name }}</h2>
            <p><strong>{{ $teacher->school->name ?? '-' }}</strong></p>
            <p>Status: {{ $teacher->employment_status }}</p>
        </div>
    </div>

    <div class="details-grid">
        <div class="column-left">
            <div class="detail-item">
                <div class="detail-label">NUPTK</div>
                <div class="detail-value">{{ $teacher->nuptk ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">NIP</div>
                <div class="detail-value">{{ $teacher->nip ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tempat, Tanggal Lahir</div>
                <div class="detail-value">{{ strtoupper($teacher->birth_place) }}, {{ $teacher->birth_date->translatedFormat('d - F - Y') }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenis Kelamin</div>
                <div class="detail-value">{{ $teacher->gender }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenis PTK</div>
                <div class="detail-value">{{ $teacher->jenis_ptk ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Status Kepegawaian</div>
                <div class="detail-value">{{ $teacher->employment_status ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tenaga Pendidikan Satuan Kerja</div>
                <div class="detail-value">{{ $teacher->school->name ?? '-' }}</div>
            </div>
        </div>

        <div class="column-right">
            <div class="detail-item">
                <div class="detail-label">Status Kepegawaian</div>
                <div class="detail-value">{{ $teacher->employment_status }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Gelar Depan</div>
                <div class="detail-value">{{ $teacher->gelar_depan ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Gelar Belakang</div>
                <div class="detail-value">{{ $teacher->gelar_belakang ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">TMT Mengajar</div>
                <div class="detail-value">{{ $teacher->tmt ? $teacher->tmt->translatedFormat('d - F - Y') : '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Mata Pelajaran Yang Di Ajar</div>
                <div class="detail-value">{{ $teacher->subjects }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenjang Pendidikan</div>
                <div class="detail-value">{{ $teacher->jenjang ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jurusan/Prodi</div>
                <div class="detail-value">{{ $teacher->education_major ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Mengajar</div>
                <div class="detail-value">{{ $teacher->mengajar ?? $teacher->subjects ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Sistem Pangkalan Data Dinas Pendidikan Kota Pematang Siantar</p>
    </div>
</body>
</html>
