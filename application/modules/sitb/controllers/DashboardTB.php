<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardTB extends CI_Controller {

    public function __construct() {
        parent::__construct();
        rootsystem::system();
        $this->load->model("Modeldatatb", "md");
    }

    public function index() {
        // 1. Ambil daftar semua periode dari model
        $resultperiode = $this->md->periode();

        // 2. Tangkap periode yang dipilih user dari filter (GET)
        $periode_terpilih = $this->input->get('selectperiode');
        
        // Jika belum ada pilihan, default ke periode terbaru (paling atas)
        if (empty($periode_terpilih)) {
            $periode_terpilih = $resultperiode[0]->PERIODE_KEY;
        }

        // 3. Konversi format PERIODE_KEY (MM.YYYY) ke format yang dibutuhkan query (DDMMYYYY)
        $split = explode('.', $periode_terpilih);
        $bulan = $split[0];
        $tahun = $split[1];

        $tgl_awal = '01' . $bulan . $tahun;
        $tgl_akhir = date('dmY', strtotime($tahun . '-' . $bulan . '-01 +1 month'));

        // 4. Ambil data pasien TB berdasarkan tanggal tersebut
        $data['pasien_tb'] = $this->md->datatb($tgl_awal, $tgl_akhir);

        // 5. Bangun string dropdown agar status 'selected' tetap terjaga
        $html_periode = "";
        foreach($resultperiode as $a) {
            $selected = ($a->PERIODE_KEY == $periode_terpilih) ? "selected" : "";
            $html_periode .= "<option value='".$a->PERIODE_KEY."' $selected>".$a->PERIODE."</option>";
        }

        $data['periode'] = $html_periode;
        $this->template->load("template/template-sidebar", "v_DashboardTB", $data);
    }
                                     
    public function export_excel() {
    // 1. Ambil periode dari URL
    $periode_terpilih = $this->input->get('selectperiode');
    if (empty($periode_terpilih)) {
        $resultperiode = $this->md->periode();
        $periode_terpilih = $resultperiode[0]->PERIODE_KEY;
    }

    // 2. Konversi tanggal untuk query
    $split = explode('.', $periode_terpilih);
    $tgl_awal = '01' . $split[0] . $split[1];
    $tgl_akhir = date('dmY', strtotime($split[1] . '-' . $split[0] . '-01 +1 month'));

    // 3. Tarik data lengkap dari model
    $data_pasien = $this->md->datatb($tgl_awal, $tgl_akhir);

    // 4. Header HTTP untuk download Excel
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_TB_Periode_".$periode_terpilih.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // 5. Output tabel dengan CSS khusus untuk format Text
    echo '<style> .str { mso-number-format:"\@"; } </style>'; // CSS untuk force format text
    echo '<table border="1">';
    echo '<tr>';
    echo '<th style="background-color:#D3D3D3;">EPISODE_ID</th>';
    echo '<th style="background-color:#D3D3D3;">NO_RM</th>';
    echo '<th style="background-color:#D3D3D3;">TGL_REGISTER</th>';
    echo '<th style="background-color:#D3D3D3;">NAMA_PASIEN</th>';
    echo '<th style="background-color:#D3D3D3;">NIK_KTP</th>'; // Kolom NIK
    echo '<th style="background-color:#D3D3D3;">JENIS_KELAMIN</th>';
    echo '<th style="background-color:#D3D3D3;">TANGGAL_LAHIR</th>';
    echo '<th style="background-color:#D3D3D3;">UMUR</th>';
    echo '<th style="background-color:#D3D3D3;">ALAMAT_LENGKAP</th>';
    echo '<th style="background-color:#D3D3D3;">PROPINSI</th>';
    echo '<th style="background-color:#D3D3D3;">KABUPATEN</th>';
    echo '<th style="background-color:#D3D3D3;">KECAMATAN</th>';
    echo '<th style="background-color:#D3D3D3;">KELURAHAN</th>';
    echo '<th style="background-color:#D3D3D3;">POLI</th>';
    echo '<th style="background-color:#D3D3D3;">TGL_PEMERIKSAAN</th>';
    echo '<th style="background-color:#D3D3D3;">HASIL_TCM</th>';
    echo '<th style="background-color:#D3D3D3;">HASIL_TORAKS</th>';
    echo '<th style="background-color:#D3D3D3;">DIAGNOSA_DOKTER</th>';
    echo '<th style="background-color:#D3D3D3;">DIAGNOSA_CODING</th>';
    echo '<th style="background-color:#D3D3D3;">ICD_KODE</th>';
    echo '<th style="background-color:#D3D3D3;">TANGGAL_MULAI_OBAT</th>';
    echo '<th style="background-color:#D3D3D3;">PADUAN_OAT</th>';
    echo '<th style="background-color:#D3D3D3;">ASSESSMENT_DOKTER</th>';
    echo '</tr>';

    foreach($data_pasien as $row) {
        echo '<tr>';
        foreach($row as $key => $cell) {
            // Berikan class "str" jika kolom adalah NIK_KTP atau NO_RM
            $class = ($key === 'NIK_KTP' || $key === 'NO_RM') ? 'class="str"' : '';
            echo '<td ' . $class . '>' . $cell . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}
}