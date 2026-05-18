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

            <!-- ================= TEMPLATE BLOCK ================= -->
            <!-- Keluhan Utama -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Keluhan Utama</label>
                <div class="row">
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="keluhanutama_soap" rows="4"></textarea>
                    </div> -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="keluhanutama_ai" rows="4" readonly></textarea>
                    </div>
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="keluhanutama_final" rows="4"></textarea>
                    </div> -->
                </div>
            </div>

            <!-- Gejala Penyerta -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Gejala Penyerta</label>
                <div class="row">
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="gejala_soap" rows="4"></textarea>
                    </div> -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="gejala_ai" rows="4" readonly></textarea>
                    </div>
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="gejala_final" rows="4"></textarea>
                    </div> -->
                </div>
            </div>

            <!-- Riwayat Penyakit Sekarang -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Riwayat Penyakit Sekarang</label>
                <div class="row">
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="rps_soap" rows="4"></textarea>
                    </div> -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="rps_ai" rows="4" readonly></textarea>
                    </div>
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="rps_final" rows="4"></textarea>
                    </div> -->
                </div>
            </div>

            <!-- Riwayat Penyakit Dahulu -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Riwayat Penyakit Dahulu</label>
                <div class="row">
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="rpd_soap" rows="4"></textarea>
                    </div> -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="rpd_ai" rows="4" readonly></textarea>
                    </div>
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="rpd_final" rows="4"></textarea>
                    </div> -->
                </div>
            </div>

            <h1 class="text-info">Pemeriksaan Fisik</h1>

            <!-- TTV -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Tanda-Tanda Vital (TTV)</label>
                <div class="row">

                    <!-- SOAP -->
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="ttv_soap" rows="4" placeholder="Input dari SOAP..."></textarea>
                    </div> -->

                    <!-- AI -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="ttv_ai" rows="4" placeholder="Hasil AI..." readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="ttv_final" rows="4" placeholder="Final dokter..."></textarea>
                    </div> -->

                </div>
            </div>

            <!-- Lokalis -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Status Lokalis</label>
                <div class="row">

                    <!-- SOAP -->
                    <!-- <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="lokalis_soap" rows="4" placeholder="Input dari SOAP..."></textarea>
                    </div> -->

                    <!-- AI -->
                    <div class="col-md-12">
                        <label>AI</label>
                        <textarea class="form-control" id="lokalis_ai" rows="4" placeholder="Hasil AI..." readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <!-- <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="lokalis_final" rows="4" placeholder="Final dokter..."></textarea>
                    </div> -->

                </div>
            </div>

            <h1 class="text-info">Pemeriksaan Penunjang</h1>

            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Obat Selama Dirawat</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="obatperawatan_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="obatperawatan_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="obatperawatan_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Obat Pulang</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="obatpulang_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="obatpulang_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="obatpulang_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <!-- Lab -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Laboratorium</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="lab_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="lab_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="lab_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <!-- Radiologi -->
            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Pencitraan Diagnostik</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="rad_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="rad_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="rad_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <h1 class="text-info">Diagnosis</h1>

            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Indikasi Rawat</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="indikasiranap_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="indikasiranap_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="indikasiranap_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <h1 class="text-info">Tindak Lanjut</h1>

            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Kontrol Ulang</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="kontrol_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="kontrol_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="kontrol_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

            <div class="mb-5">
                <label class="fs-5 fw-bold mb-3">Segera Bawa ke RS Bila</label>
                <div class="row">

                    <!-- SOAP -->
                    <div class="col-md-4">
                        <label>SOAP</label>
                        <textarea class="form-control" id="segera_soap" rows="4"></textarea>
                    </div>

                    <!-- AI -->
                    <div class="col-md-4">
                        <label>AI</label>
                        <textarea class="form-control" id="segera_ai" rows="4" readonly></textarea>
                    </div>

                    <!-- FINAL -->
                    <div class="col-md-4">
                        <label>Final Resume</label>
                        <textarea class="form-control" id="segera_final" rows="4"></textarea>
                    </div>

                </div>
            </div>

        </div>
        
    </div>
</div>