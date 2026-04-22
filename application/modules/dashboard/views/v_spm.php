<style>
	.mh-600px {
		max-height: 600px;
		overflow: auto; /* 🔥 wajib untuk sticky */
	}

	/* 🔥 TABLE */
	#tabledataspmtw1 {
		border-collapse: collapse; /* 🔥 jangan pakai separate */
		width: 100%;
	}

	/* 🔥 HEADER UMUM */
	#tabledataspmtw1 thead th {
		position: sticky;
		background-color: #f8f9fa;
		text-align: center;
		vertical-align: middle;
		z-index: 2;
	}

	/* 🔥 BARIS HEADER PERTAMA */
	#tabledataspmtw1 thead tr:first-child th {
		top: 0;
		z-index: 3;
	}

	/* 🔥 BARIS HEADER KEDUA */
	#tabledataspmtw1 thead tr:nth-child(2) th {
		top: 40px; /* ⚠️ sesuaikan tinggi baris pertama */
		z-index: 2;
	}

	/* 🔥 SHADOW BIAR KELIHATAN FLOAT */
	#tabledataspmtw1 thead tr:first-child th {
		box-shadow: 0 2px 2px rgba(0,0,0,0.05);
	}

	/* 🔥 OPTIONAL: biar tidak goyang saat scroll */
	.table-responsive {
		overflow: auto !important;
	}
</style>


<div class="row gy-5 g-xl-8 mb-xl-8">
	<div class="col-xl-12">
		<div class="card card-flush">
			<div class="card-header pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">
                        Capaian SPM BLUD RSUD/RSKD
                    </span>

                    <span class="text-muted mt-1 fw-bold fs-7">
                        Lampiran Peraturan Gubernur Daerah Khusus Ibukota Jakarta Nomor 20 Tahun 2016
                        tentang Standar Pelayanan Minimal Rumah Sakit Umum Daerah dan Rumah Sakit Khusus Daerah
                    </span>
				</h3>
				<div class="card-toolbar m-0">
					<ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#tw1">TW 1</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#tw2">TW 2</a>
						</li>
                        <li class="nav-item" role="presentation">
							<a class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#tw3">TW 3</a>
						</li>
                        <li class="nav-item" role="presentation">
							<a class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#tw4">TW 4</a>
						</li>
					</ul>
					<button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-primary btn-active-light-primary me-n3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
						<span class="svg-icon svg-icon-3 svg-icon-primary">
							<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000" />
									<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
								</g>
							</svg>
						</span>
					</button>
					<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
						<div class="menu-item px-3">
							<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">More Actions</div>
						</div>
						<div class="menu-item px-3">
							<a href="#" class="menu-link px-3" id="btnDownloadExcelProviderIGD">Download Excel</a>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body pt-0">
				<div class="tab-content">
					<div id="tw1" class="card-body p-0 tab-pane fade show active" role="tabpanel">
                        <div class="table-responsive mh-600px scroll-y me-n5 pe-5" style="overflow-x:auto;">
							<table class="table align-middle table-row-dashed fs-8 gy-2" id="tabledataspmtw1">
								<thead class="align-middle">
									<tr class="fw-bolder text-muted bg-light">
										<th class="ps-4 rounded-start" rowspan="2">#</th>
										<th rowspan="2">Jenis Pelayanan Dasar</th>
                                        <th colspan="2">Indikator Standar Pelayanan</th>
										<th colspan="2">Target Tahun</th>
                                        <th rowspan="2">Pengisian</th>
										<th colspan="3">Realisasi</th>
										<th rowspan="2">Realisasi TW 1</th>
										<th rowspan="2">Analisa Capaian</th>
										<th rowspan="2">Permasalahan</th>
                                        <th class="text-center" rowspan="2">RTL</th>
										<th class="pe-4 text-end rounded-end" rowspan="2">Actions</th>
									</tr>
                                    <tr class="fw-bolder text-muted bg-light">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                    </tr>
								</thead>
								<tbody class="text-gray-600 fw-bold" id="resuldataspmtw1"></tbody>
							</table>
						</div>
					</div>
					<div id="tw2" class="card-body p-0 tab-pane fade" role="tabpanel">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>