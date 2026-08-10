<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Dokter</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
        }
        .header h3 {
            margin: 5px 0 0 0;
            font-size: 12pt;
            font-weight: normal;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            margin-top: 25px;
            background-color: #f2f2f2;
            padding: 5px;
            border-left: 4px solid #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #e0e0e0;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- KOP LAPORAN -->
    <div class="header">
        <h2>RSUD PASAR MINGGU</h2>
        <h3>LAPORAN KINERJA PELAYANAN DOKTER</h3>
    </div>

    <!-- INFORMASI FILTER -->
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Dokter</strong></td>
            <td width="2%">:</td>
            <td width="83%"><strong><?= isset($nama_dokter) ? $nama_dokter : '-' ?></strong></td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>:</td>
            <td><?= $startdate ?> s.d. <?= $endate ?></td>
        </tr>
    </table>

    <!-- BAGIAN 1: AKTIVITAS JENIS PELAYANAN -->
    <div class="section-title">1. Aktivitas Dokter Jenis Pelayanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Jenis Pelayanan</th>
                <th width="30%">Nama Dokter</th>
                <th width="30%">Nama Tindakan / Pelayanan</th>
                <th width="10%">Total Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $totalQty = 0; 
            if(!empty($rincian)):
                foreach($rincian as $r): 
                    $jenis         = isset($r['JENIS']) ? $r['JENIS'] : '-';
                    $dokter        = !empty($r['NAMADOKTER']) ? $r['NAMADOKTER'] : (!empty($r['DOKTERID']) ? $r['DOKTERID'] : '-');
                    $nama_tindakan = !empty($r['NAMAPELAYANAN']) ? $r['NAMAPELAYANAN'] : (!empty($r['LAYAN_ID']) ? $r['LAYAN_ID'] : '-');
                    $qty           = isset($r['TOTAL_QTY']) ? (int)$r['TOTAL_QTY'] : 0;
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $jenis ?></td>
                <td><?= $dokter ?></td>
                <td><?= $nama_tindakan ?></td>
                <td class="text-center"><?= $qty ?></td>
            </tr>
            <?php 
                $totalQty += $qty;
                endforeach; 
            else:
            ?>
            <tr>
                <td colspan="5" class="text-center">Tidak ada data ditemukan</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">TOTAL TINDAKAN:</td>
                <td class="text-center font-bold"><?= $totalQty ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- BAGIAN 2: REKAP PASIEN BY DPJP -->
    <div class="section-title">2. Rekap Jumlah Pasien by DPJP</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Jenis Pelayanan</th>
                <th width="30%">Periode</th>
                <th width="20%">Total Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no2 = 1; 
            $totalKunjungan = 0;
            if(!empty($rekap)): 
                foreach($rekap as $p): 
                    $jenis_pelayanan = isset($p['JENIS']) ? $p['JENIS'] : '-';
                    $periode         = isset($p['PERIODE']) ? $p['PERIODE'] : '-';
                    $kunjungan       = isset($p['TOTAL_KUNJUNGAN']) ? (int)$p['TOTAL_KUNJUNGAN'] : 0;
            ?>
            <tr>
                <td class="text-center"><?= $no2++ ?></td>
                <td><?= $jenis_pelayanan ?></td>
                <td class="text-center"><?= $periode ?></td>
                <td class="text-center"><?= $kunjungan ?></td>
            </tr>
            <?php 
                $totalKunjungan += $kunjungan; 
                endforeach; 
            else:
            ?>
            <tr>
                <td colspan="4" class="text-center">Tidak ada data ditemukan</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold">TOTAL KUNJUNGAN PASIEN:</td>
                <td class="text-center font-bold"><?= $totalKunjungan ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>