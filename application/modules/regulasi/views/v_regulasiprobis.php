<style>
    #tabelContainer, #pdfContainer {
        transition: all 0.4s ease-in-out;
    }
    /* Memperhalus tampilan input agar menyatu tanpa garis tegas (border) saat aktif */
    #pencarianKustom:focus, .filter-dropdown:focus {
        box-shadow: none !important;
        outline: none !important;
    }
    /* UX interaktif saat hover di filter */
    .filter-dropdown:hover {
        color: #009ef7 !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f8fa !important;
        transition: background-color 0.2s;
    }
</style>

<?php 
    // =========================================================================
    // LOGIKA PHP: Mengekstrak Daftar Tahun Unik dari Database secara Otomatis
    // =========================================================================
    $tahun_unik = array();
    if(!empty($list_regulasi)){
        foreach($list_regulasi as $row){
            if(!empty($row->TANGGAL_BERLAKU)){
                // Menggunakan Regex untuk mencari angka tahun (20xx) dari format apapun
                if (preg_match('/\b(20\d{2})\b/', $row->TANGGAL_BERLAKU, $matches)) {
                    $tahun_unik[$matches[0]] = $matches[0];
                }
            }
        }
    }
    rsort($tahun_unik); // Mengurutkan dari tahun paling baru ke paling lama
?>

<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>E-Regulasi & Proses Bisnis</h1>
                        <p class="mb-0">Manajemen sentralisasi dokumen SK, SOP, dan Pedoman Pelayanan RSUD Pasar Minggu.</p>
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>

