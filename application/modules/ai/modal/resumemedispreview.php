<div class="modal fade" id="modal_view_resume" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center mb-5">
                    <h1 class="mb-3">Preview Resume Medis</h1>
                    <div class="text-muted fw-bold fs-5">
                        Bandingkan resume dokter dan hasil AI (CARE) untuk memastikan akurasi, konsistensi, dan kelengkapan data klinis.
                    </div>
                </div>

                <input type="text" id="episodeid" name="episodeid" class="form-control form-control-solid">

                <h1 class="text-info">Ringkasan Riwayat Penyakit</h1>

                <!-- ================= TEMPLATE BLOCK ================= -->
                <!-- Keluhan Utama -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Keluhan Utama</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="keluhanutama_soap" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="keluhanutama_ai" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="keluhanutama_final" rows="4" readonly></textarea>
                        </div>
                    </div>
                </div>

                <!-- Gejala Penyerta -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Gejala Penyerta</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="gejala_soap" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="gejala_ai" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="gejala_final" rows="4" readonly></textarea>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Penyakit Sekarang -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Riwayat Penyakit Sekarang</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="rps_soap" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="rps_ai" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="rps_final" rows="4" readonly></textarea>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Penyakit Dahulu -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Riwayat Penyakit Dahulu</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="rpd_soap" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="rpd_ai" rows="4" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="rpd_final" rows="4" readonly></textarea>
                        </div>
                    </div>
                </div>

                <h1 class="text-info">Pemeriksaan Fisik</h1>

                <!-- TTV -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Tanda-Tanda Vital (TTV)</label>
                    <div class="row">

                        <!-- SOAP -->
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="ttv_soap" rows="4" placeholder="Input dari SOAP..." readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="ttv_ai" rows="4" placeholder="Hasil AI..." readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="ttv_final" rows="4" placeholder="Final dokter..." readonly></textarea>
                        </div>

                    </div>
                </div>

                <!-- Lokalis -->
                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Status Lokalis</label>
                    <div class="row">

                        <!-- SOAP -->
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="lokalis_soap" rows="4" placeholder="Input dari SOAP..." readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="lokalis_ai" rows="4" placeholder="Hasil AI..." readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="lokalis_final" rows="4" placeholder="Final dokter..." readonly></textarea>
                        </div>

                    </div>
                </div>

                <h1 class="text-info">Pemeriksaan Penunjang</h1>

                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Obat Selama Dirawat</label>
                    <div class="row">

                        <!-- SOAP -->
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="obatperawatan_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="obatperawatan_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="obatperawatan_final" rows="4" readonly></textarea>
                        </div>

                    </div>
                </div>

                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Obat Pulang</label>
                    <div class="row">

                        <!-- SOAP -->
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="obatpulang_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="obatpulang_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="obatpulang_final" rows="4" readonly></textarea>
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
                            <textarea class="form-control" id="lab_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="lab_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="lab_final" rows="4" readonly></textarea>
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
                            <textarea class="form-control" id="rad_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="rad_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="rad_final" rows="4" readonly></textarea>
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
                            <textarea class="form-control" id="indikasiranap_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="indikasiranap_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="indikasiranap_final" rows="4" readonly></textarea>
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
                            <textarea class="form-control" id="kontrol_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="kontrol_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="kontrol_final" rows="4" readonly></textarea>
                        </div>

                    </div>
                </div>

                <div class="mb-5">
                    <label class="fs-5 fw-bold mb-3">Segera Bawa ke RS Bila</label>
                    <div class="row">

                        <!-- SOAP -->
                        <div class="col-md-4">
                            <label>SOAP</label>
                            <textarea class="form-control" id="segera_soap" rows="4" readonly></textarea>
                        </div>

                        <!-- AI -->
                        <div class="col-md-4">
                            <label>AI</label>
                            <textarea class="form-control" id="segera_ai" rows="4" readonly></textarea>
                        </div>

                        <!-- FINAL -->
                        <div class="col-md-4">
                            <label>Final Resume</label>
                            <textarea class="form-control" id="segera_final" rows="4" readonly></textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer p-1">				
                <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">
                    CLOSE
                </button>
            </div>
        </div>
    </div>
</div>