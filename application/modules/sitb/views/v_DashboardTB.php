<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>Laporan Lab Tuberkulosis(TB)</h1>
                        <p class="mb-0">Monitoring Tuberkulosis (TB) guna mendukung peningkatan mutu dan kualitas pelayanan kesehatan.</p>
                    </div>
                </div>   
                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs border-transparent fs-6 fw-bold flex-nowrap">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Laporan TB</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>   

<div class="tab-content mt-5">
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        
        <?php $total_data = !empty($pasien_tb) ? count($pasien_tb) : 0; ?>
            
        <div class="card border border-primary border-dashed mb-10 shadow-sm hover-elevate-up" style="transition: all 0.3s ease;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light-info px-6 py-8 rounded-start d-flex flex-column justify-content-center align-items-center text-center border-end border-info border-dashed">
                        <span class="svg-icon svg-icon-4hx svg-icon-info mb-4 fa-fade">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="black"></path>
                                <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="black"></path>
                            </svg>  
                        </span>
                        <h3 class="fs-1 text-info fw-bolder mb-1"><?= $total_data ?> Pasien</h3>
                        <span class="fs-6 fw-bold text-gray-600">Total Terdeteksi Periode Ini</span>
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
                                AI Smart Insights
                                <span class="badge badge-primary ms-3 fs-9 px-3 py-2 pulse pulse-white">
                                    <span class="pulse-ring"></span>
                                    <i class="ki-duotone ki-abstract-26 fs-7 text-white me-1"></i> Auto-Generated
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
                    <form action="<?= site_url('sitb/DashboardTB') ?>" method="GET" id="formPeriode" class="d-flex align-items-center w-100 justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="fs-7 fw-bolder text-gray-700 pe-4 text-nowrap">Periode :</span>
                            <select data-control="select2" class="form-select form-select-sm form-select-solid w-150px" name="selectperiode" id="selectperiode" onchange="this.form.submit()">
                                <?php echo $periode; ?>
                            </select>
                        </div>
                        <a href="<?= site_url('sitb/DashboardTB/export_excel?selectperiode=' . $this->input->get('selectperiode')) ?>" class="btn btn-sm btn-success text-nowrap">
                            <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i> Export Excel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            
            <div class="col-xl-8">
                <div class="card card-flush shadow-sm h-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Daftar Pasien TB</span>
                        </h3>
                    </div>
                    <div class="card-body py-5">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3" id="kt_table_tb">
                                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                    <tr class="fw-bold text-muted bg-light text-center">
                                        <th class="ps-4 min-w-100px rounded-start">No RM</th>
                                        <th class="min-w-200px">Nama Pasien</th>
                                        <th class="min-w-120px">Tgl Register</th>
                                        <th class="min-w-120px">Hasil TCM</th>
                                        <th class="min-w-150px text-start">Diagnosa ICD</th>
                                        <th class="min-w-150px rounded-end">Paduan OAT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($pasien_tb)): ?>
                                        <?php foreach($pasien_tb as $row): ?>
                                            <tr class="text-center">
                                                <td class="ps-4 fw-bold text-dark"><?= $row['NO_RM'] ?></td>
                                                <td class="text-start">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-800 fw-bold"><?= $row['NAMA_PASIEN'] ?></span>
                                                        <span class="text-muted fs-7">NIK: <?= $row['NIK_KTP'] ?></span>
                                                    </div>
                                                </td>
                                                <td><?= date('d-m-Y', strtotime($row['TGL_REGISTER'])) ?></td>
                                                <td>
                                                    <?php if($row['HASIL_TCM']): ?>
                                                        <span class="badge badge-light-danger fw-bold"><?= $row['HASIL_TCM'] ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-7 italic">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-start">
                                                    <div class="fs-7 fw-bold"><?= $row['ICD_KODE'] ?></div>
                                                    <div class="text-muted fs-8"><?= $row['DIAGNOSA_CODING'] ?></div>
                                                </td>
                                                <td><span class="badge badge-light-primary"><?= $row['PADUAN_OAT'] ?? '-' ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-10 text-gray-500 fw-bold">Data tidak ditemukan untuk periode ini.</td>
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
                            <span class="card-label fw-bolder fs-3 mb-1">Tren & Proyeksi AI</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Pergerakan 6 Bulan Terakhir</span>
                        </h3>
                    </div>
                    <div class="card-body py-5 d-flex flex-column justify-content-between">
                        
                        <div class="chart-container" style="position: relative; height:250px; width:100%;">
                            <canvas id="tbTrendChart"></canvas>
                        </div>
                        
                        <div class="mt-5 p-4 bg-light-warning border border-warning border-dashed rounded text-center hover-elevate-up" style="transition: all 0.3s ease;">
                            <span class="fs-6 fw-bold text-gray-700">Proyeksi Pasien Bulan Depan</span>
                            <div class="fs-1 fw-bolder text-warning mt-2">
                                <?= isset($prediksi_ai) ? $prediksi_ai : 0 ?> Pasien
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Mengganti $(document).ready dengan event listener bawaan browser
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Inisialisasi DataTable (Tetap butuh jQuery, tapi lebih aman di dalam event ini)
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable('#kt_table_tb')) {
            $('#kt_table_tb').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 20, 50, 100],
                "order": [[2, "desc"]], 
                "language": {
                    "emptyTable": "Belum ada data pasien TB pada periode ini"
                }
            });
        }
    }

    // 2. Inisialisasi Chart.js untuk Prediksi AI
    const canvas = document.getElementById('tbTrendChart');
    if(canvas) {
        const ctx = canvas.getContext('2d');
        
        // Menerima data JSON dari Controller PHP
        // Gunakan JSON.parse() agar format string JSON dibaca sebagai array murni oleh JavaScript
        const labels = JSON.parse('<?= isset($chart_labels) ? $chart_labels : "[]" ?>');
        const historisData = JSON.parse('<?= isset($chart_values) ? $chart_values : "[]" ?>');
        const nilaiPrediksi = <?= isset($prediksi_ai) ? $prediksi_ai : 0 ?>;
        
        // Jika data dari database kosong, beri nilai default agar grafik tidak rusak
        if (labels.length === 0) {
            labels.push("Bulan Ini");
            historisData.push(0);
        }

        // Gabungkan data historis dengan nilai prediksi
        const proyeksiData = [...historisData, nilaiPrediksi];
        const finalLabels = [...labels, "Bulan Depan (AI)"];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: finalLabels,
                datasets: [
                    {
                        label: 'Pasien TB (Historis)',
                        data: historisData,
                        borderColor: '#3699FF', 
                        backgroundColor: 'rgba(54, 153, 255, 0.15)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3699FF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4 
                    }, 
                    {
                        label: 'Proyeksi (AI)',
                        data: proyeksiData,
                        borderColor: '#FFA800', 
                        borderWidth: 2,
                        borderDash: [6, 6], 
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#FFA800',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index', 
                    intersect: false,
                },
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: '#20D489', 
                        titleFont: { size: 14, family: 'Poppins' },
                        bodyFont: { size: 13, family: 'Poppins' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e4e6ef', borderDash: [5, 5] },
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });
    }
});
</script>