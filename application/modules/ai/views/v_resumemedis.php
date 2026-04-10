<div class="row gy-5 g-xl-8 mb-xl-8">
    <div class="col-xl-4">
		<div class="card card-flush">
			<div class="card-header pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">List Pending Resume</span>
					<span class="text-muted mt-1 fw-bold fs-7">Berdasarkan Dokter Spesialis</span>
				</h3>
			</div>
			<div class="card-body pt-0">
				<div class="table-responsive mh-600px scroll-y me-n5 pe-5" style="overflow-x:auto; white-space:nowrap;">
                    <table class="table align-middle table-row-dashed fs-8 gy-2" id="tableaggregatedataradiologi">
                        <thead class="align-middle">
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 rounded-start">#</th>
                                <th>MR</th>
                                <th>NAMA PASIEN</th>
                                <th>TGL LAHIR</th>
                                <th>UMUR</th>
                                <th>JENIS KELAMIN</th>
                                <th>TGL MASUK</th>
                                <th>TGL KELUAR</th>
                                <th>RUANG</th>
                                <th class="pe-4 rounded-end text-end">KELAS</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold" id="resultdatapendingresume"></tbody>
                    </table>
                </div>
			</div>
		</div>
	</div>
    <div class="col-xl-8">
        <div class="d-flex justify-content-end">
            <input type="text" id="nampasien">
            <input type="text" id="pasienid">
            <input type="text" id="episodeid">
            <a class="btn btn-primary mb-10" id="btnsejarah" name="btnsejarah">Sejarah</a>
        </div>
        
        <div style="max-height: 80vh; overflow-y: auto;">
            <h1 class="text-info">Ringkasan Riwayat Penyakit</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Keluhan Utama :</label>
                <textarea class="form-control" id="keluhanutama_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Gejala Penyerta :</label>
                <textarea class="form-control" id="gejalapenyerta_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Riwayat Penyakit Sekarang :</label>
                <textarea class="form-control mb-5" id="penyakitsekarang1_db"></textarea>
                <textarea class="form-control"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Riwayat Penyakit Dahulu :</label>
                <textarea class="form-control" id="penyakitdahulu_db"></textarea>
            </div>
            <h1 class="text-info">Pemeriksaan Fisik</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Tanda-tanda Vital :</label>
                <textarea class="form-control" id="ttv_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Status Lokalis :</label>
                <textarea class="form-control" id="lokalis_db"></textarea>
            </div>
            <h1 class="text-info">Pemeriksaaan Penunjang</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Laboratorium :</label>
                <textarea class="form-control" id="lab_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Pencitraan Diagnostik :</label>
                <textarea class="form-control" id="rad_db"></textarea>
            </div>
            <h1 class="text-info">Diagnosis</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Indikasi Rawat :</label>
                <textarea class="form-control" id="indikasiranap_db"></textarea>
            </div>
            <h1 class="text-info">Tindakan Medis</h1>
            <h1 class="text-info">Terapi Medika Mentosa</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Obat Selama dirawat :</label>
                <textarea class="form-control" id="obatperawatan_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Obat Pulang :</label>
                <textarea class="form-control" id="obatpulang_db"></textarea>
            </div>
            <h1 class="text-info">Tindak Lanjut</h1>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Kontrol Ulang :</label>
                <textarea class="form-control" id="kontrol_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Segera Bawa ke RS Bila :</label>
                <textarea class="form-control" id="segera_db"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Severity :</label>
                <textarea class="form-control"></textarea>
            </div>
            <div class="mb-5">
                <label class="d-flex align-items-center fs-5 fw-bold mb-2">Lain - lain :</label>
                <textarea class="form-control"></textarea>
            </div>
        </div>
        
    </div>
</div>