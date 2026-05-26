<?php
if (ob_get_level()) {
    ob_end_clean();
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        th { background-color: #D3D3D3; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <?php 
        // Mengambil nama dokter dari baris pertama data rincian secara dinamis
        $nama_dokter = !empty($rincian) ? $rincian[0]['NAMA_DOKTER'] : '-'; 
    ?>

    <h2>RSUD PASAR MINGGU</h2>
    <h3>LAPORAN REKAPITULASI AKTIVITAS DOKTER</h3>
    <p>Nama Dokter: <b><?= $nama_dokter ?></b></p>
    <p>Periode: <b><?= $startdate ?> s.d. <?= $endate ?></b></p>
    <br>

    <strong>1. Rekapitulasi Pelayanan dan Tindakan</strong>
    <table border="1">
        <thead>
            <tr>
                <th width="5">No</th>
                <th width="15">Kode</th>
                <th width="50">Nama Pelayanan</th>
                <th width="15">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $totalQty = 0; foreach($rekap as $r): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center">'<?= $r['LAYAN_ID'] ?></td>
                <td><?= $r['NAMAPELAYANAN'] ?></td>
                <td class="text-center"><?= $r['JML'] ?></td>
            </tr>
            <?php $totalQty += $r['JML']; endforeach; ?>
            <tr>
                <td colspan="3" class="text-right font-bold">TOTAL TINDAKAN:</td>
                <td class="text-center font-bold"><?= $totalQty ?></td>
            </tr>
        </tbody>
    </table>

    <br>

    <strong>2. Ringkasan Kunjungan Pasien Harian</strong>
    <table border="1">
        <thead>
            <tr>
                <th width="5">No</th>
                <th width="20">Tanggal</th>
                <th width="45">Nama Dokter</th>
                <th width="15">Jml Pasien</th>
            </tr>
        </thead>
        <tbody>
            <?php $no2 = 1; $totalPasien = 0; foreach($rincian as $p): ?>
            <tr>
                <td class="text-center"><?= $no2++ ?></td>
                <td class="text-center"><?= $p['TANGGAL'] ?></td>
                <td><?= $p['NAMA_DOKTER'] ?></td>
                <td class="text-center"><?= $p['JMLPASIEN'] ?></td>
            </tr>
            <?php $totalPasien += $p['JMLPASIEN']; endforeach; ?>
            <tr>
                <td colspan="3" class="text-right font-bold">TOTAL PASIEN:</td>
                <td class="text-center font-bold"><?= $totalPasien ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>