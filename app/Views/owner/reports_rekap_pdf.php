<?php
function indoMonth($m)
{
    $arr = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $arr[(int)$m] ?? $m;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .sub {
            color: #555;
            font-size: 12px;
        }

        .grid {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .col {
            display: table-cell;
            vertical-align: top;
            padding: 6px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
        }

        .card h4 {
            margin: 0 0 6px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .b-warn {
            background: #ffe08a;
        }

        .b-ok {
            background: #c9f7c9;
        }

        .b-no {
            background: #ddd;
        }

        .footer {
            margin-top: 16px;
            font-size: 11px;
            color: #555;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2><?= esc($title) ?></h2>
        <div class="sub">Periode: <?= esc(indoMonth($month)) ?> <?= esc($year) ?> (<?= esc($start) ?> s/d <?= esc($end) ?>)</div>
    </div>

    <div class="grid">
        <div class="col" style="width:50%">
            <div class="card">
                <h4>Ringkas Izin</h4>
                <div>Menunggu: <span class="badge b-warn"><?= esc($izin['menunggu']) ?></span></div>
                <div>Disetujui: <span class="badge b-ok"><?= esc($izin['setuju']) ?></span></div>
                <div>Ditolak: <span class="badge b-no"><?= esc($izin['tolak']) ?></span></div>
            </div>
        </div>
        <div class="col" style="width:50%">
            <div class="card">
                <h4>Ringkas Cuti</h4>
                <div>Menunggu: <span class="badge b-warn"><?= esc($cuti['menunggu']) ?></span></div>
                <div>Disetujui: <span class="badge b-ok"><?= esc($cuti['setuju']) ?></span></div>
                <div>Ditolak: <span class="badge b-no"><?= esc($cuti['tolak']) ?></span></div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:32%;">Nama</th>
                <th style="width:28%;">Email</th>
                <th class="center" style="width:10%;">Izin ✔</th>
                <th class="center" style="width:10%;">Izin ✖</th>
                <th class="center" style="width:10%;">Cuti ✔</th>
                <th class="center" style="width:10%;">Cuti ✖</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                    <tr>
                        <td><?= esc($r['nama']) ?></td>
                        <td><?= esc($r['email']) ?></td>
                        <td class="center"><?= (int)$r['izin_setuju'] ?></td>
                        <td class="center"><?= (int)$r['izin_tolak'] ?></td>
                        <td class="center"><?= (int)$r['cuti_setuju'] ?></td>
                        <td class="center"><?= (int)$r['cuti_tolak'] ?></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="6" class="center">Tidak ada data pada periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: <?= esc($owner) ?> &middot; Tanggal cetak: <?= date('Y-m-d H:i') ?>
    </div>

</body>

</html>