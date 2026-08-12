<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Performance Database</title>
    <style>
        /* =======================================================
           PENGATURAN DASAR & TIPOGRAFI (MAGAZINE STYLE)
           ======================================================= */
        @page { margin: 40px 50px; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 10pt; 
            color: #334155; 
            line-height: 1.5; 
            background-color: #ffffff;
        }
        
        /* Header Majalah */
        .header-container {
            border-bottom: 4px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .report-title { 
            color: #0f172a;  
            font-size: 24pt; 
            font-weight: 800;
            text-transform: uppercase; 
            margin: 0 0 5px 0; 
            letter-spacing: -0.5px;
        }
        .report-meta { 
            font-size: 10pt; 
            color: #64748b; 
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Sub-Judul Bagian */
        h3 { 
            color: #0f172a; 
            font-size: 13pt; 
            border-bottom: 1px solid #cbd5e1; 
            padding-bottom: 5px; 
            margin-top: 35px; 
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* =======================================================
           KOTAK ANALISIS (EXECUTIVE SUMMARY)
           ======================================================= */
        .executive-summary { 
            background-color: #f8fafc; 
            border-left: 5px solid #2563eb; 
            padding: 20px; 
            margin-bottom: 30px; 
            page-break-inside: avoid;
        }
        .summary-title { 
            font-weight: 800; 
            color: #1e40af; 
            font-size: 11pt; 
            margin-bottom: 12px; 
            text-transform: uppercase;
        }
        .summary-text { 
            font-size: 9.5pt; 
            text-align: justify; 
            margin-bottom: 10px; 
        }
        .summary-text:last-child { margin-bottom: 0; }

        /* =======================================================
           DESAIN TABEL (CLEAN & MODERN)
           ======================================================= */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
            page-break-inside: avoid; 
        }
        /* Tabel tanpa garis vertikal untuk kesan elegan */
        th { 
            background-color: #f1f5f9; 
            color: #475569; 
            padding: 10px 8px; 
            text-align: left; 
            font-size: 9pt; 
            font-weight: 700; 
            text-transform: uppercase;
            border-bottom: 2px solid #cbd5e1;
        }
        td { 
            padding: 10px 8px; 
            font-size: 9.5pt; 
            vertical-align: middle; 
            border-bottom: 1px solid #e2e8f0;
        }
        /* Efek interaktif jika dibuka di browser */
        tr:hover td { background-color: #f8fafc; } 
        
        /* =======================================================
           UTILITAS (BADGES & ALIGNMENT)
           ======================================================= */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
        }
        .badge-kritis { background-color: #ef4444; } /* Merah */
        .badge-aman { background-color: #10b981; }   /* Hijau */
        .badge-info { background-color: #3b82f6; }   /* Biru */
        
        .text-kritis { color: #ef4444; font-weight: bold; }
        .text-aman { color: #10b981; font-weight: bold; }

        .page-break { page-break-before: always; }
        
        /* Layout Grid sederhana untuk Header Meta menggunakan tabel agar aman di Dompdf */
        .meta-table { width: 100%; border: none; margin-bottom: 0; }
        .meta-table td, .meta-table th { border: none; padding: 0; background: transparent; }
    </style>
</head>
<body>

    <!-- ================== HEADER LAPORAN ================== -->
    <div class="header-container">
        <table class="meta-table">
            <tr>
                <td>
                    <h1 class="report-title">Laporan Performance Database</h1>
                    <div class="report-meta">Dicetak Pada: <?= date('d F Y | H:i'); ?> WIB</div>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    <span class="badge badge-info">DOKUMEN RAHASIA (INTERNAL)</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================== EXECUTIVE SUMMARY (DIPINDAH KE ATAS) ================== -->
    <!-- Memberikan gaya paper/majalah di mana abstrak atau ringkasan diletakkan paling awal -->
    <div class="executive-summary">
        <div class="summary-title">Executive Summary & Analisis Sistem Pakar</div>
        
        <?php 
            // PREPARASI DATA UNTUK ANALISIS
            $ts_kritis_count = 0;
            $nama_ts_kritis = [];
            if(!empty($tablespace_usage)) {
                foreach($tablespace_usage as $row) {
                    if($row['PERCENT_USED'] >= 85) {
                        $ts_kritis_count++;
                        $nama_ts_kritis[] = $row['TABLESPACE_NAME'];
                    }
                }
            }

            $beban_tinggi = false;
            if(!empty($awr_data)) {
                $metrics_summary = [];
                foreach($awr_data as $row){
                    $metrik = $row['METRIK'] ?? $row['metrik'];
                    $avg = $row['NILAI_RATA_RATA'] ?? $row['nilai_rata_rata'];
                    
                    if(!isset($metrics_summary[$metrik])){
                        $metrics_summary[$metrik] = ['avg_total' => 0, 'count' => 0];
                    }
                    $metrics_summary[$metrik]['avg_total'] += $avg;
                    $metrics_summary[$metrik]['count']++;
                }

                foreach($metrics_summary as $name => $val){
                    $final_avg = round($val['avg_total'] / $val['count'], 2);
                    if(($name == 'Host CPU Utilization (%)' && $final_avg > 70) || 
                       ($name == 'Average Active Sessions' && $final_avg > 10)) {
                        $beban_tinggi = true;
                    }
                }
            }

            // 1. Analisis Uptime
            $uptime = intval($instance_uptime['UPTIME_DAYS'] ?? 0);
            if ($uptime > 30) {
                echo "<p class='summary-text'><strong>Ketersediaan Sistem:</strong> Database beroperasi dengan sangat stabil. Mencapai waktu aktif (<em>uptime</em>) selama <strong>{$uptime} hari</strong> tanpa interupsi, mencerminkan keandalan infrastruktur yang sangat baik.</p>";
            } else {
                echo "<p class='summary-text'><strong>Ketersediaan Sistem:</strong> Server baru saja diinisiasi ulang (<em>startup</em>) dalam <strong>{$uptime} hari</strong> terakhir. Pastikan ini merupakan bagian dari pemeliharaan rutin dan bukan akibat kegagalan sistem (<em>crash</em>).</p>";
            }

            // 2. Analisis Kapasitas Tablespace
            if ($ts_kritis_count == 0) {
                echo "<p class='summary-text'><strong>Kapasitas Ruang Penyimpanan:</strong> Indikator penyimpanan berstatus <span class='text-aman'>Sangat Sehat</span>. Tidak ditemukan adanya <em>Tablespace</em> yang mendekati ambang batas maksimal (85%). Kapasitas saat ini sangat memadai untuk menampung data operasional periode mendatang.</p>";
            } else {
                $nama_ts_gabung = implode(", ", $nama_ts_kritis);
                echo "<p class='summary-text'><strong class='text-kritis'>Peringatan Kapasitas Ruang:</strong> Sistem mendeteksi <strong>{$ts_kritis_count} Tablespace</strong> yang utilitasnya telah melampaui 85%, yakni: <strong>{$nama_ts_gabung}</strong>. Tindakan penambahan ruang (<em>Add Datafile</em>) perlu segera diagendakan guna menghindari berhentinya layanan transaksi.</p>";
            }

            // 3. Analisis Performa AWR
            if ($beban_tinggi) {
                echo "<p class='summary-text'><strong class='text-kritis'>Performa & Beban Kerja:</strong> Terekam adanya lonjakan beban CPU atau penumpukan sesi antrean yang signifikan selama periode pantau. Direkomendasikan untuk melakukan inspeksi lanjutan pada kueri-kueri berat (<em>Top SQL</em>) pada periode sibuk.</p>";
            } else {
                echo "<p class='summary-text'><strong>Performa & Beban Kerja:</strong> Arus transaksi (I/O, Sesi aktif, dan Utilisasi CPU) terpantau dalam batas kewajaran. Tidak terdeteksi adanya hambatan (<em>bottleneck</em>) yang berisiko mengganggu kelancaran operasional harian.</p>";
            }
        ?>
    </div>

    <!-- ================== BAGIAN 1: INFO SISTEM ================== -->
    <h3>1. Profil Instance & Server</h3>
    <table>
        <tr>
            <th width="25%">Nama Instance</th>
            <td width="25%" class="fw-bold"><?= $instance_uptime['INSTANCE_NAME'] ?? '-'; ?></td>
            <th width="25%">Mode Operasional</th>
            <td width="25%">
                <span class="badge badge-aman"><?= $db_status['OPEN_MODE'] ?? '-'; ?></span> 
                <span style="font-size: 8pt; color: #64748b;">(<?= $db_status['DATABASE_ROLE'] ?? '-'; ?>)</span>
            </td>
        </tr>
        <tr>
            <th>Host Server</th>
            <td><?= $instance_uptime['HOST_NAME'] ?? '-'; ?></td>
            <th>Uptime Terkini</th>
            <td class="fw-bold text-aman"><?= $instance_uptime['UPTIME_DAYS'] ?? '0'; ?> Hari</td>
        </tr>
        <tr>
            <th>Versi Engine (Oracle)</th>
            <td colspan="3" style="color: #64748b; font-style: italic;"><?= $instance_uptime['VERSION'] ?? '-'; ?></td>
        </tr>
    </table>

    <!-- ================== BAGIAN 2: KAPASITAS RUANG (TABLESPACE) ================== -->
    <h3>2. Peta Utilisasi Penyimpanan (Tablespace)</h3>
    <table>
        <thead>
            <tr>
                <th width="35%">Identitas Tablespace</th>
                <th class="text-right">Alokasi (MB)</th>
                <th class="text-right">Terpakai (MB)</th>
                <th class="text-center" width="15%">Rasio Pakai</th>
                <th class="text-center" width="15%">Indikator</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(!empty($tablespace_usage)): 
                foreach($tablespace_usage as $row): 
                    $is_kritis = ($row['PERCENT_USED'] >= 85);
            ?>
            <tr>
                <td class="fw-bold text-gray-800"><?= $row['TABLESPACE_NAME'] ?? '-'; ?></td>
                <td class="text-right"><?= isset($row['TOTAL_MB']) ? number_format($row['TOTAL_MB'], 2) : '-'; ?></td>
                <td class="text-right"><?= isset($row['USED_MB']) ? number_format($row['USED_MB'], 2) : '-'; ?></td>
                <td class="text-center <?= $is_kritis ? 'text-kritis' : 'fw-bold'; ?>">
                    <?= $row['PERCENT_USED'] ?? '0'; ?>%
                </td>
                <td class="text-center">
                    <?php if($is_kritis): ?>
                        <span class="badge badge-kritis">Penuh</span>
                    <?php else: ?>
                        <span class="badge badge-aman">Aman</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center" style="color: #94a3b8;">Tidak ada data Tablespace yang terdata.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- ================== BAGIAN 3: FAST RECOVERY AREA ================== -->
    <h3>3. Area Pemulihan & Cadangan (FRA)</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori File Recovery</th>
                <th class="text-center">Persentase Terpakai</th>
                <th class="text-center">Potensi Reclaimable</th>
                <th class="text-center">Kuantitas File</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($fra_usage)): foreach($fra_usage as $row): ?>
            <tr>
                <td class="fw-bold"><?= $row['FILE_TYPE'] ?? '-'; ?></td>
                <td class="text-center"><?= $row['PERCENT_SPACE_USED'] ?? '0'; ?>%</td>
                <td class="text-center" style="color: #64748b;"><?= $row['PERCENT_SPACE_RECLAIMABLE'] ?? '0'; ?>%</td>
                <td class="text-center"><?= $row['NUMBER_OF_FILES'] ?? '0'; ?> Unit</td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center" style="color: #94a3b8;">Log FRA kosong atau tidak terkonfigurasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Halaman baru opsional jika data tabel banyak -->
    <!-- <div class="page-break"></div> -->

    <!-- ================== BAGIAN 4: PERFORMA (AWR) ================== -->
    <h3>4. Rekam Jejak Beban Kerja (AWR)</h3>
    <table>
        <thead>
            <tr>
                <th>Metrik Pemantauan Sistem</th>
                <th class="text-center">Distribusi Rata-rata</th>
                <th class="text-center">Puncak Tertinggi (Spike)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(!empty($awr_data)): 
                // Ulangi logika mapping AWR agar bisa di-loop ke tabel
                $metrics_summary = [];
                foreach($awr_data as $row){
                    $metrik = $row['METRIK'] ?? $row['metrik'];
                    $avg = $row['NILAI_RATA_RATA'] ?? $row['nilai_rata_rata'];
                    $max = $row['NILAI_MAKSIMAL'] ?? $row['nilai_maksimal'];
                    
                    if(!isset($metrics_summary[$metrik])){
                        $metrics_summary[$metrik] = ['avg_total' => 0, 'max_peak' => 0, 'count' => 0];
                    }
                    $metrics_summary[$metrik]['avg_total'] += $avg;
                    if($max > $metrics_summary[$metrik]['max_peak']) {
                        $metrics_summary[$metrik]['max_peak'] = $max;
                    }
                    $metrics_summary[$metrik]['count']++;
                }

                foreach($metrics_summary as $name => $val):
                    $final_avg = round($val['avg_total'] / $val['count'], 2);
                    $final_max = $val['max_peak'];
            ?>
            <tr>
                <td class="fw-bold text-gray-800"><?= $name; ?></td>
                <td class="text-center"><?= $final_avg; ?></td>
                <td class="text-center fw-bold text-info"><?= $final_max; ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="3" class="text-center" style="color: #94a3b8;">Arsip histori performa (AWR) belum tersedia.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer Laporan -->
    <div style="margin-top: 50px; text-align: center; color: #94a3b8; font-size: 8pt; border-top: 1px solid #e2e8f0; padding-top: 10px;">
        Dihasilkan secara otomatis oleh Sistem Pemantauan Database Internal. <br>
        Dokumen ini sah dan tidak memerlukan tanda tangan basah.
    </div>

</body>
</html>