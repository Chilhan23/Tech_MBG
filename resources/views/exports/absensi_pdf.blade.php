<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 12px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 4px;
        }
        .header p { font-size: 11px; color: #555; }

        .meta {
            margin-bottom: 14px;
            font-size: 10.5px;
            color: #374151;
            display: flex;
            justify-content: space-between;
        }
        .meta strong { color: #111; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }
        thead tr { background: #1d4ed8; color: white; }
        thead th { padding: 8px 6px; text-align: left; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f1f5f9; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }

        .summary {
            margin-top: 14px;
            padding: 8px 12px;
            background: #eff6ff;
            border-left: 3px solid #2563eb;
            border-radius: 4px;
            font-size: 11px;
        }

        .footer {
            margin-top: 14px;
            font-size: 10px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Absensi MBG</h1>
    <p>Program Makan Bergizi Gratis &mdash; Data Pengambilan Makanan</p>
</div>

<div class="meta">
    <div>
        <strong>Tanggal:</strong> {{ $labelTanggal }}
        &nbsp;&nbsp;
        <strong>Kelas:</strong> {{ $labelScope }}
        &nbsp;&nbsp;
        <strong>Jurusan:</strong> {{ $labelJurusan }}
    </div>
    <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:4%">No</th>
            <th style="width:26%">Nama Siswa</th>
            <th style="width:14%">NISN</th>
            <th style="width:10%">Kelas</th>
            <th style="width:24%">Jurusan</th>
            <th style="width:10%">Jam Ambil</th>
            <th style="width:12%">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td style="font-family: monospace;">{{ $row['nisn'] }}</td>
                <td>{{ $row['kelas'] }}</td>
                <td>{{ $row['jurusan'] }}</td>
                <td>{{ $row['waktu'] ?? '-' }}</td>
                <td>
                    @if ($row['sudah_ambil'])
                        <span class="badge badge-success">Sudah Ambil</span>
                    @else
                        <span class="badge badge-danger">Belum Ambil</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#9ca3af;">
                    Tidak ada data siswa.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@php
    $sudah = collect($rows)->where('sudah_ambil', true)->count();
    $belum = collect($rows)->where('sudah_ambil', false)->count();
    $total = count($rows);
@endphp

<div class="summary">
    <strong>Ringkasan:</strong>
    Total {{ $total }} siswa &nbsp;&mdash;&nbsp;
    <span style="color:#166534"><strong>✓ Sudah ambil: {{ $sudah }}</strong></span>
    &nbsp;&mdash;&nbsp;
    <span style="color:#991b1b"><strong>✗ Belum ambil: {{ $belum }}</strong></span>
    @if ($total > 0)
        &nbsp;&mdash;&nbsp; <strong>{{ round(($sudah / $total) * 100) }}%</strong> hadir
    @endif
</div>

<div class="footer">
    Digenerate otomatis oleh Sistem MBG &bull; {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>