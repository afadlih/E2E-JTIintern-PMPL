<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Mahasiswa</title>
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
        }
        th {
            background-color: #f4f4f4;
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
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .status-aktif {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .status-selesai {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        .status-menunggu {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        .status-belum {
            background-color: #f5f5f5;
            color: #616161;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Data Mahasiswa JTI-Politeknik Negeri Malang</div>
        <div class="subtitle">
            Diekspor pada: {{ $timestamp }}<br>
            Total Data: {{ $total }} Mahasiswa
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Kelas</th>
                <th>IPK</th>
                <th>Status Magang</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswa as $index => $mhs)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $mhs->name }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama_kelas }}</td>
                <td>{{ $mhs->ipk }}</td>
                <td>
                    <span class="status {{ strtolower(str_replace(' ', '-', $mhs->status_magang)) }}">
                        {{ $mhs->status_magang }}
                    </span>
                </td>
                <td>{{ $mhs->alamat }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh sistem JTI-Intern
    </div>
</body>
</html>