<div class="row" id="mainLayout">
    
    <div class="col-12" id="tabelContainer">
        <div class="card card-flush shadow-sm h-100">
            <div class="card-header pt-5 d-flex justify-content-between align-items-center">
                <h3 class="card-title align-items-start flex-column mb-0">
                    <span class="card-label fw-bolder fs-3">Daftar Dokumen Regulasi</span>
                </h3>
                
                <div class="card-toolbar d-flex flex-row flex-wrap gap-3 align-items-center">
                    
                    <!-- FILTER AREA YANG SUDAH DIBERSIHKAN -->
                    <div class="d-flex align-items-center gap-1 bg-light rounded-pill p-1 border">
                        <div class="ps-3 text-muted">
                            <i class="fas fa-filter fs-6"></i>
                        </div>

                        <select id="filterJenis" class="form-select form-select-sm bg-transparent border-0 text-muted cursor-pointer filter-dropdown fw-bold" style="width: 140px; padding-left: 8px;">
                            <option value="">Semua Dokumen</option>
                            <option value="SK DIREKTUR">SK Direktur</option>
                            <option value="STANDAR">SOP / SPO</option>
                            <option value="PEDOMAN">Pedoman</option>
                            <option value="INSTRUKSI KERJA">Instruksi Kerja</option>
                        </select>

                        <div class="vr bg-gray-400 my-2" style="width: 2px;"></div>

                        <select id="filterTahun" class="form-select form-select-sm bg-transparent border-0 text-muted cursor-pointer filter-dropdown fw-bold" style="width: 120px;">
                            <option value="">Semua Tahun</option>
                            <?php foreach($tahun_unik as $thn): ?>
                                <option value="<?= $thn; ?>"><?= $thn; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="input-group input-group-solid input-group-sm bg-light rounded-pill border" style="width: 200px; overflow: hidden;">
                        <span class="input-group-text bg-transparent border-0 pe-2" id="search-icon">
                            <i class="fas fa-search text-muted fs-5"></i>
                        </span>
                        <input type="text" id="pencarianKustom" class="form-control bg-transparent border-0 form-control-sm ps-0" placeholder="Ketik kata kunci..." aria-describedby="search-icon">
                    </div>

                    <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahRegulasi">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Unggah
                    </button>
                    
                </div>
            </div>
            
            <div class="card-body py-5">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3 w-100" id="tabelRegulasi">
                        <thead>
                            <tr class="fw-bold text-muted bg-light text-center">
                                <th class="ps-4 min-w-50px rounded-start">No</th>
                                <th class="min-w-150px text-start">Nomor & Judul</th>
                                <th class="min-w-150px">Jenis Regulasi</th>
                                <th class="min-w-150px">Departemen</th>
                                <th class="min-w-100px">Tgl Berlaku</th>
                                <th class="min-w-100px">Pengunggah</th>
                                <th class="min-w-100px rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($list_regulasi)): ?>
                                <?php $no = 1; foreach($list_regulasi as $row): ?>
                                <tr class="text-center">
                                    <td class="ps-4 fw-bold text-dark"><?= $no++; ?></td>
                                    
                                    <td class="text-start">
                                        <?php 
                                            // Mengamankan teks deskripsi dari karakter khusus agar tidak merusak HTML
                                            $teks_deskripsi = !empty($row->DESKRIPSI) ? htmlspecialchars($row->DESKRIPSI) : 'Tidak ada deskripsi singkat untuk dokumen ini.'; 
                                        ?>
                                        
                                        <!-- Menambahkan atribut title di wadah utama agar muncul saat disorot -->
                                        <div class="d-flex flex-column" title="Deskripsi: <?= $teks_deskripsi; ?>">
                                            
                                            <a href="javascript:void(0)" 
                                            onclick="bukaPDF('<?= base_url('index.php/regulasi/regulasiprobis/lihat_dokumen/'.$row->FILE_DOKUMEN); ?>', '<?= $row->JUDUL_DOKUMEN; ?>')" 
                                            class="text-primary fw-bolder fs-6 mb-1 text-hover-info text-decoration-underline">
                                                <?= $row->NOMOR_DOKUMEN; ?>
                                            </a>

                                            <span class="text-gray-800 fw-bold"><?= $row->JUDUL_DOKUMEN; ?></span>
                                            
                                            <!-- Cuplikan teks deskripsi di bawah judul (tetap dibiarkan agar tabel tidak kosong) -->
                                            <span class="text-muted fs-7 mt-1 text-truncate" style="max-width: 250px;">
                                                <?= !empty($row->DESKRIPSI) ? $row->DESKRIPSI : '-'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $jenis = strtoupper($row->JENIS_DOKUMEN);
                                            $badge_class = 'badge-light-secondary';
                                            if($jenis == 'SK DIREKTUR') $badge_class = 'badge-light-danger';
                                            if($jenis == 'STANDAR OPERASIONAL PROSEDUR' || $jenis == 'SPO') $badge_class = 'badge-light-success';
                                            if(strpos($jenis, 'PEDOMAN') !== false) $badge_class = 'badge-light-warning';
                                        ?>
                                        <span class="badge <?= $badge_class; ?> fw-bold px-4 py-2"><?= $jenis; ?></span>
                                    </td>
                                    
                                    <td>
                                        <?php $nama_bagian = !empty($row->BAGIAN) ? $row->BAGIAN : '-'; ?>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building text-gray-400 fs-6 me-2"></i>
                                            <span class="text-gray-700 fw-semibold text-truncate" style="max-width: 150px; display: inline-block;" title="<?= $nama_bagian; ?>">
                                                <?= $nama_bagian; ?>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            // Mengecek apakah tanggal kosong, jika tidak maka diformat menjadi d.m.Y
                                            $tgl_berlaku = !empty($row->TANGGAL_BERLAKU) ? date('d.m.Y', strtotime($row->TANGGAL_BERLAKU)) : '-'; 
                                        ?>
                                        <span class="text-dark fw-bold d-block"><?= $tgl_berlaku; ?></span>
                                    </td>
                                    
                                    <td>
                                        <span class="badge badge-light-primary fw-bold"><?= $row->CREATED_BY; ?></span>
                                    </td>
                                    
                                    <td>
                                        <button type="button" onclick="bukaPDF('<?= base_url('index.php/regulasi/regulasiprobis/lihat_dokumen/'.$row->FILE_DOKUMEN); ?>', '<?= $row->JUDUL_DOKUMEN; ?>')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Lihat PDF">
                                            <i class="fas fa-eye fs-4"></i>
                                        </button>
                                        <button type="button" onclick="konfirmasiHapus('<?= $row->ID_REGULASI; ?>', '<?= $row->NOMOR_DOKUMEN; ?>')" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Hapus Dokumen">
                                            <i class="fas fa-trash fs-4"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none" id="pdfContainer">
        <div class="card card-flush shadow-sm border border-primary border-dashed h-100">
            <div class="card-header pt-5 bg-light-primary min-h-80px">
                <h3 class="card-title">
                    <i class="fas fa-file-pdf text-danger me-3 fs-2"></i>
                    <span class="card-label fw-bolder fs-5 mb-0 text-truncate" id="judulDokumenViewer" style="max-width: 200px;">Pratinjau</span>
                </h3>
                <div class="card-toolbar d-flex gap-2">
                    <a href="#" id="btnDownloadPDF" download class="btn btn-sm btn-icon btn-light-success shadow-sm" title="Unduh Dokumen">
                        <i class="fas fa-download fs-4"></i>
                    </a>
                    <button type="button" onclick="cetakPDF()" class="btn btn-sm btn-icon btn-light-primary shadow-sm" title="Cetak Dokumen">
                        <i class="fas fa-print fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger shadow-sm ms-2" onclick="tutupPDF()" title="Tutup Pratinjau">
                        <i class="fas fa-times fs-3"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <iframe id="iframePDF" src="" width="100%" height="800px" style="border: none; background-color: #525659;"></iframe>
            </div>
        </div>
    </div>

