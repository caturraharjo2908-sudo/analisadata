<!-- Bagian Header & Navigasi Tab -->
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
        
        <!-- 1. Informasi Jumlah Data[cite: 11, 12] -->
        <?php $total_data = !empty($pasien_tb) ? count($pasien_tb) : 0; ?>
        <div class="alert alert-dismissible bg-light-info border border-info border-3 border-dashed d-flex flex-column flex-sm-row w-100 p-5 mb-10 fa-fade">
            <span class="svg-icon svg-icon-2hx svg-icon-info me-4 mb-5 mb-sm-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="black"></path>
                    <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="black"></path>
                </svg>  
            </span>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-info">Laporan Layanan Lap Turbekolosis(TB) Per Periode</h5>
                <div class="d-flex align-items-center">
                    <span class="text-gray-700 fw-bold">Total Pasien Terdeteksi: </span>
                    <span class="badge badge-info badge-lg ms-2 fw-bolder"><?= $total_data ?> Pasien</span>
                </div>
            </div>
        </div>
        
        <!-- 2. Filter & Toolbar[cite: 9, 10, 13] -->
        <div class="card mb-5 mb-xl-8">
            <div class="card-body py-6">
                <div class="d-flex flex-stack flex-wrap">
                    <form action="<?= site_url('sitb/DashboardTB') ?>" method="GET" id="formPeriode" class="d-flex align-items-center">
                        <span class="fs-7 fw-bolder text-gray-700 pe-4 text-nowrap">Periode :</span>
                        <select data-control="select2" class="form-select form-select-sm form-select-solid w-150px" name="selectperiode" id="selectperiode" onchange="this.form.submit()">
                            <?php echo $periode; ?>
                        </select>
                        <a href="<?= site_url('sitb/DashboardTB/export_excel?selectperiode=' . $this->input->get('selectperiode')) ?>" class="btn btn-sm btn-success ms-3 text-nowrap">
                            <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Export Excel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. Tabel Data (Hanya Satu Kali)[cite: 9, 13] -->
        <div class="card card-flush shadow-sm">
            <div class="card-body py-5">
                <!-- Kontainer Scroll[cite: 9] -->
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3" id="kt_table_tb">
                        <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                            <tr class="fw-bold text-muted bg-light text-center">
                                <th class="ps-4 min-w-100px rounded-start">No RM</th>
                                <th class="min-w-200px">Nama Pasien</th>
                                <th class="min-w-120px">Tgl Register</th>
                                <th class="min-w-150px">Hasil TCM</th>
                                <th class="min-w-200px text-start">Diagnosa ICD</th>
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
</div>

<!-- 4. Script Inisialisasi[cite: 9] -->
<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#kt_table_tb')) {
        $('#kt_table_tb').DataTable({
            "pageLength": 20, // Menampilkan 20 baris per halaman
            "lengthMenu": [10, 20, 50, 100],
            "order": [[2, "desc"]], // Urutkan berdasarkan Tgl Register
            "language": {
                "emptyTable": "Belum ada data pasien TB pada periode ini"
            }
        });
    }
});
</script>