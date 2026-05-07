<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-4">
        <?php      
            include_once(APPPATH."views/template/search.php");
        ?>
		<div class="card card-flush">
			<div class="card-header pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">List User</span>
					<span class="text-muted mt-1 fw-bold fs-7">-</span>
				</h3>
			</div>
			<div class="card-body pt-0">
				<div class="table-responsive mh-610px">
                    <table class="table align-middle table-row-dashed fs-8 gy-2">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">#</th>
                                <th>Username</th>
                                <th>Nama User</th>
                                <th class="pe-4 text-end rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold" id="resultdatauser"></tbody>
                    </table>
                </div>
			</div>
		</div>
	</div>
    <div class="col-xl-8">
		<div class="card card-flush">
			<div class="card-header pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">List Modules</span>
					<span class="text-muted mt-1 fw-bold fs-7">-</span>
				</h3>
			</div>
			<div class="card-body pt-0">
				<div class="scroll-y me-n5 pe-5" id="listmodules"></div>
			</div>
		</div>
	</div>
</div>