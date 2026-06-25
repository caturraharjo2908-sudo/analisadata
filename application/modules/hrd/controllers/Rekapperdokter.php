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

    // public function export_excel() {
    // // 1. Tangkap parameter filter yang dikirim dari form
    // $startdate = $this->input->post('startdate');
    // $endate    = $this->input->post('endate');
    // $dokter_id = $this->input->post('dokter_id');
       
    // // // 2. Tarik data dari Model
    // // $data['rekap']     = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
    // // $data['rincian']   = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    // // $data['startdate'] = $startdate;
    // // $data['endate']    = $endate;

    // // --- 2. KODINGAN BARU DITAMBAHKAN ---
    // $data['rekap_jenis'] = $this->md->datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id);
    // $data['rincian']     = $this->md->datarincianpasien($startdate, $endate, $dokter_id);
    
    // $data['startdate'] = $startdate;
    // $data['endate']    = $endate;


    // // 3. Ambil nama dokter dari baris pertama data rincian secara dinamis
    // $nama_dokter = !empty($data['rincian']) ? $data['rincian'][0]['NAMA_DOKTER'] : 'Semua_Dokter';

    // // 4. Bersihkan nama dokter dari karakter spasi, titik, koma agar aman untuk nama file Windows/Linux
    // $nama_dokter_clean = str_replace(array(' ', '.', ',', '/'), array('_', '', '', '_'), $nama_dokter);
    // $nama_dokter_clean = preg_replace('/_+/', '_', $nama_dokter_clean); 
    // $nama_dokter_clean = trim($nama_dokter_clean, '_'); 

    // // 5. PERBAIKAN: Menggunakan tanggal hari ini dengan format rapat (contoh hasil: 29052026)
    // $tanggal_clean = date('dmY');

    // // 6. Satukan menjadi nama file yang diinginkan
    // $filename = "Laporan_Aktivitas_Dokter_" . $tanggal_clean . "_" . $nama_dokter_clean . ".xls";

    // // 7. Set Header PHP untuk memaksa browser melakukan unduhan file Excel
    // header("Content-Type: application/vnd.ms-excel");
    // header("Content-Disposition: attachment; filename=\"$filename\"");
    // header("Cache-Control: max-age=0");

    // // 8. Load file template tampilan Excel
    // $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
    // }

//    public function export_excel() {
//     error_reporting(0); // Membungkam error header.php
//     if (ob_get_level()) { ob_end_clean(); }

//     $startdate = $this->input->post('startdate');
//     $endate    = $this->input->post('endate');
//     $dokter_id = $this->input->post('dokter_id');
    
//     // Memanggil 2 fungsi model yang Anda minta
//     $data['rekap']   = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
//     $data['rincian'] = $this->md->datarincianpasien_bykeuepisode($startdate, $endate, $dokter_id);
    
//     $data['startdate'] = $startdate;
//     $data['endate']    = $endate;

//     // Nama file dinamis
//     $tanggal_clean = date('dmY');
//     $filename = "Laporan_Aktivitas_Dokter_" . $tanggal_clean . ".xls";

//     header("Content-Type: application/vnd.ms-excel");
//     header("Content-Disposition: attachment; filename=\"$filename\"");
//     header("Cache-Control: max-age=0");

