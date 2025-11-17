<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Perusahaan</title>
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
        .contact-info {
            font-size: 10px;
            color: #666;
        }
        .website {
            color: #1a0dab;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Data Perusahaan Mitra JTI-Politeknik Negeri Malang</div>
        <div class="subtitle">
            Diekspor pada: {{ $timestamp }}<br>
            Total Data: {{ $total }} Perusahaan
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Perusahaan</th>
                <th>Wilayah</th>
                <th>Alamat</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Website</th>
                <th>Lowongan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perusahaan as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->nama_perusahaan }}</td>
                <td>{{ $p->nama_kota ?? 'N/A' }}</td>
                <td>{{ $p->alamat_perusahaan ?? 'N/A' }}</td>
                <td>{{ $p->contact_person ?? 'N/A' }}</td>
                <td>{{ $p->email ?? 'N/A' }}</td>
                <td class="website">{{ $p->website ?? 'N/A' }}</td>
                <td>{{ $p->lowongan_count }} Lowongan</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh sistem JTI-Intern pada {{ $timestamp }}
    </div>
</body>
</html>