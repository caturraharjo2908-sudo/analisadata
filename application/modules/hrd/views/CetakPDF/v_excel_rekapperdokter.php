<?php
// Membersihkan output buffering agar data Excel tidak korup
if (ob_get_level()) {
    ob_end_clean();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        th { background-color: #D3D3D3; font-weight: bold; text-align: center; padding: 5px; }
        td { padding: 5px; }
    </style>
</head>
<body>
    <h2>RSUD PASAR MINGGU</h2>
    <h3>LAPORAN KINERJA DOKTER</h3>
    <p>Nama Dokter: <b><?= isset($nama_dokter) ? $nama_dokter : '-' ?></b></p>
    <p>Periode: <b><?= $startdate ?> s.d. <?= $endate ?></b></p>
    <br>

    <strong>1. Aktivitas Dokter Jenis Pelayanan</strong>
    <table border="1">
        <thead>
            <tr>
                <th width="5">No</th>
                <th width="25">Jenis Pelayanan</th>
                <th width="35">Nama Dokter</th>
                <th width="45">Nama Tindakan / Pelayanan</th>
                <th width="15">Total Qty</th>
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
            <tr>
                <td colspan="4" class="text-right font-bold">TOTAL TINDAKAN:</td>
                <td class="text-center font-bold"><?= $totalQty ?></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <strong>2. Rekap Jumlah Pasien by DPJP</strong>
    <table border="1">
        <thead>
            <tr style="background-color: #D3D3D3;">
                <th width="5">No</th>
                <th width="40">JENIS PELAYANAN</th>
                <th width="40">PERIODE</th>
                <th width="20">TOTAL</th>
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
            <tr>
                <td colspan="3" class="text-right font-bold">TOTAL KUNJUNGAN PASIEN:</td>
                <td class="text-center font-bold"><?= $totalKunjungan ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>