//     $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
//     }

    // public function export_excel() {
    //     error_reporting(0); // Membungkam error header.php
    //     if (ob_get_level()) { ob_end_clean(); }

    //     $startdate = $this->input->post('startdate');
    //     $endate    = $this->input->post('endate');
    //     $dokter_id = $this->input->post('dokter_id');
        
    //     // Memanggil model rekap aktivitas 
    //     $data['rekap']   = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
        
    //     // --- PERBAIKAN DI SINI: Kirim 6 parameter dengan urutan yang benar ---
    //     $data['rincian'] = $this->md->datarincianpasien_bykeuepisode(
    //         $dokter_id, $startdate, $endate, // Parameter untuk Rawat Jalan
    //         $dokter_id, $startdate, $endate  // Parameter untuk Rawat Inap
    //     );
    //     // ----------------------------------------------------------------------

    //     $data['startdate'] = $startdate;
    //     $data['endate']    = $endate;

    //     // Nama file dinamis
    //     $tanggal_clean = date('dmY');
    //     $filename = "Laporan_Aktivitas_Dokter_" . $tanggal_clean . ".xls";

    //     header("Content-Type: application/vnd.ms-excel");
    //     header("Content-Disposition: attachment; filename=\"$filename\"");
    //     header("Cache-Control: max-age=0");

    //     $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
    // }
                

    public function export_excel() {
        error_reporting(0); // Membungkam error header.php
        if (ob_get_level()) { ob_end_clean(); }

        $startdate = $this->input->post('startdate');
        $endate    = $this->input->post('endate');
        $dokter_id = $this->input->post('dokter_id');
        
        // 1. Pemanggilan Rekap Pasien (BUTUH 6 PARAMETER KARENA UNION)
        // Urutan: ID Dokter, Tanggal Awal, Tanggal Akhir
        $data['rekap'] = $this->md->datarincianpasien_bykeuepisode(
            $dokter_id, $startdate, $endate, // Untuk Rawat Jalan
            $dokter_id, $startdate, $endate  // Untuk Rawat Inap
        );
        
        // 2. Pemanggilan Aktivitas Dokter (HANYA BUTUH 3 PARAMETER NORMAL)
        // Urutan: Tanggal Awal, Tanggal Akhir, ID Dokter
        $data['rincian'] = $this->md->datarekapaktivitasdokter_jenisPelayanan(
            $startdate, $endate, $dokter_id
        );
        
        $data['startdate'] = $startdate;
        $data['endate']    = $endate;

        // 2. Ambil nama dokter secara dinamis dari database berdasarkan dokter_id
        $nama_dokter = 'Semua_Dokter';
        if (!empty($dokter_id)) {
            $query_dokter = $this->db->query("SELECT NAMA FROM WEB_CO_DOKTER_MS WHERE DOKTER_ID = ?", array($dokter_id))->row();
            if ($query_dokter) {
                $nama_dokter = $query_dokter->NAMA;
            }
        }

        // 3. Bersihkan nama dokter dari karakter spasi, titik, koma, dll agar aman untuk file-system
        $nama_dokter_clean = str_replace(array(' ', '.', ',', '/'), array('_', '', '', '_'), $nama_dokter);
        $nama_dokter_clean = preg_replace('/_+/', '_', $nama_dokter_clean); 
        $nama_dokter_clean = trim($nama_dokter_clean, '_'); 

        // 4. PENYESUAIAN: Menyusun nama file dengan NAMA DOKTER di PALING AWAL
        $tanggal_clean = date('dmY');
        $filename = $nama_dokter_clean . "_Laporan_Aktivitas_Dokter_" . $tanggal_clean . ".xls";

        // 5. Set Header PHP untuk mengunduh Excel
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        // MENAMBAHKAN VARIABEL NAMA DOKTER KE DALAM DATA VIEW
        $data['nama_dokter'] = $nama_dokter;

        $this->load->view('cetakpdf/v_excel_rekapperdokter', $data);
    }

    // public function export_pdf() {
    // $startdate = $this->input->post('startdate');
    // $endate    = $this->input->post('endate');
    // $dokter_id = $this->input->post('dokter_id');
    
    // $data['rekap']     = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
    // // Mengirim 6 parameter dengan urutan: ID Dokter, Start Date, End Date
    // $data['rincian'] = $this->md->datarincianpasien_bykeuepisode(
    // $dokter_id, $startdate, $endate,  // Ini akan masuk ke parameter Rajal
    // $dokter_id, $startdate, $endate   // Ini akan masuk ke parameter Ranap
    // );
    // // $data['rincian'] = $this->md->datarincianpasien_bykeuepisode($startdate, $endate, $dokter_id);
    // $data['startdate'] = $startdate;
    // $data['endate']    = $endate;

    // // Load file tampilan HTML untuk PDF
    // $html = $this->load->view('cetakpdf/v_pdf_rekapperdokter', $data, TRUE);

    // // --- TAMBAHKAN BARIS INI ---
    // // Memaksa sistem membaca seluruh library yang diunduh via Composer
    // require_once FCPATH . 'vendor/autoload.php'; 
    // // ---------------------------

    // // Inisialisasi DOMPDF
    // $dompdf = new \Dompdf\Dompdf();
    // $dompdf->loadHtml($html);
    
    // // Pengaturan ukuran presisi F4 dalam satuan points (595.28 x 935.43)
    // $dompdf->setPaper(array(0, 0, 595.28, 935.43), 'portrait');
    
    // // Render HTML ke format PDF
    // $dompdf->render();
    
    // // Alirkan dokumen langsung ke tab baru browser
    // $dompdf->stream("Laporan_Aktivitas_Dokter.pdf", array("Attachment" => 0));
    // }


    
    // public function get_rekap_aktivitas(){
    // // Menangkap data dari form/AJAX di View
    //     $startdate  = $this->input->post("startdate");
    //     $endate     = $this->input->post("endate");
    //     $dokter_id  = $this->input->post("dokter_id"); // Sudah benar menangkap dokter_id
        
    //     // PERBAIKAN: Ganti variabel $episode_id menjadi $dokter_id pada parameter ketiga
    //     $result = $this->md->datarekapaktivitasdokter($startdate, $endate, $dokter_id);
        
    //     // Mengembalikan balasan JSON
    //     if(!empty($result)){
    //         $json["responCode"]   = "00";
    //         $json["responHead"]   = "success";
    //         $json["responDesc"]   = "Data Ditemukan";
    //         $json['responResult'] = $result;
    //     }else{
    //         $json["responCode"]   = "01";
    //         $json["responHead"]   = "info";
    //         $json["responDesc"]   = "Data Tidak Ditemukan";
    //         $json['responResult'] = array();
    //     }

    //     echo json_encode($json);
    // }

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

    public function datarincianpasien_bykeuepisode() {
        // Membungkam pesan error PHP agar JSON tidak cacat
        error_reporting(0); 

        // Cukup tangkap 3 parameter dari AJAX View
        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->input->post("dokter_id");
    
        // Panggil method model dengan menggandakan parameternya (3 untuk rajal, 3 untuk ranap)
        $result = $this->md->datarincianpasien_bykeuepisode(
            $dokter_id, $startdate, $endate, // Parameter Rawat Jalan
            $dokter_id, $startdate, $endate  // Parameter Rawat Inap
        );
        
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


    public function datarekapaktivitasdokter_jenisPelayanan() {
        // Membungkam pesan error PHP agar tidak merusak format JSON
        error_reporting(0); 

        $startdate = $this->input->post("startdate");
        $endate    = $this->input->post("endate");
        $dokter_id = $this->input->post("dokter_id");

        // Memanggil fungsi model dengan 3 parameter utama
        $result = $this->md->datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id);

        if(!empty($result)){
            $json["responCode"]   = "00";
            $json["responHead"]   = "success";
            $json["responDesc"]   = "Data Aktivitas Ditemukan";
            $json['responResult'] = $result;
        } else {
            $json["responCode"]   = "01";
            $json["responHead"]   = "info";
            $json["responDesc"]   = "Data Aktivitas Tidak Ditemukan";
            $json['responResult'] = array();
        }

        echo json_encode($json);
    }
    
}


?>