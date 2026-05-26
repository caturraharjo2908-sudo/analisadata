<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Nama class disesuaikan dengan nama file agar tidak error di CodeIgniter
class Rekapperdokter extends CI_Controller {

    public function __construct(){
        parent::__construct();
        rootsystem::system();
        $this->load->model("Modelrekapperdokter", "md");
    }

    public function index(){
    $data = $this->loadcombobox();
    // Buka komentar baris di bawah ini agar halaman bisa dirender:
    $this->template->load("template/template-sidebar","v_rekapperdokter",$data);
}
    public function loadcombobox(){
        // 1. Load data periode (opsional jika masih dipakai)
    $resultperiode = $this->md->periode();
    
    // 2. Load data master dokter dari Model
    $resultdokter = $this->md->get_list_dokter();
    
    $data = array(); 
    
    // 3. Rakit tag <option> untuk dropdown dokter
    $opt_dokter = "<option value=''>-- Pilih Dokter --</option>";
    if(!empty($resultdokter)){
        foreach($resultdokter as $d ){
            // Sesuaikan "PEGAWAI_ID" dan "NAMA_PEGAWAI" dengan alias dari query model Anda
            $opt_dokter .= "<option value='".$d->DOKTER_ID."'>".$d->NAMA."</option>";
        }
    }
    
    // 4. Masukkan ke dalam array data yang akan dikirim ke View
    $data['opt_dokter'] = $opt_dokter;
    
    return $data;
    }


    
    // public function export_pdf() {
    // // Karena dipanggil via AJAX POST, ubah input->get menjadi input->post
    // $startdate = $this->input->post('startdate');
    // $endate    = $this->input->post('endate');
    // $dokter_id = $this->input->post('dokter_id');

    // $data['rekap']     = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
    // $data['rincian']   = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    // $data['startdate'] = $startdate;
    // $data['endate']    = $endate;

    // // Load file HTML template PDF Anda menjadi string
    // $html = $this->load->view('cetakpdf/v_pdf_rekapperdokter', $data, TRUE);

    // // Kirim kembali ke View dalam format JSON
    // echo json_encode(['responCode' => '00', 'html' => $html]);
    // }

    public function export_excel() {
    // 1. Tangkap parameter filter yang dikirim dari form
    $startdate = $this->input->post('startdate');
    $endate    = $this->input->post('endate');
    $dokter_id = $this->input->post('dokter_id');
       
    // 2. Tarik data dari Model
    $data['rekap']     = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
    $data['rincian']   = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    $data['startdate'] = $startdate;
    $data['endate']    = $endate;

    // 3. Ambil nama dokter dari baris pertama data rincian secara dinamis
    $nama_dokter = !empty($data['rincian']) ? $data['rincian'][0]['NAMA_DOKTER'] : 'Semua_Dokter';

    // 4. Bersihkan nama dokter dari karakter spasi, titik, koma agar aman untuk nama file Windows/Linux
    $nama_dokter_clean = str_replace(array(' ', '.', ',', '/'), array('_', '', '', '_'), $nama_dokter);
    $nama_dokter_clean = preg_replace('/_+/', '_', $nama_dokter_clean); // Merapikan jika ada underscore ganda
    $nama_dokter_clean = trim($nama_dokter_clean, '_'); // Menghilangkan underscore di ujung teks

    // 5. Hilangkan tanda hubung pada tanggal (misal: 01-05-2026 menjadi 01052026)
    $tanggal_clean = str_replace('-', '', $startdate);

    // 6. Satukan menjadi nama file yang diinginkan
    $filename = "Laporan_Aktivitas_Dokter_" . $tanggal_clean . "_" . $nama_dokter_clean . ".xls";

    // 7. Set Header PHP untuk memaksa browser melakukan unduhan file Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    // 8. Load file template tampilan Excel
    $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
    }
                
    public function export_pdf() {
    $startdate = $this->input->post('startdate');
    $endate    = $this->input->post('endate');
    $dokter_id = $this->input->post('dokter_id');
    
    $data['rekap']     = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
    $data['rincian']   = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    $data['startdate'] = $startdate;
    $data['endate']    = $endate;

    // Load file tampilan HTML untuk PDF
    $html = $this->load->view('cetakpdf/v_pdf_rekapperdokter', $data, TRUE);

    // --- TAMBAHKAN BARIS INI ---
    // Memaksa sistem membaca seluruh library yang diunduh via Composer
    require_once FCPATH . 'vendor/autoload.php'; 
    // ---------------------------

    // Inisialisasi DOMPDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    
    // Pengaturan ukuran presisi F4 dalam satuan points (595.28 x 935.43)
    $dompdf->setPaper(array(0, 0, 595.28, 935.43), 'portrait');
    
    // Render HTML ke format PDF
    $dompdf->render();
    
    // Alirkan dokumen langsung ke tab baru browser
    $dompdf->stream("Laporan_Aktivitas_Dokter.pdf", array("Attachment" => 0));
    }


    
    public function get_rekap_aktivitas(){
    // Menangkap data dari form/AJAX di View
        $startdate  = $this->input->post("startdate");
        $endate     = $this->input->post("endate");
        $dokter_id  = $this->input->post("dokter_id"); // Sudah benar menangkap dokter_id
        
        // PERBAIKAN: Ganti variabel $episode_id menjadi $dokter_id pada parameter ketiga
        $result = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
        
        // Mengembalikan balasan JSON
        if(!empty($result)){
            $json["responCode"]   = "00";
            $json["responHead"]   = "success";
            $json["responDesc"]   = "Data Ditemukan";
            $json['responResult'] = $result;
        }else{
            $json["responCode"]   = "01";
            $json["responHead"]   = "info";
            $json["responDesc"]   = "Data Tidak Ditemukan";
            $json['responResult'] = array();
        }

        echo json_encode($json);
    }


    public function get_rincian_pasien() {
    // Membungkam pesan error PHP (seperti header.php) agar JSON tidak cacat
    error_reporting(0); 

    $startdate  = $this->input->post("startdate");
    $endate     = $this->input->post("endate");
    $dokter_id  = $this->input->post("dokter_id");
    
    // Panggil method baru yang ada di Model
    $result = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    
    if(!empty($result)){
        $json["responCode"]   = "00";
        $json["responHead"]   = "success";
        $json["responDesc"]   = "Data Ditemukan";
        $json['responResult'] = $result;
    }else{
        $json["responCode"]   = "01";
        $json["responHead"]   = "info";
        $json["responDesc"]   = "Data Tidak Ditemukan";
        $json['responResult'] = array();
    }

    echo json_encode($json);
}

    
}


?>