</div>

<!-- MODAL FORM -->
<div class="modal fade" id="modalTambahRegulasi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="formRegulasi" class="form" action="<?= base_url('index.php/regulasi/regulasiprobis/simpan_regulasi'); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Registrasi Dokumen Baru</h1>
                        <div class="text-muted fw-bold fs-5">Pastikan file dalam format PDF dan maksimal 10MB</div>
                    </div>
                    
                    <!-- BARIS 1: Nomor dan Jenis Dokumen -->
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2"><span class="required">Nomor Dokumen</span></label>
                            <input type="text" class="form-control form-control-solid" name="nomor_dokumen" required placeholder="Contoh: SK/001/2026">
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2"><span class="required">Jenis Dokumen</span></label>
                            <select class="form-select form-select-solid" name="jenis_dokumen" required>
                                <option value="" selected disabled>-- Pilih Klasifikasi --</option>
                                <option value="SK DIREKTUR">SK Direktur</option>
                                <option value="STANDAR OPERASIONAL PROSEDUR">Standar Operasional Prosedur(SOP)</option>
                                <option value="PEDOMAN PENGORGANISASIAN">Pedoman Pengorganisasian</option>
                                <option value="PEDOMAN PELAYANAN">Pedoman Pelayanan</option>
                                <option value="INSTRUKSI KERJA">Instruksi Kerja</option>
                            </select>
                        </div>
                    </div>

                    <!-- BARIS 2: Status Riwayat dan Kata Kunci -->
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">Status Riwayat</label>
                            <select class="form-select form-select-solid" name="status_riwayat">
                                <option value="Berlaku" selected>Berlaku (Baru)</option>
                                <option value="Mencabut Regulasi Lama">Mencabut Regulasi Lama</option>
                                <option value="Mengubah Regulasi Lama">Mengubah Regulasi Lama</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">Kata Kunci (Tags)</label>
                            <input type="text" class="form-control form-control-solid" name="kata_kunci" placeholder="Contoh: rekam medis, PP 28 Tahun 2024">
                            <div class="form-text">Pisahkan dengan koma jika lebih dari satu.</div>
                        </div>
                    </div>

                    <!-- BARIS 3: Judul dan Deskripsi -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-bold mb-2"><span class="required">Judul Dokumen</span></label>
                        <input type="text" class="form-control form-control-solid" name="judul_dokumen" required placeholder="Contoh: SPO Penerimaan Pasien Baru">
                    </div>
                    <div class="d-flex flex-column mb-8">
                        <label class="fs-6 fw-bold mb-2"><span class="required">Deskripsi / Abstrak Singkat</span></label>
                        <textarea class="form-control form-control-solid" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat ruang lingkup regulasi ini..."></textarea>
                    </div>

                    <!-- BARIS 4: Tanggal dan File -->
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2"><span class="required">Tanggal Berlaku</span></label>
                            <input type="date" class="form-control form-control-solid" name="tanggal_berlaku" required>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2"><span class="required">Unggah File (PDF)</span></label>
                            <input type="file" class="form-control form-control-solid" name="file_dokumen" id="fileInput" accept=".pdf" required>
                        </div>
                    </div>
                    
                    <!-- TOMBOL SUBMIT -->
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batalkan</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">
                            <span class="indicator-label">Simpan Data & Unggah</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    $.fn.dataTable.ext.search = [];

    var desainDataKosong = `
        <div class="d-flex flex-column align-items-center justify-content-center p-10">
            <i class="fas fa-search text-muted mb-4 opacity-50" style="font-size: 5rem;"></i>
            <h4 class="text-gray-800 fw-bolder mb-2">Dokumen Tidak Ditemukan</h4>
            <p class="text-muted fs-6 text-center w-75">
                Data dengan filter atau kata kunci yang Anda masukkan tidak ada di sistem.
            </p>
        </div>
    `;

    var tabelDataRegulasi = $('#tabelRegulasi').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            emptyTable: desainDataKosong, 
            zeroRecords: desainDataKosong
        },
        destroy: true, 
        ordering: true,
        pageLength: 10,
        dom: "<'table-responsive'tr><'row align-items-center mt-4'<'col-sm-12 col-md-5 fs-7 text-muted'i><'col-sm-12 col-md-7'p>>",
        columnDefs: [ { orderable: false, targets: 6 } ] 
    });

    $('#pencarianKustom').on('keyup input paste', function () {
        tabelDataRegulasi.search(this.value).draw();
    });

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabelRegulasi') {
                return true;
            }
            var filterJenis = $('#filterJenis').val().toLowerCase();
            var filterTahun = $('#filterTahun').val();
            var teksJenis = (data[2] || "").replace(/<[^>]+>/g, '').toLowerCase(); 
            var teksTanggal = (data[4] || "").replace(/<[^>]+>/g, ''); 

            if (filterJenis !== "" && teksJenis.indexOf(filterJenis) === -1) {
                return false; 
            }
            if (filterTahun !== "" && teksTanggal.indexOf(filterTahun) === -1) {
                return false; 
            }
            return true; 
        }
    );

    $('#filterJenis, #filterTahun').on('change', function() {
        tabelDataRegulasi.draw(); 
        stylingFilterActive(this); 
    });

    function stylingFilterActive(element) {
        if ($(element).val() !== "") {
            $(element).removeClass('text-muted').addClass('text-primary');
        } else {
            $(element).removeClass('text-primary').addClass('text-muted');
        }
    }

    <?php if($this->session->flashdata('pesan_sukses')): ?>
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $this->session->flashdata("pesan_sukses"); ?>', timer: 3000, showConfirmButton: false });
    <?php endif; ?>

    <?php if($this->session->flashdata('pesan_error')): ?>
        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: '<?= $this->session->flashdata("pesan_error"); ?>' });
    <?php endif; ?>

    $('#fileInput').on('change', function() {
        if(this.files && this.files[0]) {
            var file = this.files[0];
            if(file.type !== 'application/pdf') {
                Swal.fire('Format Tidak Sesuai', 'Sistem hanya menerima file berekstensi .pdf', 'warning');
                $(this).val(''); 
                return;
            }
            if(file.size > 10485760) {
                Swal.fire('File Terlalu Besar', 'Ukuran file PDF maksimal adalah 10 MB.', 'warning');
                $(this).val(''); 
            }
        }
    });

    $('#formRegulasi').on('submit', function() {
        let btn = $('#btnSimpan');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
    });
});

