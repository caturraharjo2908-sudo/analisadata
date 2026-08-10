<!DOCTYPE html>
<html>
<head>
    <title>Export Excel Inventori Farmasi</title>
    <style>
        /* Class 'str' digunakan untuk memaksa Excel membaca sel sebagai Text (bukan angka/scientific) */
        .str { 
            mso-number-format:"\@"; 
        } 
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 5px;
        }
        th {
            background-color: #D3D3D3;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Judul Excel (Opsional, bisa dihapus jika hanya ingin murni tabel) -->
    <table>
        <tr>
            <th colspan="4" style="font-size: 16px; background-color: #ffffff; border: none; text-align: left;">
                LAPORAN INVENTORI FARMASI (STOK KESELURUHAN)
            </th>
        </tr>
        <tr>
            <th colspan="4" style="background-color: #ffffff; border: none; text-align: left;">
                Tanggal Tarik Data: <?= date('d-m-Y H:i:s') ?>
            </th>
        </tr>
        <tr>
            <td colspan="4" style="border: none;"></td>
        </tr>
    </table>

    <!-- Tabel Data Utama -->
    <table>
        <thead>
            <tr>
                <th>KODE OBAT</th>
                <th>NAMA OBAT</th>
                <th>SATUAN</th>
                <th>TOTAL STOK KESELURUHAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($stok_farmasi)): ?>
                <?php foreach($stok_farmasi as $row): ?>
                    <tr>
                        <td class="str"><?= $row['OBAT_ID'] ?></td>
                        <td><?= $row['NAMA_OBAT'] ?></td>
                        <td><?= $row['SATUAN'] ?></td>
                        <td style="text-align: right;"><?= $row['TOTAL_STOK_KESELURUHAN'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data stok.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>