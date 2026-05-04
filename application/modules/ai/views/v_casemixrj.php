<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-12">
        <div class="card rounded bgi-no-repeat bgi-position-x-end bgi-size-cover" style="background-color: #ffffff; background-position: calc(100% + 0.5rem) 100%;background-size: 20% auto;background-image: url('<?= base_url('assets/images/svg/misc/taieri.svg') ?>');">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
                    <div>
                        <h1>Clinical Claim Review Engine</h1>
                        <p class="mb-0">
							AI-assisted Claim Validation & Optimization System
						</p>
                    </div>
                </div>
                <div class="d-flex overflow-auto min-h-30px">
                    <ul class="nav nav-stretch nav-line-tabs border-transparent fs-6 fw-bold flex-nowrap">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#tab1">Rawat Jalan</a>
						</li>
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content mt-5">

    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        <?php      
            include_once(APPPATH."views/template/search.php");
        ?>
		<div class="card card-flush">
			<div class="card-header pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">Kunjungan Rawat Jalan</span>
					<span class="text-muted mt-1 fw-bold fs-7">-</span>
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
							<a href="#" class="menu-link px-3" onclick="exportTableToExcel('tabledatacasemixrj', 'Casemix Rawat Jalan')">Download Excel</a>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body pt-0">
				<div class="table-responsive mh-610px">
                    <table class="table align-middle table-row-dashed fs-8 gy-2" id="tabledatacasemixrj">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">#</th>
                                <th>No MR</th>
                                <th>Nama Pasien</th>
                                <th>Tgl Masuk</th>
                                <th>Poli Tujuan</th>
                                <th>Nama Dokter</th>
                                <th>No SEP</th>
                                <th>Status</th>
                                <th>DPJP Utama</th>
                                <th class="pe-4 text-end rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold" id="resultdatacasemixrj"></tbody>
                    </table>
                </div>
			</div>
		</div>
    </div>

</div>