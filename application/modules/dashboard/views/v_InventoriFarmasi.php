<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>Dashboard Inventori Farmasi</h1>
                        <p class="mb-0">Monitoring gabungan stok obat dari seluruh depo secara Real-Time.</p>
                    </div>
                </div>   
                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs border-transparent fs-6 fw-bold flex-nowrap">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Posisi Stok</a>
                        </li>
                        <!-- Tambahkan baris di bawah ini -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab2">Detail Per Depo</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>   



<div class="tab-content mt-5">
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        
        <?php $total_data = !empty($stok_farmasi) ? count($stok_farmasi) : 0; ?>
            
        <div class="card border border-primary border-dashed mb-10 shadow-sm hover-elevate-up" style="transition: all 0.3s ease;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light-info px-6 py-8 rounded-start d-flex flex-column justify-content-center align-items-center text-center border-end border-info border-dashed">
                        <span class="svg-icon svg-icon-4hx svg-icon-info mb-4 fa-fade">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="black"></path>
                                <path d="M21 7H3C2.4 7 2 7.4 2 8V18C2 18.6 2.4 19 3 19H21C21.6 19 22 18.6 22 18V8C22 7.4 21.6 7 21 7ZM12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12C16 14.2 14.2 16 12 16Z" fill="black"></path>
                            </svg>  
                        </span>
                        <h3 class="fs-1 text-info fw-bolder mb-1"><?= number_format($total_data, 0, ',', '.') ?> Item Obat</h3>
                        <span class="fs-6 fw-bold text-gray-600">Total Macam Obat Aktif</span>
                    </div>

                    <div class="col-md-8 bg-light-primary px-6 py-8 rounded-end d-flex align-items-center">
                        <span class="svg-icon svg-icon-3hx svg-icon-primary me-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19.93C7.05 19.43 4 16.05 4 12C4 7.95 7.05 4.57 11 4.07V19.93ZM13 4.07C16.95 4.57 20 7.95 20 12C20 16.05 16.95 19.43 13 19.93V4.07Z" fill="black"/>
                                <circle cx="9" cy="10" r="1.5" fill="black"/>
                                <circle cx="15" cy="10" r="1.5" fill="black"/>
                                <path d="M12 16C13.66 16 15 14.66 15 13H9C9 14.66 10.34 16 12 16Z" fill="black"/>
                            </svg>
                        </span>
                        <div class="d-flex flex-column">
                            <h4 class="text-primary mb-3 d-flex align-items-center">
                                Analitik Inventori Pintar (AI)
                                <span class="badge badge-primary ms-3 fs-9 px-3 py-2 pulse pulse-white">
                                    <span class="pulse-ring"></span>
                                    <i class="ki-duotone ki-abstract-26 fs-7 text-white me-1"></i> Real-time
                                </span>
                            </h4>
                            <span class="text-dark fs-5 lh-lg fw-semibold" style="text-align: justify;">
                                <?= $smart_insight ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 mb-xl-8 shadow-sm">
            <div class="card-body py-6">
                <div class="d-flex flex-stack flex-wrap">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="fs-7 fw-bolder text-gray-700 pe-4 text-nowrap">Status Data :</span>
                            <span class="badge badge-light-success fs-6 fw-bold">Live (Real-Time)</span>
                        </div>
                        <a href="<?= site_url('dashboard/Inventorifarmasi/export_excel') ?>" class="btn btn-sm btn-success text-nowrap">
                            <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            
            <div class="col-xl-8">
                <div class="card card-flush shadow-sm h-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Daftar Stok Obat</span>
                        </h3>
                    </div>
                    <div class="card-body py-5">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3" id="kt_table_stok">
                                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                    <tr class="fw-bold text-muted bg-light text-center">
                                        <th class="ps-4 min-w-100px rounded-start">Kode Obat</th>
                                        <th class="min-w-250px text-start">Nama Obat</th>
                                        <th class="min-w-120px">Satuan</th>
                                        <th class="min-w-150px rounded-end">Total Stok (All Depo)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($stok_farmasi)): ?>
                                        <?php foreach($stok_farmasi as $row): ?>
                                            <tr class="text-center">
                                                <td class="ps-4 fw-bold text-dark"><?= $row['OBAT_ID'] ?></td>
                                                <td class="text-start">
                                                    <span class="text-gray-800 fw-bold"><?= $row['NAMA_OBAT'] ?></span>
                                                </td>
                                                <td><span class="badge badge-light-info"><?= $row['SATUAN'] ?? '-' ?></span></td>
                                                <td>
                                                    <?php if($row['TOTAL_STOK_KESELURUHAN'] <= 0): ?>
                                                        <span class="badge badge-light-danger fw-bold fs-6"><?= $row['TOTAL_STOK_KESELURUHAN'] ?></span>
                                                    <?php else: ?>
                                                        <span class="text-dark fw-bolder fs-5"><?= $row['TOTAL_STOK_KESELURUHAN'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-10 text-gray-500 fw-bold">Data stok tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card card-flush shadow-sm h-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Top 10 Volume Stok</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Item dengan Kuantitas Tertinggi</span>
                        </h3>
                    </div>
                    <div class="card-body py-5 d-flex flex-column justify-content-center">
                        
                        <div class="chart-container" style="position: relative; height:350px; width:100%;">
                            <canvas id="stokBarChart"></canvas>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- KONTEN TAB 2 (DETAIL PER DEPO INTERAKTIF) -->
    <div class="tab-pane fade" id="tab2" role="tabpanel">
        
        <?php 
            // 1. Grouping Data berdasarkan LOKASI_ID menggunakan PHP
            $grouped_depo = [];
            if(!empty($stok_per_depo)){
                foreach($stok_per_depo as $row) {
                    $grouped_depo[$row['LOKASI_ID']][] = $row;
                }
            }
        ?>

        <div class="row gy-5 g-xl-8 mt-2">
            
            <!-- KOLOM KIRI: DAFTAR DEPO (Bentuk Kotak Interaktif) -->
            <div class="col-xl-3">
                <div class="card shadow-sm">
                    <div class="card-header pt-4 pb-2 min-h-50px">
                        <h3 class="card-title fw-bolder fs-4 text-dark">Pilih Lokasi Depo</h3>
                    </div>
                    <div class="card-body p-4" style="max-height: 600px; overflow-y: auto;">
                        <ul class="nav nav-pills flex-column" role="tablist">
                            <?php 
                                // 1. Definisikan array warna tema Metronic (Warna-warna ini akan menjadi pastel jika ditambah prefix bg-light-)
                                $pastel_themes = ['primary', 'success', 'warning', 'info', 'danger'];
                                $theme_index = 0;
                                $is_first = true; 
                                
                                foreach($grouped_depo as $lokasi_id => $items): 
                                    $active_class = $is_first ? 'active' : '';
                                    
                                    // 2. Pilih warna secara bergantian berdasarkan urutan depo
                                    $theme = $pastel_themes[$theme_index % count($pastel_themes)];
                                    $theme_index++;
                            ?>
                                <li class="nav-item mb-4">
                                    <!-- 3. Tambahkan class bg-light-<?= $theme ?> dan border-<?= $theme ?> agar kotak berwarna pastel -->
                                    <a class="nav-link btn btn-outline btn-outline-dashed border-<?= $theme ?> btn-color-dark btn-active-light-<?= $theme ?> bg-light-<?= $theme ?> d-flex flex-stack text-start p-4 <?= $active_class ?> hover-elevate-up" 
                                    data-bs-toggle="pill" 
                                    href="#depo_<?= preg_replace('/[^A-Za-z0-9\-]/', '', $lokasi_id) ?>" 
                                    role="tab" 
                                    style="transition: all 0.3s ease; border-radius: 12px; border-width: 2px;">
                                    
                                        <div class="d-flex align-items-center">
                                            <!-- Ikon dengan warna solid senada agar kontras dengan background pastel -->
                                            <div class="symbol symbol-50px symbol-circle me-4 shadow-sm">
                                                <span class="symbol-label bg-<?= $theme ?>">
                                                    <i class="fas fa-pills fs-2 text-white"></i>
                                                </span>
                                            </div>
                                            
                                            <div class="d-flex flex-column justify-content-center">
                                                <div class="fs-5 fw-bolder text-dark mb-1"><?= $lokasi_id ?></div>
                                                
                                                <!-- Badge jumlah item menggunakan warna pastel juga -->
                                                <div class="fs-7 fw-bold text-gray-600 d-flex align-items-center mt-1">
                                                    <i class="ki-duotone ki-abstract-26 fs-7 me-1 text-<?= $theme ?>"><span class="path1"></span><span class="path2"></span></i>
                                                    <span class="badge badge-light-<?= $theme ?> border border-<?= $theme ?> px-2 py-1 text-dark shadow-sm">
                                                        <?= count($items) ?> Item Obat
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Indikator Panah -->
                                        <div class="d-none d-md-block ms-2">
                                            <div class="btn btn-icon btn-sm btn-active-color-<?= $theme ?>">
                                                <i class="ki-duotone ki-arrow-right fs-1 text-<?= $theme ?>"><span class="path1"></span><span class="path2"></span></i>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php 
                                $is_first = false; 
                                endforeach; 
                            ?>
                            
                            <?php if(empty($grouped_depo)): ?>
                                <div class="text-center text-muted py-5">Belum ada depo.</div>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

           <!-- KOLOM KANAN: TABEL RINCIAN PER DEPO (Bentuk Card) -->
            <div class="col-xl-9">
                <div class="tab-content" id="depoTabContent">
                    <?php 
                        $is_first_tab = true;
                        foreach($grouped_depo as $lokasi_id => $items): 
                            $active_tab = $is_first_tab ? 'show active' : '';
                            $safe_id = preg_replace('/[^A-Za-z0-9\-]/', '', $lokasi_id);
                    ?>
                        <div class="tab-pane fade <?= $active_tab ?>" id="depo_<?= $safe_id ?>" role="tabpanel">
                            <div class="card shadow-sm h-100">
                                <div class="card-header pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-3 mb-1">Stok Gudang: <span class="text-primary"><?= $lokasi_id ?></span></span>
                                        <span class="text-muted mt-1 fw-bold fs-7">Rincian pergerakan stok aktif saat ini</span>
                                    </h3>
                                </div>
                                
                                <!-- BAGIAN INI YANG DITAMBAHKAN SCROLL -->
                                <div class="card-body py-5" style="max-height: 600px; overflow-y: auto;">
                                    
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3 table-depo-detail" style="width:100%">
                                            <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                                <tr class="fw-bold text-muted bg-light text-center">
                                                    <th class="ps-4 min-w-100px rounded-start">Kode Obat</th>
                                                    <th class="min-w-250px text-start">Nama Obat</th>
                                                    <th class="min-w-100px">Satuan</th>
                                                    <th class="min-w-150px rounded-end">Stok Akhir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($items as $row_depo): ?>
                                                    <tr class="text-center">
                                                        <td class="ps-4 fw-bold text-dark"><?= $row_depo['OBAT_ID'] ?></td>
                                                        <td class="text-start">
                                                            <span class="text-gray-800 fw-bold"><?= $row_depo['NAMA_OBAT'] ?></span>
                                                        </td>
                                                        <td><span class="badge badge-light-info"><?= $row_depo['SATUAN'] ?? '-' ?></span></td>
                                                        <td>
                                                            <?php if($row_depo['STOK_AKHIR'] <= 0): ?>
                                                                <span class="badge badge-light-danger fw-bold fs-6"><?= $row_depo['STOK_AKHIR'] ?></span>
                                                            <?php else: ?>
                                                                <span class="text-dark fw-bolder fs-5"><?= $row_depo['STOK_AKHIR'] ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $is_first_tab = false;
                        endforeach; 
                    ?>
                </div>
            </div>

        </div>
    </div>
    <!-- AKHIR KONTEN TAB 2 -->
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Inisialisasi DataTable
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable('#kt_table_stok')) {
            $('#kt_table_stok').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 20, 50, 100],
                "order": [[3, "desc"]], // Sort default by Total Stok (Terbesar)
                "language": {
                    "emptyTable": "Belum ada data obat"
                }
            });
        }
    }

    // 2. Inisialisasi Chart.js (Bar Chart untuk Top 10 Stok)
    const canvas = document.getElementById('stokBarChart');
    if(canvas) {
        const ctx = canvas.getContext('2d');
        
        // Data dari Controller
        const labels = JSON.parse('<?= isset($chart_labels) ? $chart_labels : "[]" ?>');
        const stokData = JSON.parse('<?= isset($chart_values) ? $chart_values : "[]" ?>');
        
        // Memotong text label panjang agar rapi di chart
        const truncatedLabels = labels.map(label => label.length > 15 ? label.substring(0, 15) + '...' : label);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: truncatedLabels,
                datasets: [
                    {
                        label: 'Kuantitas Stok',
                        data: stokData,
                        backgroundColor: '#3699FF', // Warna primary template
                        borderRadius: 4,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Chart dibuat horizontal agar nama obat mudah terbaca
                plugins: { 
                    legend: { display: false }, // Sembunyikan legend karena hanya 1 warna
                    tooltip: {
                        backgroundColor: '#20D489', 
                        titleFont: { size: 14, family: 'Poppins' },
                        bodyFont: { size: 13, family: 'Poppins' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            // Menampilkan nama asli lengkap pada saat di-hover
                            title: function(context) {
                                return labels[context[0].dataIndex];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#e4e6ef', borderDash: [5, 5] }
                    },
                    y: { 
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Poppins', size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 3. Inisialisasi DataTable untuk Detail Per Depo
    // Inisialisasi DataTables untuk semua tabel di dalam Tab Depo
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        
        // Inisialisasi tabel menggunakan class (.table-depo-detail) karena id harus unik
        var tableDepo = $('.table-depo-detail').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 20, 50],
            "order": [[1, "asc"]], // Urutkan berdasarkan Nama Obat
            "language": {
                "emptyTable": "Data stok kosong untuk depo ini."
            }
        });

        // Event Listener: Sesuaikan ulang lebar kolom DataTables setiap kali Tab Depo di-klik
        // Ini mencegah bug UI di mana kolom header menyusut saat dirender di tab yang sedang tertutup (display: none)
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
        
        // Event Listener: Sesuaikan juga saat Tab Utama (Detail Per Depo) diklik pertama kali
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href"); // Activated tab
            if(target === '#tab2') {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
    }
});
</script>