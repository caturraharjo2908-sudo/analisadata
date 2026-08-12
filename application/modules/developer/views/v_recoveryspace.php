<?php 
    // --- PREPARASI DATA UNTUK GRAFIK LINGKARAN FRA ---
    $chart_labels = [];
    $chart_data = [];
    $total_used = 0;

    if(!empty($fra_usage)){
        foreach($fra_usage as $row){
            if($row['PERCENT_SPACE_USED'] > 0){
                $chart_labels[] = $row['FILE_TYPE'];
                $chart_data[] = $row['PERCENT_SPACE_USED'];
                $total_used += $row['PERCENT_SPACE_USED'];
            }
        }
    }
    $free_space = 100 - $total_used;
    if($free_space > 0){
        $chart_labels[] = 'FREE SPACE (KOSONG)';
        $chart_data[] = $free_space;
    }
?>
<!-- <a href="<?= base_url('recoveryspace/cetak_laporan_bulanan'); ?>" class="btn btn-primary" target="_blank">
    <i class="fa-solid fa-file-pdf"></i> Cetak Laporan PDF
</a> -->


<div class="row gy-5 g-xl-8 mb-xl-8"> 
    
    <div class="col-xl-4">

        <!-- =============== MULAI CARD DOWNLOAD PDF =============== -->
        <div class="card card-flush shadow-sm mb-5 mb-xl-8 border-start border-danger border-4">
            <div class="card-body p-5 d-flex flex-column justify-content-center text-center">
                <h4 class="text-gray-800 fw-bolder mb-2">Laporan Kinerja Bulanan</h4>
                <span class="text-muted fw-bold fs-7 mb-4">Unduh rekapitulasi performa dan kapasitas database dalam format PDF.</span>
                
                <a href="<?= site_url('developer/recoveryspace/cetak_laporan_bulanan'); ?>" class="btn btn-danger fw-bolder shadow-sm w-100" target="_blank">
                    <i class="fa-solid fa-file-pdf me-2 fs-4"></i> Download PDF Sekarang
                </a>
            </div>
        </div>
        <!-- =============== AKHIR CARD DOWNLOAD PDF =============== -->
        
        <div class="row g-3 mb-5 mb-xl-8">
            <div class="col-sm-6">
                <button class="btn btn-primary w-100 h-100 fw-bolder fs-7 shadow-sm d-flex flex-column justify-content-center align-items-center py-3" onclick="location.reload();">
                    <i class="fa-solid fa-arrows-rotate mb-2" style="font-size: 1.8rem;"></i> 
                    <br><span>Refresh Monitoring</span>
                </button>
            </div>
            <div class="col-sm-6">
                <div class="card card-flush shadow-sm h-100 border border-light bg-light">
                    <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center text-center">
                        <span class="text-muted fw-bold fs-9 text-uppercase mb-1"><i class="fa-regular fa-calendar me-1"></i> Waktu Sistem</span>
                        <span class="fw-bolder fs-8 text-gray-800" id="live-date">--</span>
                        <span class="fw-bolder fs-3 text-primary mt-1" id="live-time" style="font-family: monospace;">--:--:--</span>
                    </div>
                </div>
            </div>
        </div>

        



        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-power-off text-success me-2"></i> Instance Uptime</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Informasi Server & Waktu Aktif</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-8 gy-3 mb-0">
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td class="w-50">Instance / Host</td>
                            <td class="text-end text-primary fs-6">
                                <?= $instance_uptime['INSTANCE_NAME'] ?? '-'; ?> 
                                <span class="text-muted fs-8 fw-normal">@ <?= $instance_uptime['HOST_NAME'] ?? '-'; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Versi Oracle</td>
                            <td class="text-end"><?= $instance_uptime['VERSION'] ?? '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Status Instance</td>
                            <td class="text-end">
                                <?php 
                                    $status = $instance_uptime['STATUS'] ?? '-';
                                    $badge_class = ($status == 'OPEN') ? 'bg-success' : 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $badge_class; ?>"><?= $status; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Waktu Startup</td>
                            <td class="text-end text-muted"><?= $instance_uptime['STARTUP_TIME'] ?? '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Total Uptime</td>
                            <td class="text-end text-success fw-bolder fs-5">
                                <?= $instance_uptime['UPTIME_DAYS'] ?? '0'; ?> <span class="fs-7 text-muted fw-normal">Hari</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>    


        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-server text-primary me-2"></i> Info Database</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Status DB & Data Guard</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-8 gy-3 mb-0">
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td class="w-50">Nama DB</td>
                            <td class="text-end text-primary fs-6"><?= $db_status['NAME'] ?? '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Open Mode</td>
                            <td class="text-end"><span class="badge bg-success"><?= $db_status['OPEN_MODE'] ?? '-'; ?></span></td>
                        </tr>
                        <tr>
                            <td>Role / Protection</td>
                            <td class="text-end"><?= $db_status['DATABASE_ROLE'] ?? '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Log Diterapkan (Primary)</td>
                            <td class="text-end text-success"><?= $dataguard['LOG_APPLIED'] ?? '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Log Diterima (Secondary)</td>
                            <td class="text-end text-info"><?= $dataguard['LOG_RECEIVED'] ?? '-'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-shield-halved text-success me-2"></i> RMAN Backup</span>
                    <span class="text-muted mt-1 fw-bold fs-7">7 Hari Terakhir</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-8 gy-2">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Tipe</th>
                                <th>Status</th>
                                <th class="pe-4 text-end rounded-end">Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <?php if(!empty($rman_backup)): foreach ($rman_backup as $row): ?>
                            <tr>
                                <td class="ps-4"><?= $row['BACKUP_TYPE']; ?></td>
                                <td>
                                    <?php if(strtoupper($row['STATUS']) == 'COMPLETED') echo '<span class="badge bg-success">COMPLETED</span>'; 
                                          elseif(strtoupper($row['STATUS']) == 'FAILED') echo '<span class="badge bg-danger">FAILED</span>';
                                          else echo '<span class="badge bg-warning text-dark">'.$row['STATUS'].'</span>'; ?>
                                </td>
                                <td class="pe-4 text-end text-muted fs-8"><?= $row['END_TIME']; ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada aktivitas backup</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-gauge-high text-warning me-2"></i> Resource Limit</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Sesi & Proses Aktif</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-8 gy-2">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Resource</th>
                                <th class="text-center">Digunakan</th>
                                <th class="pe-4 text-end rounded-end">Limit</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <?php if(!empty($resource_limit)): foreach ($resource_limit as $row): ?>
                            <tr>
                                <td class="ps-4"><?= strtoupper($row['RESOURCE_NAME']); ?></td>
                                <td class="text-center text-primary"><?= $row['CURRENT_UTILIZATION']; ?></td>
                                <td class="pe-4 text-end"><?= $row['LIMIT_VALUE']; ?></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="col-xl-8">

        
        
        <!-- MODUL 1: Tren Performa (AWR) - UX DIPERBARUI -->
        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5 border-bottom-0">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <i class="fa-solid fa-chart-area text-success me-2"></i> Tren Performa Sistem (AWR)
                    </span>
                    <span class="text-muted mt-1 fw-bold fs-7">Analisis 7 Hari Terakhir (Detail per Menit)</span>
                </h3>
                
                <!-- Navigasi Tabs UX -->
                <div class="card-toolbar">
                    <ul class="nav nav-pills nav-pills-custom d-flex justify-content-end" role="tablist">
                        <li class="nav-item mb-3 me-3 me-lg-4">
                            <a class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active-light-primary active d-flex flex-column flex-center py-2 px-4" data-bs-toggle="tab" href="#tab_kinerja">
                                <span class="fs-7 fw-bolder">Kinerja Server</span>
                                <span class="fs-9">CPU & Sesi Aktif</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active-light-danger d-flex flex-column flex-center py-2 px-4" data-bs-toggle="tab" href="#tab_transaksi">
                                <span class="fs-7 fw-bolder">Beban Transaksi</span>
                                <span class="fs-9">I/O & Commits</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body pt-2 pb-5">
                <div class="tab-content">
                    <!-- Tab 1: Kinerja Server -->
                    <div class="tab-pane fade show active" id="tab_kinerja" role="tabpanel">
                        <div style="position: relative; height: 350px; width: 100%;">
                            <canvas id="awrKinerjaChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Tab 2: Beban Transaksi -->
                    <div class="tab-pane fade" id="tab_transaksi" role="tabpanel">
                        <div style="position: relative; height: 350px; width: 100%;">
                            <canvas id="awrTransaksiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            // =================================================================
            // LOGIKA ANALISIS OTOMATIS AWR (7 HARI TERAKHIR)
            // =================================================================
            $max_cpu = 0; $waktu_cpu = ''; $avg_cpu_total = 0; $count_cpu = 0;
            $max_io = 0;  $waktu_io = '';
            $max_sesi = 0; $waktu_sesi = '';
            $max_commit = 0; $waktu_commit = '';

            if(!empty($awr_data)) {
                foreach($awr_data as $row) {
                    // Amankan pembacaan key array (huruf besar/kecil)
                    $metrik = strtoupper($row['METRIK'] ?? $row['metrik']);
                    $max_val = floatval($row['NILAI_MAKSIMAL'] ?? $row['nilai_maksimal']);
                    $avg_val = floatval($row['NILAI_RATA_RATA'] ?? $row['nilai_rata_rata']);
                    $waktu = $row['WAKTU'] ?? $row['waktu'];

                    // Ekstrak Data CPU
                    if(strpos($metrik, 'CPU') !== false) {
                        if($max_val > $max_cpu) { $max_cpu = $max_val; $waktu_cpu = $waktu; }
                        $avg_cpu_total += $avg_val;
                        $count_cpu++;
                    }
                    // Ekstrak Data I/O
                    if(strpos($metrik, 'I/O') !== false) {
                        if($max_val > $max_io) { $max_io = $max_val; $waktu_io = $waktu; }
                    }
                    // Ekstrak Data Sessions
                    if(strpos($metrik, 'SESSIONS') !== false) {
                        if($max_val > $max_sesi) { $max_sesi = $max_val; $waktu_sesi = $waktu; }
                    }
                    // Ekstrak Data Commits
                    if(strpos($metrik, 'COMMITS') !== false) {
                        if($max_val > $max_commit) { $max_commit = $max_val; $waktu_commit = $waktu; }
                    }
                }
            }
            
            // Hitung rata-rata CPU mingguan
            $avg_cpu_final = $count_cpu > 0 ? round($avg_cpu_total / $count_cpu, 2) : 0;
            
            // Tentukan Status Kesehatan Server berdasarkan CPU
            $status_kesehatan = 'Normal';
            $warna_status = 'success';
            if ($max_cpu > 90) { $status_kesehatan = 'Kritis'; $warna_status = 'danger'; }
            elseif ($max_cpu > 75) { $status_kesehatan = 'Peringatan'; $warna_status = 'warning'; }
        ?>

        <!-- ================= MODUL 1.5: KESIMPULAN ANALISIS AWR ================= -->
        <div class="card card-flush bg-light-<?= $warna_status; ?> mb-5 mb-xl-8 border border-<?= $warna_status; ?>">
            <div class="card-body py-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="fs-1 me-3 text-<?= $warna_status; ?>">
                        <i class="fa-solid <?= $status_kesehatan == 'Kritis' ? 'fa-triangle-exclamation' : ($status_kesehatan == 'Peringatan' ? 'fa-bell' : 'fa-check-circle'); ?>"></i>
                    </span>
                    <div>
                        <h4 class="text-<?= $warna_status; ?> fw-bolder mb-0">Analisis Otomatis: Status <?= $status_kesehatan; ?></h4>
                        <span class="text-muted fs-8 fw-bold">Berdasarkan pemantauan 7 hari terakhir</span>
                    </div>
                </div>
                
                <div class="fs-6 text-gray-700 fw-medium" style="line-height: 1.6;">
                    Selama satu minggu terakhir, rata-rata penggunaan CPU berjalan stabil di kisaran <strong><?= $avg_cpu_final; ?>%</strong>. 
                    Namun, tercatat lonjakan beban kerja tertinggi (Peak Load) terjadi pada <strong><?= $waktu_cpu; ?></strong>, 
                    di mana utilitas CPU menyentuh angka <strong><?= $max_cpu; ?>%</strong> dengan jumlah sesi aktif mencapai <strong><?= $max_sesi; ?> sesi</strong> (puncak sesi pada <?= $waktu_sesi; ?>).
                    <br><br>
                    Dari sisi lalu lintas data (transaksi), beban baca/tulis disk (I/O) mencapai titik tertingginya pada <strong><?= $waktu_io; ?></strong> 
                    dengan kecepatan <strong><?= $max_io; ?> Requests/Sec</strong>. Aktivitas komit data <em>(User Commits)</em> tertinggi tercatat pada 
                    <strong><?= $waktu_commit; ?></strong> sebesar <strong><?= $max_commit; ?> Commits/Sec</strong>. 
                    
                    <?php if($max_cpu > 85): ?>
                    <div class="alert alert-danger mt-3 mb-0 p-3 fs-7 border-danger d-flex align-items-center">
                        <i class="fa-solid fa-bolt text-danger me-2 fs-4"></i>
                        <span><strong>Rekomendasi Tindakan:</strong> Ditemukan lonjakan CPU di atas batas aman (85%). Disarankan untuk memeriksa kueri berat pada Top SQL di sekitar waktu <strong><?= $waktu_cpu; ?></strong> untuk mendeteksi *bottleneck*.</span>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success mt-3 mb-0 p-3 fs-7 border-success d-flex align-items-center">
                        <i class="fa-solid fa-shield-check text-success me-2 fs-4"></i>
                        <span><strong>Kapasitas Aman:</strong> Server berjalan dengan sangat baik dan memiliki ruang komputasi yang cukup untuk menangani beban transaksi saat ini.</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Penggunaan FRA</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Distribusi Flash Recovery Area</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="mb-6 pb-5 border-bottom" style="position: relative; height: 230px; width: 100%;">
                    <canvas id="fraDonutChart"></canvas>
                </div>
                
                <div class="table-responsive mt-3">
                    <table class="table align-middle table-row-dashed fs-8 gy-2 datatable">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Tipe File</th>
                                <th width="35%">Terpakai (%)</th>
                                <th>Reclaimable</th>
                                <th class="pe-4 text-end rounded-end">Jml File</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <?php if(!empty($fra_usage)): foreach ($fra_usage as $row): 
                                if ($row['PERCENT_SPACE_USED'] == 0) continue; 
                            ?>
                            <tr>
                                <td class="ps-4"><i class="fa-regular fa-file-code me-2 text-muted"></i> <?= $row['FILE_TYPE']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-3 fs-7 <?= $row['PERCENT_SPACE_USED'] > 80 ? 'text-danger' : 'text-primary'; ?>">
                                            <?= $row['PERCENT_SPACE_USED']; ?>%
                                        </span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar <?= $row['PERCENT_SPACE_USED'] > 80 ? 'bg-danger' : 'bg-primary'; ?>" 
                                                 role="progressbar" style="width: <?= $row['PERCENT_SPACE_USED']; ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $row['PERCENT_SPACE_RECLAIMABLE']; ?>%</td>
                                <td class="pe-4 text-end"><?= $row['NUMBER_OF_FILES']; ?></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODUL 3: Kapasitas Tablespace -->
        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-database text-info me-2"></i> Kapasitas Tablespace</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Penggunaan Berjalan vs Maksimal Kapasitas</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table align-middle table-row-dashed fs-8 gy-2 datatable">
                        <thead class="align-middle" style="position: sticky; top: 0; z-index: 1;">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">Nama Tablespace</th>
                                <th class="text-end">Terpakai (MB)</th>
                                <th class="text-end">Dialokasikan (MB)</th>
                                <th class="text-end">Maksimal (MB)</th>
                                <th class="pe-4 text-end rounded-end" width="25%">Status (%)</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <?php if(!empty($tablespace_usage)): foreach ($tablespace_usage as $row): 
                                if ($row['PERCENT_USED'] == 0) continue; 
                                $is_critical = $row['PERCENT_USED'] > 85;
                            ?>
                            <tr class="<?= $is_critical ? 'bg-light-danger' : ''; ?>">
                                <td class="ps-4 text-dark"><i class="fa-solid fa-hard-drive me-2 text-muted"></i> <?= $row['TABLESPACE_NAME']; ?></td>
                                <td class="text-end text-primary"><?= number_format($row['USED_MB'], 2, ',', '.'); ?></td>
                                <td class="text-end"><?= number_format($row['ALLOCATED_MB'], 2, ',', '.'); ?></td>
                                <td class="text-end fw-bolder text-dark"><?= number_format($row['MAX_MB'], 2, ',', '.'); ?></td>
                                <td class="pe-4">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="me-3 fs-7 <?= $is_critical ? 'text-danger' : 'text-success'; ?>">
                                            <?= $row['PERCENT_USED']; ?>%
                                        </span>
                                        <div class="progress w-50" style="height: 6px;">
                                            <div class="progress-bar <?= $is_critical ? 'bg-danger' : 'bg-success'; ?>" 
                                                 role="progressbar" style="width: <?= $row['PERCENT_USED']; ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><i class="fa-solid fa-code text-danger me-2"></i> Top SQL (Kueri Aktif)</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Aktivitas Sesi 2 Jam Terakhir</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table align-middle table-row-dashed fs-8 gy-2 datatable-large">
                        <thead class="align-middle" style="position: sticky; top: 0; z-index: 2;">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">User / Mesin</th>
                                <th>State & Event</th>
                                <th class="text-center">Samples</th>
                                <th class="pe-4 rounded-end" width="50%">SQL Text</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <?php if(!empty($top_sql)): foreach ($top_sql as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="text-primary fs-7 mb-1"><?= $row['USERNAME']; ?></div>
                                    <div class="text-muted fw-normal"><i class="fa-solid fa-desktop me-1"></i> <?= $row['MACHINE']; ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-dark mb-1"><?= $row['SESSION_STATE']; ?></span>
                                    <div class="text-muted fw-normal fs-9"><i class="fa-solid fa-clock-rotate-left me-1"></i> <?= $row['EVENT']; ?></div>
                                </td>
                                <td class="text-center fs-6 text-dark"><?= $row['TOTAL_SAMPLES']; ?></td>
                                <td class="pe-4">
                                    <textarea class="form-control form-control-sm fs-9" rows="3" readonly style="font-family: monospace; background:#f5f8fa; border:none; resize:none;"><?= htmlspecialchars($row['SQL_TEXT']); ?></textarea>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var btnCetak = document.getElementById('btnCetakPdf');
        var selectPeriode = document.getElementById('selectperiode');

        if (btnCetak && selectPeriode) {
            btnCetak.addEventListener('click', function() {
                // 1. Ambil nilai bulan/tahun yang sedang dipilih di dropdown
                var periodeTerpilih = selectPeriode.value; 
                
                // 2. Gabungkan URL controller dengan parameter periode tersebut
                var urlCetak = "<?= base_url('recoveryspace/cetak_laporan_bulanan/'); ?>" + periodeTerpilih;
                
                // 3. Buka tab baru untuk proses render PDF
                window.open(urlCetak, '_blank');
            });
        }
    });


    document.addEventListener("DOMContentLoaded", function() {
        
        // --- 1. INISIALISASI CHART.JS (FRA DONUT CHART) ---
        if(document.getElementById('fraDonutChart')) {
            const ctx = document.getElementById('fraDonutChart').getContext('2d');
            const fraLabels = <?= json_encode($chart_labels ?? []); ?>;
            const fraData = <?= json_encode($chart_data ?? []); ?>;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: fraLabels,
                    datasets: [{
                        data: fraData,
                        backgroundColor: ['#009ef7', '#f1416c', '#ffc700', '#50cd89', '#7239ea', '#e4e6ef'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: {
                        legend: { position: 'right', labels: { usePointStyle: true, padding: 20, font: { size: 11, family: 'inherit' } } },
                        tooltip: { callbacks: { label: function(context) { return ' ' + context.label + ': ' + context.parsed + '%'; } } }
                    }
                }
            });
        }

        // --- 1.5 INISIALISASI GRAFIK AWR (UX TABS & THRESHOLD LIMIT) ---
        if (document.getElementById('awrKinerjaChart') && document.getElementById('awrTransaksiChart')) {
            const ctxKinerja = document.getElementById('awrKinerjaChart').getContext('2d');
            const ctxTransaksi = document.getElementById('awrTransaksiChart').getContext('2d');
            const awrRawData = <?= json_encode($awr_data ?? []); ?>;
            
            if (awrRawData.length > 0) {
                const labels = [...new Set(awrRawData.map(item => item.WAKTU || item.waktu))];

                const getMetricAvg = (metricName) => {
                    return labels.map(label => {
                        const row = awrRawData.find(x => (x.WAKTU === label || x.waktu === label) && (x.METRIK === metricName || x.metrik === metricName));
                        return row ? parseFloat(row.NILAI_RATA_RATA || row.nilai_rata_rata || 0) : 0;
                    });
                };

                const getMetricMax = (metricName) => {
                    return labels.map(label => {
                        const row = awrRawData.find(x => (x.WAKTU === label || x.waktu === label) && (x.METRIK === metricName || x.metrik === metricName));
                        return row ? parseFloat(row.NILAI_MAKSIMAL || row.nilai_maksimal || 0) : 0;
                    });
                };

                // =========================================================
                // PENGATURAN ANGKA BATAS KRITIS (THRESHOLD)
                // Silakan ubah angka di bawah ini sesuai standar sistem Anda
                // =========================================================
                const limitCPU = 85;   // Batas CPU 85%
                const limitIO  = 3000; // Batas I/O 1000 Requests per Second
                // =========================================================

                const commonOptions = {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, padding: 15, font: { size: 12 } } },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)', titleColor: '#181c32', bodyColor: '#5e6278',
                            borderColor: 'rgba(0,0,0,0.1)', borderWidth: 1, padding: 12, boxPadding: 6,
                            usePointStyle: true, titleFont: { size: 14, weight: 'bold' }, bodyFont: { size: 12 },
                            callbacks: {
                                title: function(tooltipItems) {
                                    const parts = tooltipItems[0].label.split(' ');
                                    return parts.length === 2 ? `🗓️ ${parts[0]}   ⏱️ ${parts[1]}` : tooltipItems[0].label;
                                },
                                label: function(context) {
                                    // Sembunyikan tulisan 'Max' untuk garis batas karena angkanya konstan
                                    if(context.dataset.label.includes('Batas Kritis')) {
                                        return ` ⚠️ ${context.dataset.label}: ${context.parsed.y}`;
                                    }
                                    const maxVal = context.dataset.maxData[context.dataIndex]; 
                                    return ` ${context.dataset.label}: Avg ${context.parsed.y} | Max ${maxVal}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false }, 
                            ticks: { 
                                maxTicksLimit: 12, color: '#a1a5b7', font: { size: 11 },
                                callback: function(value) {
                                    const label = labels[value];
                                    if(!label) return '';
                                    const parts = label.split(' ');
                                    return parts.length === 2 ? `${parts[0].split('-')[2]}/${parts[0].split('-')[1]} ${parts[1]}` : label;
                                }
                            } 
                        }
                    }
                };

                // 1. GRAFIK TAB 1 (Kinerja Server)
                let optionsKinerja = JSON.parse(JSON.stringify(commonOptions));
                optionsKinerja.scales.y = { type: 'linear', position: 'left', title: { display: true, text: 'CPU (%)', color: '#009ef7' }, max: 100 }; // Kunci max CPU di 100%
                optionsKinerja.scales.y1 = { type: 'linear', position: 'right', title: { display: true, text: 'Sesi Aktif', color: '#50cd89' }, grid: { drawOnChartArea: false } };

                new Chart(ctxKinerja, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'CPU Utilization (%)', data: getMetricAvg('Host CPU Utilization (%)'), maxData: getMetricMax('Host CPU Utilization (%)'),
                                borderColor: '#009ef7', backgroundColor: 'rgba(0, 158, 247, 0.1)', yAxisID: 'y', tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 6, fill: true
                            },
                            {
                                label: 'Active Sessions', data: getMetricAvg('Average Active Sessions'), maxData: getMetricMax('Average Active Sessions'),
                                borderColor: '#50cd89', backgroundColor: 'transparent', yAxisID: 'y1', tension: 0.4, borderWidth: 2, borderDash: [5, 5], pointRadius: 0, pointHoverRadius: 6
                            },
                            // --- DATASET GARIS BATAS CPU ---
                            {
                                label: 'Batas Kritis CPU',
                                data: labels.map(() => limitCPU), // Bikin data datar sepanjang sumbu X
                                maxData: labels.map(() => limitCPU),
                                borderColor: '#f1416c', // Warna Merah Alert
                                backgroundColor: 'transparent',
                                yAxisID: 'y',
                                borderWidth: 2,
                                borderDash: [10, 5], // Garis putus-putus
                                pointRadius: 0,
                                pointHoverRadius: 0, // Jangan munculkan titik saat di-hover
                                fill: false
                            }
                        ]
                    },
                    options: optionsKinerja
                });

                // 2. GRAFIK TAB 2 (Beban Transaksi)
                let optionsTransaksi = JSON.parse(JSON.stringify(commonOptions));
                optionsTransaksi.scales.y = { type: 'linear', position: 'left', title: { display: true, text: 'I/O Requests / Sec', color: '#009ef7' } };
                optionsTransaksi.scales.y1 = { type: 'linear', position: 'right', title: { display: true, text: 'Commits / Sec', color: '#ffc700' }, grid: { drawOnChartArea: false } };

                new Chart(ctxTransaksi, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'I/O Requests / Sec', data: getMetricAvg('I/O Requests per Second'), maxData: getMetricMax('I/O Requests per Second'),
                                borderColor: '#009ef7', backgroundColor: 'rgba(0, 158, 247, 0.1)', yAxisID: 'y', tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 6, fill: true
                            },
                            {
                                label: 'User Commits / Sec', data: getMetricAvg('User Commits Per Sec'), maxData: getMetricMax('User Commits Per Sec'),
                                borderColor: '#ffc700', backgroundColor: 'transparent', yAxisID: 'y1', tension: 0.4, borderWidth: 2, borderDash: [5, 5], pointRadius: 0, pointHoverRadius: 6
                            },
                            // --- DATASET GARIS BATAS I/O ---
                            {
                                label: 'Batas Kritis I/O',
                                data: labels.map(() => limitIO), 
                                maxData: labels.map(() => limitIO),
                                borderColor: '#f1416c', // Warna Merah Alert
                                backgroundColor: 'transparent',
                                yAxisID: 'y',
                                borderWidth: 2,
                                borderDash: [10, 5],
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                fill: false
                            }
                        ]
                    },
                    options: optionsTransaksi
                });
            }
        }
        
        // --- 2. INISIALISASI DATATABLES ---
        if (typeof $.fn.DataTable !== 'undefined') {
            const dtLanguage = {
                "search": "Cari:", "lengthMenu": "_MENU_ baris", "info": "Menampilkan _START_-_END_ dari _TOTAL_",
                "infoEmpty": "Data kosong", "zeroRecords": "Tidak ditemukan",
                "paginate": { "previous": "Sebelumnya", "next": "Selanjutnya" }
            };

            $('.datatable').DataTable({
                "pageLength": 5, "lengthChange": false, "language": dtLanguage,
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });

            $('.datatable-large').DataTable({
                "pageLength": 5, "lengthMenu": [[5, 10, 25], [5, 10, 25]],
                "language": dtLanguage, "order": [[2, "desc"]]
            });
        }

        // Script untuk Jam Real-Time
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            
            const dayName = days[now.getDay()];
            const day = String(now.getDate()).padStart(2, '0');
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            if(document.getElementById('live-date') && document.getElementById('live-time')) {
                document.getElementById('live-date').textContent = `${dayName}, ${day} ${month} ${year}`;
                document.getElementById('live-time').textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>