function bukaPDF(url_dokumen, judul) {
    document.getElementById('judulDokumenViewer').innerText = judul;
    document.getElementById('iframePDF').src = url_dokumen;
    document.getElementById('btnDownloadPDF').href = url_dokumen;
    
    let tabelContainer = document.getElementById('tabelContainer');
    let pdfContainer = document.getElementById('pdfContainer');

    tabelContainer.classList.remove('col-12');
    tabelContainer.classList.add('col-lg-7');

    pdfContainer.classList.remove('d-none');
    pdfContainer.classList.add('col-lg-5');
}

function tutupPDF() {
    let tabelContainer = document.getElementById('tabelContainer');
    let pdfContainer = document.getElementById('pdfContainer');

    pdfContainer.classList.add('d-none');
    pdfContainer.classList.remove('col-lg-5');

    tabelContainer.classList.remove('col-lg-7');
    tabelContainer.classList.add('col-12');

    document.getElementById('iframePDF').src = "";
    document.getElementById('btnDownloadPDF').href = "#";
}

function cetakPDF() {
    var iframe = document.getElementById('iframePDF');
    iframe.contentWindow.focus();
    iframe.contentWindow.print();
}

function konfirmasiHapus(id_regulasi, nomor_dok) {
    Swal.fire({
        title: 'Nonaktifkan Dokumen?',
        html: "Anda akan menarik regulasi <b>" + nomor_dok + "</b> dari sistem.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Tarik Dokumen',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            window.location.href = "<?= base_url('index.php/regulasi/regulasiprobis/hapus_regulasi/'); ?>" + id_regulasi;
        }
    });
}
</script>