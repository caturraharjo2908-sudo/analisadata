<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
					<div>
						<h1>Laporan SPI – Pembelian Obat Atas Permintaan Sendiri Farmasi</h1>
						<p class="mb-0">
                            Monitoring pengawasan internal atas permintaan sendiri pada unit farmasi untuk memastikan kepatuhan, mutu, dan pengelolaan obat.
                        </p>
					</div>
				</div>
                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
						<li class="nav-item">
							<a class="nav-link text-muted active" data-bs-toggle="tab" href="#summary">Summary</a>
						</li>
						<!-- <li class="nav-item">
							<a class="nav-link text-muted" data-bs-toggle="tab" href="#indikatormutu">Indikator Mutu</a>
						</li> -->
                        <li class="nav-item">
							<a class="nav-link text-muted" data-bs-toggle="tab" href="#rawdata">Raw Data</a>
						</li>
                        <!-- <li class="nav-item">
							<a class="nav-link text-muted" data-bs-toggle="tab" href="#temuantl">Temuan & Tindak Lanjut</a>
						</li> -->
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content mt-5">
	<div class="tab-pane fade active show" id="summary" role="tabpanel">
        <div class="row gy-5 g-xl-5 mb-xl-5">
			<div class="col-xl-12">
				<div class="card card-flush">
					<div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
							<span class="card-label fw-bolder fs-3 mb-1">Pembelian Obat Atas Permintaan Sendiri</span>
							<span class="text-muted mt-1 fw-bold fs-7">Berdasarkan Periode Bulan Transaksi</span>
						</h3>
					</div>
					<div class="card-body pt-0">
						<div class="card-rounded-bottom" id="grafikpembelianobat"></div>
					</div>
				</div>
			</div>
            <div class="col-xl-6">
				<div class="card card-flush">
                    <div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">20 Obat dengan Penjualan Tertinggi</span>
							<span class="text-muted mt-1 fw-bold fs-7">Menampilkan 20 obat dengan jumlah penjualan (QTY) terbanyak dalam periode yang dipilih</span>
                        </h3>
					</div>
					<div class="card-body p-0">
						<div class="card-rounded-bottom" id="grafikobat"></div>
					</div>
				</div>
			</div>
            <div class="col-xl-6">
				<div class="card card-flush">
                    <div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Distribusi Jumlah Obat per Golongan</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Menunjukkan total QTY obat yang masuk setiap golongan dalam periode tertentu</span>
                        </h3>
					</div>
					<div class="card-body p-0">
						<div class="card-rounded-bottom" id="grafikgolonganobat"></div>
					</div>
				</div>
			</div>
			<div class="col-xl-6">
				<div class="card card-flush">
                    <div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Penjualan Obat Golongan Narkotik</span>
							<span class="text-muted mt-1 fw-bold fs-7">Menampilkan daftar obat golongan narkotik dengan jumlah penjualan</span>
                        </h3>
					</div>
					<div class="card-body p-0">
						<div class="card-rounded-bottom" id="grafikobatnarkotik"></div>
					</div>
				</div>
			</div>
			<div class="col-xl-6">
				<div class="card card-flush">
                    <div class="card-header pt-5">
						<h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Penjualan Obat Golongan Psikotropika</span>
							<span class="text-muted mt-1 fw-bold fs-7">Menampilkan daftar obat golongan psikotropika dengan jumlah penjualan</span>
                        </h3>
					</div>
					<div class="card-body p-0">
						<div class="card-rounded-bottom" id="grafikobatpsikotropika"></div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <div class="tab-pane fade" id="indikatormutu" role="tabpanel">
    </div>
    <div class="tab-pane fade" id="rawdata" role="tabpanel">
        <div class="row gy-5 g-xl-5 mb-xl-5">
			<div class="col-xl-12">
                <?php      
					include_once(APPPATH."views/template/search.php");
				?>
                <div class="card card-flush">
					<div class="card-header pt-5" id="">
						<h3 class="card-title align-items-start flex-column">
							<span class="card-label fw-bolder fs-3 mb-1"></span>
						</h3>
						<div class="card-toolbar m-0">
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
									<a href="#" class="menu-link px-3" onclick="exportTableToExcel('tablerawdataapsfarmasi', 'Laporan APS Farmasi')">Download Excel</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive mh-610px">
							<table class="table align-middle table-row-dashed fs-8 gy-2" id="tablerawdataapsfarmasi">
								<thead class="align-middle">
									<tr class="fw-bolder text-muted bg-light">
										<th class="ps-4 rounded-start" rowspan="2">#</th>
										<th rowspan="2">No MR</th>
                                        <th rowspan="2">NIK Karyawan</th>
										<th rowspan="2">Nama Pasien</th>
                                        <th rowspan="2">Tanggal</th>
										<th rowspan="2">Poliklinik</th>
										<th rowspan="2">Dokter</th>
										<th rowspan="2">Provider</th>
										<th rowspan="2">Diagnosa</th>
                                        <th colspan="3" class="text-center">Obat</th>
										<th class="pe-4 text-end rounded-end" rowspan="2">Actions</th>
									</tr>
                                    <tr class="fw-bolder text-muted bg-light">
                                        <th>Nama</th>
                                        <th>Qty</th>
                                        <th>Golongan</th>
                                    </tr>
								</thead>
								<tbody class="text-gray-600 fw-bold" id="resultdataapsfarmasi"></tbody>
							</table>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="temuantl" role="tabpanel">
    </div>
</div>