<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Sekolah - {{ $school->name }}</title>
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
        <h1>BIODATA SEKOLAH</h1>
        <p>Dinas Pendidikan Kota Pematang Siantar</p>
        <p>Jl. Merdeka No.228c, Dwikora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146</p>
    </div>

    <div class="profile-section">
        <div class="photo-section">
            @if($school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}" class="school-logo">
            @else
                <div class="photo-placeholder">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                    </svg>
                </div>
            @endif
        </div>

        <div class="info-section">
            <h2>{{ $school->name }}</h2>
            <p><strong>{{ $school->education_level }} - {{ $school->status }}</strong></p>
            <p>NPSN: {{ $school->npsn }}</p>
        </div>
    </div>

    <div class="details-grid">
        <div class="column-left">
            <div class="detail-item">
                <div class="detail-label">Nama Sekolah</div>
                <div class="detail-value">{{ $school->name ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">NPSN</div>
                <div class="detail-value">{{ $school->npsn ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenjang Pendidikan</div>
                <div class="detail-value">{{ $school->education_level ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">{{ $school->status ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Alamat Lengkap</div>
                <div class="detail-value">{{ $school->address ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Desa</div>
                <div class="detail-value">{{ $school->desa ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Kecamatan</div>
                <div class="detail-value">{{ $school->kecamatan ?? '-' }}</div>
            </div>
        </div>

        <div class="column-right">
            <div class="detail-item">
                <div class="detail-label">Kabupaten/Kota</div>
                <div class="detail-value">{{ $school->kabupaten_kota ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Provinsi</div>
                <div class="detail-value">{{ $school->provinsi ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">No HP</div>
                <div class="detail-value">{{ $school->phone ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Website</div>
                <div class="detail-value">
                    @if($school->website)
                        <a href="{{ $school->website }}" target="_blank" style="color: #0d524a; text-decoration: underline;">{{ $school->website }}</a>
                    @else
                        -
                    @endif
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $school->email ?? '-' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Kepala Sekolah</div>
                <div class="detail-value">{{ $school->headmaster ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Sistem Pangkalan Data Dinas Pendidikan Kota Pematang Siantar</p>
    </div>
</body>
</html>
