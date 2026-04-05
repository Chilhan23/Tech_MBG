<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
            margin: 25px;
        }

        .kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-logo {
            width: 60px;
            height: auto;
            object-fit: contain;
            float: left;
        }

        .kop-text {
            transform: translateX(-28px);
        }

        .kop h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop p {
            margin: 2px 0;
            font-size: 12px;
        }

        .judul {
            text-align: center;
            margin: 15px 0;
        }

        .judul h2 {
            font-size: 14px;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .meta {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .meta table {
            width: 100%;
        }

        .meta td {
            padding: 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th, table.data td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        table.data th {
            background-color: #e5e7eb;
            font-weight: bold;
        }

        table.data td.text-left {
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .ttd {
            width: 200px;
            text-align: center;
            float: right;
        }

        .ttd .nama {
            margin-top: 60px;
            text-decoration: underline;
            font-weight: bold;
        }

        .ringkasan {
            margin-top: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="kop">
    <img src="/images/pancacita-provinsi-aceh-seeklogo.png" alt="Logo Pancasila" class="kop-logo">
    <div class="kop-text">
        <h1>Pemerintah Aceh</h1>
        <p>DINAS PENDIDIKAN</p>
        <p>SMK NEGERI 5 Telkom Banda Aceh</p>
        <p>Jl. Stadion H. Dimurthala No. 5, Kota Baru, Kecamatan Kuta Alam, Kota Banda Aceh, Aceh</p>
    </div>
</div>

<div class="judul">
    <h2>LAPORAN ABSENSI SISWA</h2>
    <p>PROGRAM MAKAN BERGIZI GRATIS (MBG)</p>
</div>

<div class="meta">
    <table>
        <tr>
            <td width="50%">
                Tanggal : {{ $labelTanggal }} <br>
                Kelas : {{ $labelScope }} <br>
                Jurusan : {{ $labelJurusan }}
            </td>
            <td width="50%" style="text-align:right;">
                Dicetak : {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
</div>

<table class="data">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>NISN</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Jam Ambil</th>
            <th>Jam Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="text-left">{{ $row['name'] }}</td>
                <td>{{ $row['nisn'] }}</td>
                <td>{{ $row['kelas'] }}</td>
                <td>{{ $row['jurusan'] }}</td>
                <td>{{ $row['waktu_ambil'] ?? '-' }}</td>
                <td>{{ $row['waktu_kembali'] ?? '-' }}</td>
                <td>
                    @if ($row['sudah_ambil'] && $row['waktu_kembali'])
                        Sudah DiKembalikan
                    @elseif ($row['sudah_ambil'])
                        Belum Kembali
                    @else
                        Tidak Hadir
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">Tidak ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

@php
    $sudah = collect($rows)->where('sudah_ambil', true)->count();
    $kembali = collect($rows)->whereNotNull('waktu_kembali')->count();
    $belum = collect($rows)->where('sudah_ambil', false)->count();
    $total = count($rows);
@endphp

<div class="ringkasan">
    <strong>Ringkasan:</strong><br>
    Total Siswa: {{ $total }} <br>
    Sudah Ambil: {{ $sudah }} <br>
    Sudah Kembali: {{ $kembali }} <br>
    Belum Ambil: {{ $belum }}
</div>

<div class="footer">
    <div class="ttd">
        <p>{{ now()->translatedFormat('d F Y') }},Banda Aceh</p>
        <p>Petugas,</p>

        <div class="nama">
            (............................)
        </div>
    </div>
</div>

</body>
</html>