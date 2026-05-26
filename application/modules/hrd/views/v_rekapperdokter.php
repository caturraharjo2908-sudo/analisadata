<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12 border">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>Rekapitulasi Aktivitas Dokter</h1>
                        <p class="mb-0">
                            Laporan Rekapitulasi Pelayanan dan Tindakan per Dokter
                        </p>
                    </div>
                </div>

                <div class="row mb-8 mt-3">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Awal</label>
                        <div class="position-relative d-flex align-items-center">
                            <span class="position-absolute ms-3 opacity-50">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input class="form-control form-control-sm ps-10" id="startdate" placeholder="Pilih Tanggal" style="cursor: pointer;" readonly />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tanggal Akhir</label>
                        <div class="position-relative d-flex align-items-center">
                            <span class="position-absolute ms-3 opacity-50">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input class="form-control form-control-sm ps-10" id="endate" placeholder="Pilih Tanggal" style="cursor: pointer;" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">   
                        <label class="form-label fw-bold">Pilih Dokter</label>
                        <select class="form-select form-select-sm" id="dokter_id" data-placeholder="Cari & Pilih Dokter...">
                            <?= $opt_dokter; ?>
                        </select>
                    </div>                   
                    <!-- <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-sm btn-primary w-100 px-2" id="btnFilter" onclick="loadDataRekap()">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-sm btn-danger w-100 px-2" onclick="exportKePDF()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-success w-100 px-2" onclick="exportKeExcel()">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div> -->

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <div class="d-flex w-100 gap-2 mb-0">
                            <button type="button" class="btn btn-sm btn-primary font-weight-bold flex-fill py-1" onclick="loadDataRekap()">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            
                            <button type="button" class="btn btn-sm btn-success font-weight-bold flex-fill py-1" onclick="exportKeExcel()">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>

                            <!-- <button type="button" class="btn btn-sm btn-danger font-weight-bold flex-fill py-1" onclick="exportKePDF()">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button> -->
                        </div>
                    </div>
                </div>

                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
						<li class="nav-item">
							<a class="nav-link text-muted active" data-bs-toggle="tab" href="#tab_rekap_pelayanan">Rekap Pelayanan</a>
						</li>
						<li class="nav-item">
							<a class="nav-link text-muted" data-bs-toggle="tab" href="#tab_rincian_pasien">Rekap Jumlah Pasien</a>
						</li>
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content mt-5">
    <div class="tab-pane fade active show" id="tab_rekap_pelayanan" role="tabpanel">
		<div class="row gy-5 g-xl-8 mb-xl-8">
			<div class="col-xl-12">
				<div class="card card-flush">
					<div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Data Rekap Pelayanan</span>
						</h3>
						<div class="card-toolbar m-0">
							<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
								<div class="menu-item px-3">
									<a href="#" class="menu-link px-3" onclick="exportTableToExcel('tablerekappelayanan', 'Rekap Pelayanan Dokter')">Download Excel</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-8">
						<div class="table-responsive">
							<table class="table align-middle table-row-dashed fs-8 gy-2" id="tablerekappelayanan">
								<thead>
									<tr class="fw-bolder text-muted bg-light align-middle text-uppercase">
										<th class="ps-4 rounded-start w-50px">#</th>
										<th class="w-200px">ID LAYANAN</th>
										<th>NAMA PELAYANAN / TINDAKAN</th>
                                        <th class="text-center rounded-end w-150px">JUMLAH (QTY)</th>
									</tr>
								</thead>
								<tbody class="text-gray-600 fw-bold" id="result_rekap_pelayanan">
                                    </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="tab-pane fade" id="tab_rincian_pasien" role="tabpanel">
		<div class="row gy-5 g-xl-8 mb-xl-8">
			<div class="col-xl-12">
				<div class="card card-flush">
					<div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Data Rincian Pasien</span>
						</h3>
						<div class="card-toolbar m-0">
							<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
								<div class="menu-item px-3">
									<a href="#" class="menu-link px-3" onclick="exportTableToExcel('tablerincianpasien', 'Detail Pasien Dokter')">Download Excel</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-8">
						<div class="table-responsive">
							<table class="table align-middle table-row-dashed fs-8 gy-2" id="tablerincianpasien">
                                <thead>
                                    <tr class="fw-bolder text-muted bg-light align-middle text-uppercase">
                                        <th class="ps-4 rounded-start w-50px">#</th>
                                        <th>TANGGAL</th>
                                        <th>NAMA DOKTER</th>
                                        <th class="text-center rounded-end w-150px">JUMLAH PASIEN</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="result_rincian_pasien">
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

    // Pastikan DOM siap sebelum menginisialisasi Datepicker
    $(document).ready(function() {
        // Inisialisasi Flatpickr untuk Tanggal Awal
        $("#startdate").flatpickr({
            dateFormat: "d-m-Y", // Format disesuaikan dengan TO_DATE Oracle (DD-MM-YYYY)
            allowInput: false,
            // Opsional: Batasi agar tanggal awal tidak bisa lebih dari hari ini
            maxDate: "today" 
        });

        // Inisialisasi Flatpickr untuk Tanggal Akhir
        $("#endate").flatpickr({
            dateFormat: "d-m-Y",
            allowInput: false,
            maxDate: "today"
        });
    });

    // 3. INI TAMBAHAN UNTUK SELECT2 DOKTER (BOLD & HITAM)
    //$('#episode_id').select2({
        $('#dokter_id').select2({
        placeholder: "Cari & Pilih Dokter...",
        allowClear: true,
        // Fungsi untuk memformat tampilan list dropdown
        templateResult: function (data) {
            if (!data.id) { return data.text; } // Lewati jika hanya teks placeholder
            return $('<span style="color: #000000; font-weight: bold;">' + data.text + '</span>');
        },
        // Fungsi untuk memformat tampilan teks yang sudah dipilih
        templateSelection: function (data) {
            if (!data.id) { return data.text; } 
            return $('<span style="color: #000000; font-weight: bold;">' + data.text + '</span>');
        }
    });

    function loadDataRekap() {
    var startdate = $('#startdate').val();
    var endate = $('#endate').val();
    // PERBAIKAN 1: Ambil dari ID elemen select dokter yang benar
    var dokter_id = $('#dokter_id').val(); 

    if(startdate == "" || endate == "" || dokter_id == "") {
        alert("Silakan lengkapi tanggal dan pilih Dokter!");
        return;
    }

    $('#result_rekap_pelayanan').html('<tr><td colspan="4" class="text-center">Sedang memuat data...</td></tr>');

    $.ajax({
        // PERBAIKAN 2: Gunakan site_url agar routing index.php di lokal aman dari 404
        url: "<?= site_url('hrd/Rekapperdokter/get_rekap_aktivitas') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            startdate: startdate,
            endate: endate,
            dokter_id: dokter_id, // PERBAIKAN 3: Kirim parameter sebagai 'dokter_id'
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        success: function(res) {
            var html = "";
            if(res.responCode == "00") {
                var no = 1;
                $.each(res.responResult, function(i, item) {
                    html += "<tr>";
                    html += "<td class='ps-4'>" + no++ + "</td>";
                    html += "<td>" + item.LAYAN_ID + "</td>";
                    html += "<td>" + item.NAMAPELAYANAN + "</td>";
                    html += "<td class='text-center'>" + item.JML + "</td>";
                    html += "</tr>";
                });
            } else {
                html = "<tr><td colspan='4' class='text-center text-danger'>" + res.responDesc + "</td></tr>";
            }
            
            $('#result_rekap_pelayanan').html(html);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#result_rekap_pelayanan').html('<tr><td colspan="4" class="text-center text-danger">Gagal terhubung ke server. Sila cek Inspect Element (F12) bagian Console.</td></tr>');
        }
        
    });

    // AJAX UNTUK RINCIAN PASIEN
    $('#result_rincian_pasien').html('<tr><td colspan="4" class="text-center">Sedang memuat data...</td></tr>');
    
    $.ajax({
        url: "<?= site_url('hrd/Rekapperdokter/get_rincian_pasien') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            startdate: startdate,
            endate: endate,
            dokter_id: dokter_id,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        success: function(res) {
            var html = "";
            if(res.responCode == "00") {
                var no = 1;
                $.each(res.responResult, function(i, item) {
                    html += "<tr>";
                    html += "<td class='ps-4'>" + no++ + "</td>";
                    html += "<td>" + item.TANGGAL + "</td>";
                    html += "<td>" + item.NAMA_DOKTER + "</td>";
                    html += "<td class='text-center'>" + item.JMLPASIEN + "</td>";
                    html += "</tr>";
                });
            } else {
                html = "<tr><td colspan='4' class='text-center text-danger'>" + res.responDesc + "</td></tr>";
            }
            $('#result_rincian_pasien').html(html);
        }
    });
 }
    
 function exportKePDF() {
    var startdate = $('#startdate').val();
    var endate = $('#endate').val();
    var dokter_id = $('#dokter_id').val();

    if(startdate == "" || endate == "" || dokter_id == "") {
        alert("Silakan lengkapi filter tanggal dan dokter terlebih dahulu!");
        return;
    }

    // Membuat form virtual untuk mem-POST data ke server dan membuka PDF di tab baru
    var form = $('<form>', {
        'action': "<?= site_url('hrd/Rekapperdokter/export_pdf') ?>",
        'method': 'POST',
        'target': '_blank' 
    }).append($('<input>', {
        'name': 'startdate',
        'value': startdate,
        'type': 'hidden'
    })).append($('<input>', {
        'name': 'endate',
        'value': endate,
        'type': 'hidden'
    })).append($('<input>', {
        'name': 'dokter_id',
        'value': dokter_id,
        'type': 'hidden'
    })).append($('<input>', {
        'name': '<?= $this->security->get_csrf_token_name(); ?>',
        'value': '<?= $this->security->get_csrf_hash(); ?>',
        'type': 'hidden'
    }));

    $(document.body).append(form);
    form.submit();
    form.remove(); // Hapus form setelah dikirim agar memori bersih
    }

    function exportKeExcel() {
    var startdate = $('#startdate').val();
    var endate    = $('#endate').val();
    var dokter_id = $('#dokter_id').val();

    if(startdate == "" || endate == "" || dokter_id == "") {
        alert("Silakan lengkapi filter tanggal dan dokter terlebih dahulu!");
        return;
    }

    // Membuat form virtual untuk mem-POST data ke fungsi excel controller
    var form = $('<form>', {
        'action': "<?= site_url('hrd/Rekapperdokter/export_excel') ?>",
        'method': 'POST'
    }).append($('<input>', {
        'name': 'startdate',
        'value': startdate,
        'type': 'hidden'
    })).append($('<input>', {
        'name': 'endate',
        'value': endate,
        'type': 'hidden'
    })).append($('<input>', {
        'name': 'dokter_id',
        'value': dokter_id,
        'type': 'hidden'
    })).append($('<input>', {
        'name': '<?= $this->security->get_csrf_token_name(); ?>',
        'value': '<?= $this->security->get_csrf_hash(); ?>',
        'type': 'hidden'
    }));

    $(document.body).append(form);
    form.submit();
    form.remove(); // Bersihkan form dari memori setelah disubmit
    }

</script>
