<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Dosen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px 0;
        }
        .bimbingan-count {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Data Dosen JTI-Politeknik Negeri Malang</div>
        <div class="subtitle">
            Diekspor pada: {{ $timestamp }}<br>
            Total Data: {{ $total }} Dosen
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Dosen</th>
                <th style="width: 15%;">NIP</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 20%;">Wilayah</th>
                <th style="width: 15%;">Jumlah Bimbingan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dosen as $index => $d)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $d->nama_dosen ?? 'N/A' }}</td>
                <td>{{ $d->nip ?? 'N/A' }}</td>
                <td>{{ $d->email ?? 'N/A' }}</td>
                <td>{{ $d->wilayah ?? 'N/A' }}</td>
                <td class="bimbingan-count">{{ $d->jumlah_bimbingan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh sistem JTI-Intern pada {{ $timestamp }}
    </div>
</body>
</html>