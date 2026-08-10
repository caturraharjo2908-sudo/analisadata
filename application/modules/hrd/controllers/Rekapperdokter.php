<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Rekapperdokter extends CI_Controller {

    public function __construct(){
        parent::__construct();
        rootsystem::system();
        $this->load->model("Modelrekapperdokter", "md");
    }

    public function index(){
        // Kita tidak perlu lagi load data master dokter ke dropdown
        $this->template->load("template/template-sidebar", "v_rekapperdokter");
    }

    // =========================================================================
    // FUNGSI KEAMANAN BARU: Mengambil DOKTER_ID dari tabel berdasarkan NIK session
    // =========================================================================
    private function _get_dokter_id_from_session() {
        // Berdasarkan Model login Anda, username (NIK) disimpan sebagai USER_ID
        $nik = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : (isset($_SESSION['userid']) ? $_SESSION['userid'] : '');
        
        if (empty($nik)) return null;

        // Cari DOKTER_ID di tabel SR01_GEN_USER_DATA
        $query = $this->db->query("SELECT DOKTER_ID FROM SR01_GEN_USER_DATA WHERE UPPER(USER_ID) = UPPER(?) AND AKTIF = '1'", array($nik))->row();
        
        return ($query && !empty($query->DOKTER_ID)) ? $query->DOKTER_ID : null;
    }

    // =========================================================================
    // AJAX REQUESTS (Menggunakan ID dari Session)
    // =========================================================================

    public function get_rekap_aktivitas(){
        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->_get_dokter_id_from_session(); 
        
        if(empty($dokter_id)) {
            echo json_encode(["responCode" => "01", "responDesc" => "Akun Anda tidak memiliki DOKTER_ID yang valid.", "responResult" => []]);
            return;
        }

        $result = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
        
        if(!empty($result)){
            echo json_encode(["responCode" => "00", "responHead" => "success", "responDesc" => "Data Ditemukan", "responResult" => $result]);
        }else{
            echo json_encode(["responCode" => "01", "responHead" => "info", "responDesc" => "Data Tidak Ditemukan", "responResult" => []]);
        }
    }

    public function get_rincian_pasien() {
        error_reporting(0); 
        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->_get_dokter_id_from_session();
        
        if(empty($dokter_id)) {
            echo json_encode(["responCode" => "01", "responDesc" => "Akun Anda tidak memiliki DOKTER_ID yang valid.", "responResult" => []]);
            return;
        }

        $result = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
        
        if(!empty($result)){
            echo json_encode(["responCode" => "00", "responResult" => $result]);
        }else{
            echo json_encode(["responCode" => "01", "responDesc" => "Data Tidak Ditemukan", "responResult" => []]);
        }
    }

    public function datarincianpasien_bykeuepisode() {
        error_reporting(0); 
        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->_get_dokter_id_from_session();
    
        if(empty($dokter_id)) {
            echo json_encode(["responCode" => "01", "responDesc" => "Akun Anda tidak memiliki DOKTER_ID yang valid.", "responResult" => []]);
            return;
        }

        $result = $this->md->datarincianpasien_bykeuepisode($dokter_id, $startdate, $endate, $dokter_id, $startdate, $endate);
        
        if(!empty($result)){
            echo json_encode(["responCode" => "00", "responResult" => $result]);
        }else{
            echo json_encode(["responCode" => "01", "responDesc" => "Data Tidak Ditemukan", "responResult" => []]);
        }
    }

    public function datarekapaktivitasdokter_jenisPelayanan() {
        error_reporting(0); 
        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->_get_dokter_id_from_session();

        if(empty($dokter_id)) {
            echo json_encode(["responCode" => "01", "responDesc" => "Akun Anda tidak memiliki DOKTER_ID yang valid.", "responResult" => []]);
            return;
        }

        $result = $this->md->datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id);

        if(!empty($result)){
            echo json_encode(["responCode" => "00", "responResult" => $result]);
        } else {
            echo json_encode(["responCode" => "01", "responDesc" => "Data Aktivitas Tidak Ditemukan", "responResult" => []]);
        }
    }

    // =========================================================================
    // EKSPOR DATA (EXCEL & PDF)
    // =========================================================================

    public function export_excel() {
        error_reporting(0);
        if (ob_get_level()) { ob_end_clean(); }

        $startdate = $this->input->post('startdate');
        $endate    = $this->input->post('endate');
        $dokter_id = $this->_get_dokter_id_from_session();
        
        if(empty($dokter_id)) {
            show_error('Akses Ditolak: Anda tidak memiliki akses sebagai Dokter atau DOKTER_ID tidak ditemukan.', 403, 'Akses Dilarang');
            return;
        }

        $data['rekap']   = $this->md->datarincianpasien_bykeuepisode($dokter_id, $startdate, $endate, $dokter_id, $startdate, $endate);
        $data['rincian'] = $this->md->datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id);
        
        $data['startdate'] = $startdate;
        $data['endate']    = $endate;

        $nama_dokter = isset($_SESSION['name']) ? $_SESSION['name'] : 'Dokter';
        $nama_dokter_clean = str_replace(array(' ', '.', ',', '/'), array('_', '', '', '_'), $nama_dokter);
        $nama_dokter_clean = preg_replace('/_+/', '_', $nama_dokter_clean); 
        $nama_dokter_clean = trim($nama_dokter_clean, '_'); 

        $tanggal_clean = date('d-m-Y');
        $filename = $nama_dokter_clean . "_Laporan_Aktivitas_Dokter_" . $tanggal_clean . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $data['nama_dokter'] = $nama_dokter;
        $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
    }

    public function export_pdf() {
        error_reporting(0); 
        if (ob_get_level()) { ob_end_clean(); }

        $startdate = $this->input->post('startdate');
        $endate    = $this->input->post('endate');
        $dokter_id = $this->_get_dokter_id_from_session();
        
        if(empty($dokter_id)) {
            show_error('Akses Ditolak: Anda tidak memiliki akses sebagai Dokter atau DOKTER_ID tidak ditemukan.', 403, 'Akses Dilarang');
            return;
        } 

        $data['rekap']   = $this->md->datarincianpasien_bykeuepisode($dokter_id, $startdate, $endate, $dokter_id, $startdate, $endate);
        $data['rincian'] = $this->md->datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id);
        $data['startdate'] = $startdate;
        $data['endate']    = $endate;

        $nama_dokter = isset($_SESSION['name']) ? $_SESSION['name'] : 'Dokter';
        $data['nama_dokter'] = $nama_dokter;

        $html = $this->load->view('cetakpdf/v_pdf_rekapperdokter', $data, TRUE);

        require_once FCPATH . 'vendor/autoload.php'; 
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper(array(0, 0, 595.28, 935.43), 'portrait');
        $dompdf->render();
        
        $nama_dokter_clean = str_replace(array(' ', '.', ',', '/'), array('_', '', '', '_'), $nama_dokter);
        $nama_dokter_clean = preg_replace('/_+/', '_', $nama_dokter_clean); 
        $nama_dokter_clean = trim($nama_dokter_clean, '_'); 
        
        $tanggal_download = date('d-m-Y');
        $filename = $nama_dokter_clean . "_Laporan_Aktivitas_Dokter_" . $tanggal_download . ".pdf";
        
        $dompdf->stream($filename, array("Attachment" => 1));
    }
}