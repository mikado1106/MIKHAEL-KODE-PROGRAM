<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h3 {
            margin: 0 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .sub {
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h3>Rekap Absensi</h3>
    <div class="sub">Periode: <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?></div>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th class="center">Hadir</th>
                <th class="center">Terlambat</th>
                <th class="center">Pulang Cepat</th>
                <th class="center">Durasi Total</th>
                <th class="center">Rata-rata</th>
                <th class="center">Izin</th>
                <th class="center">Cuti</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nama']) ?></td>
                        <td class="center"><?= (int)$r['hadir'] ?></td>
                        <td class="center"><?= (int)$r['terlambat'] ?></td>
                        <td class="center"><?= (int)$r['pulang_cepat'] ?></td>
                        <td class="center"><?= htmlspecialchars($r['durasi_total']) ?></td>
                        <td class="center"><?= htmlspecialchars($r['durasi_rata']) ?></td>
                        <td class="center"><?= (int)$r['izin'] ?></td>
                        <td class="center"><?= (int)$r['cuti'] ?></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="8" class="center">Tidak ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>