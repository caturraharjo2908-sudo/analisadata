<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12 border">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>Rekapitulasi Aktivitas Dokter</h1>
                        <p class="mb-0">Laporan Rekapitulasi Pelayanan dan Tindakan per Dokter</p>
                    </div>
                </div>

                <div class="row mb-8 mt-3">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Awal</label>
                        <div class="position-relative d-flex align-items-center">
                            <span class="position-absolute ms-3 opacity-50"><i class="fas fa-calendar-alt"></i></span>
                            <input class="form-control form-control-sm ps-10" id="startdate" placeholder="Pilih Tanggal" style="cursor: pointer;" readonly />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Akhir</label>
                        <div class="position-relative d-flex align-items-center">
                            <span class="position-absolute ms-3 opacity-50"><i class="fas fa-calendar-alt"></i></span>
                            <input class="form-control form-control-sm ps-10" id="endate" placeholder="Pilih Tanggal" style="cursor: pointer;" readonly />
                        </div>
                    </div>
                    
                    <!-- KOTAK DOKTER (STATIS BERDASARKAN SESSION) -->
                    <div class="col-md-4">   
                        <label class="form-label fw-bold">Dokter DPJP</label>
                        <div class="form-control form-control-sm bg-light d-flex align-items-center" style="cursor: not-allowed;">
                            <i class="fas fa-user-md me-2 text-primary"></i> 
                            <strong><?= isset($_SESSION['name']) ? $_SESSION['name'] : 'Memuat...' ?></strong>
                        </div>
                    </div>                   

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <div class="d-flex w-100 gap-2 mb-0">
                            <button type="button" class="btn btn-sm btn-primary font-weight-bold flex-fill py-1" onclick="loadDataRekap()">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            <button type="button" class="btn btn-sm btn-success font-weight-bold flex-fill py-1" onclick="exportKeExcel()">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-danger font-weight-bold flex-fill py-1" onclick="exportKePDF()">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PERBAIKAN CLASS TABS: Menambahkan "active" pada link pertama -->
                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                        <li class="nav-item">
							<a class="nav-link text-active-primary active" data-bs-toggle="tab" href="#tab_rincian_pasien">Rekap Jumlah Pasien by DPJP</a>
						</li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary" data-bs-toggle="tab" href="#tabjenispelayanan">Aktivitas Dokter Jenis Pelayanan</a>
                        </li>
                    </ul>	
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content mt-5">
    <!-- HAPUS tab_rekap_pelayanan yang kosong karena menyebabkan tumpah tindih -->

    <!-- Tab 1: HARUS MEMILIKI "active show" -->
    <div class="tab-pane fade show active" id="tab_rincian_pasien" role="tabpanel">
		<div class="row gy-5 g-xl-8 mb-xl-8">
			<div class="col-xl-12">
				<div class="card card-flush">
					<div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Data Rincian Pasien By DPJP</span>
						</h3>
					</div>
					<div class="card-body p-8">
						<div class="table-responsive">
							<table class="table align-middle table-row-dashed fs-8 gy-2" id="tablerincianpasien">
                                <thead>
                                    <tr class="fw-bolder text-muted bg-light align-middle text-uppercase">
                                        <th class="ps-4 rounded-start w-50px">#</th>
                                        <th>JENIS PELAYANAN</th>
                                        <th>PERIODE</th>
                                        <th class="text-center rounded-end w-150px">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="result_rincian_pasien">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Silakan tentukan filter tanggal terlebih dahulu.</td>
                                    </tr>
                                </tbody>
                            </table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <!-- Tab 2: HANYA MEMILIKI "fade" (Tanpa active show) -->
    <div class="tab-pane fade" id="tabjenispelayanan" role="tabpanel">
		<div class="row gy-5 g-xl-8 mb-xl-8">
			<div class="col-xl-12">
				<div class="card card-flush">					
					<div class="card-body p-8">
						<div class="table-responsive">
							<table class="table align-middle gs-0 gy-4" id="tablejenispelayanan">
                                <thead>
                                    <tr class="fw-bolder text-muted bg-light align-middle text-uppercase">
                                        <th class="ps-4 rounded-start w-50px">#</th>
                                        <th>JENIS PELAYANAN</th>
                                        <th>NAMA DOKTER</th>
                                        <th>NAMA TINDAKAN / PELAYANAN</th>
                                        <th class="text-center rounded-end w-150px">TOTAL QTY</th>
                                    </tr>
                                </thead>
                                <tbody id="result_jenis_pelayanan">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Silakan tentukan filter tanggal terlebih dahulu.</td>
                                    </tr>
                                </tbody>
                             </table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $("#startdate").flatpickr({ dateFormat: "d-m-Y", allowInput: false, maxDate: "today" });
        $("#endate").flatpickr({ dateFormat: "d-m-Y", allowInput: false, maxDate: "today" });
    });

    function loadDataRekap() {
        var startdate = $('#startdate').val();
        var endate    = $('#endate').val();

        if(startdate == "" || endate == "") {
            alert("Silakan lengkapi tanggal laporan!");
            return;
        }

        // 1. AJAX UNTUK RINCIAN PASIEN
        $('#result_rincian_pasien').html('<tr><td colspan="4" class="text-center">Sedang memuat data...</td></tr>');
        
        $.ajax({
            url: "<?= site_url('hrd/Rekapperdokter/datarincianpasien_bykeuepisode') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                startdate: startdate, 
                endate: endate,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            success: function(res) {
                var html = "";
                if(res.responCode == "00") {
                    var no = 1;
                    $.each(res.responResult, function(i, item) {
                        html += "<tr>";
                        html += "<td class='ps-4'>" + no++ + "</td>";
                        html += "<td>" + item.JENIS + "</td>";
                        html += "<td>" + item.PERIODE + "</td>"; 
                        html += "<td class='text-center fw-bold'>" + item.TOTAL_KUNJUNGAN + "</td>"; 
                        html += "</tr>";
                    });
                } else {
                    html = "<tr><td colspan='4' class='text-center text-danger'>" + res.responDesc + "</td></tr>";
                }
                $('#result_rincian_pasien').html(html);
            },
            error: function() {
                $('#result_rincian_pasien').html('<tr><td colspan="4" class="text-center text-danger">Terjadi kesalahan pada server.</td></tr>');
            }
        });

        // 2. AJAX UNTUK AKTIVITAS DOKTER JENIS PELAYANAN
        $('#result_jenis_pelayanan').html('<tr><td colspan="5" class="text-center">Sedang memuat data aktivitas...</td></tr>');

        $.ajax({
            url: "<?= site_url('hrd/Rekapperdokter/datarekapaktivitasdokter_jenisPelayanan') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                startdate: startdate,
                endate: endate,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            success: function(res) {
                var html = "";
                if(res.responCode == "00") {
                    var no = 1;
                    $.each(res.responResult, function(i, item) {
                        html += "<tr>";
                        html += "<td class='ps-4'>" + no++ + "</td>";
                        html += "<td><span class='badge badge-light-primary fw-bold'>" + item.JENIS + "</span></td>";
                        html += "<td>" + (item.NAMADOKTER ? item.NAMADOKTER : '-') + "</td>";
                        html += "<td>" + (item.NAMAPELAYANAN ? item.NAMAPELAYANAN : '-') + "</td>";
                        html += "<td class='text-center fw-bold text-dark'>" + (item.TOTAL_QTY ? item.TOTAL_QTY : 0) + "</td>";
                        html += "</tr>";
                    });
                } else {
                    html = "<tr><td colspan='5' class='text-center text-danger'>" + res.responDesc + "</td></tr>";
                }
                $('#result_jenis_pelayanan').html(html);
            },
            error: function() {
                $('#result_jenis_pelayanan').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data dari server.</td></tr>');
            }
        });
    }

    function exportKePDF() {
        var startdate = $('#startdate').val();
        var endate    = $('#endate').val();

        if(startdate == "" || endate == "") {
            alert("Silakan lengkapi filter tanggal terlebih dahulu!");
            return;
        }

        var form = $('<form>', {
            'action': "<?= site_url('hrd/Rekapperdokter/export_pdf') ?>",
            'method': 'POST',
            'target': '_blank' 
        }).append($('<input>', { 'name': 'startdate', 'value': startdate, 'type': 'hidden'
        })).append($('<input>', { 'name': 'endate', 'value': endate, 'type': 'hidden'
        })).append($('<input>', { 'name': '<?= $this->security->get_csrf_token_name(); ?>', 'value': '<?= $this->security->get_csrf_hash(); ?>', 'type': 'hidden'
        }));

        $(document.body).append(form);
        form.submit();
        form.remove(); 
    }

    function exportKeExcel() {
        var startdate = $('#startdate').val();
        var endate    = $('#endate').val();

        if(startdate == "" || endate == "") {
            alert("Silakan lengkapi filter tanggal terlebih dahulu!");
            return;
        } 

        var form = $('<form>', {
            'action': "<?= site_url('hrd/Rekapperdokter/export_excel') ?>",
            'method': 'POST'
        }).append($('<input>', { 'name': 'startdate', 'value': startdate, 'type': 'hidden'
        })).append($('<input>', { 'name': 'endate', 'value': endate, 'type': 'hidden'
        })).append($('<input>', { 'name': '<?= $this->security->get_csrf_token_name(); ?>', 'value': '<?= $this->security->get_csrf_hash(); ?>', 'type': 'hidden'
        }));

        $(document.body).append(form);
        form.submit();
        form.remove(); 
    }
</script>