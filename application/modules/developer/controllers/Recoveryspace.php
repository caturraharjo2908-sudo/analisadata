<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Panggil namespace Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

class Recoveryspace extends CI_Controller {

    public function __construct(){
        parent::__construct();
        // Memanggil fungsi core sistem (biasanya untuk validasi session/login)
        rootsystem::system(); 
        
        // Load model dan buat alias 'md' agar pemanggilannya lebih ringkas
        $this->load->model("Modelrecoveryspace", "md");
    }

    public function index(){
        // Menyiapkan variabel judul halaman
        $data['title'] = 'Monitoring Recovery Space Status';

        // --- TAMBAHAN BARU: Variabel Periode untuk Toolbar ---
        // Anda bisa mengisi ini dengan tag <option> HTML statis sebagai contoh dulu
        $data['referensi'] = '';

        // Mengumpulkan semua data dari model menggunakan alias $this->md
        $data['fra_usage']         = $this->md->get_fra_usage();
        $data['top_sql']           = $this->md->get_top_sql();
        $data['db_status']         = $this->md->get_db_status();
        $data['dataguard']         = $this->md->get_dataguard_status();
        $data['tablespace_usage']  = $this->md->get_tablespace_usage();
        $data['resource_limit']    = $this->md->get_resource_limit();
        $data['blocking_sessions'] = $this->md->get_blocking_sessions();
        $data['rman_backup']       = $this->md->get_rman_backup_status();

        // --- TAMBAHAN BARU: Data AWR ---
        $data['awr_data']          = $this->md->get_AWR();

        $data['instance_uptime']   = $this->md->get_instance_uptime();
        // Me-load view menggunakan library template (sudah otomatis menggabungkan header/sidebar/footer)
        $this->template->load("template/template-sidebar", "v_recoveryspace", $data);
    }


    public function cetak_laporan_bulanan() {
        // 1. Ambil SEMUA data dari Model yang dibutuhkan oleh View PDF
        $data['db_status']        = $this->md->get_db_status();
        $data['instance_uptime']  = $this->md->get_instance_uptime();
        
        // --- INI ADALAH 3 BARIS YANG HILANG SEBELUMNYA ---
        $data['tablespace_usage'] = $this->md->get_tablespace_usage();
        $data['fra_usage']        = $this->md->get_fra_usage();
        $data['awr_data']         = $this->md->get_AWR(); 
        // --------------------------------------------------
        
        // 2. Load View HTML ke dalam variabel string (parameter TRUE sangat penting)
        $html = $this->load->view('v_laprecoveryspace_pdf', $data, TRUE);

        // 3. Konfigurasi Opsi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $options->setDefaultFont('Helvetica');

        // 4. Inisialisasi Dompdf
        $dompdf = new Dompdf($options);

        // 5. Masukkan string HTML ke dalam engine Dompdf
        $dompdf->loadHtml($html);

        // 6. Atur ukuran dan orientasi kertas
        $dompdf->setPaper('A4', 'portrait');

        // 7. Render (Ubah HTML menjadi PDF)
        $dompdf->render();

        // 8. Output file ke Browser
        $nama_file = 'Laporan_Performa_Database_' . date('Y_m') . '.pdf';
        
        // Saya ubah Attachment menjadi 0 sementara agar Anda bisa melihat 
        // preview-nya langsung di browser tanpa harus download berulang kali.
        // Jika sudah fix, kembalikan ke 1.
        $dompdf->stream($nama_file, array("Attachment" => 0));
    }

    // Fungsi bawaan dari template Anda (dibiarkan jika nanti digunakan untuk AJAX / Filter Data)
    public function datatransaksi(){
        $periode = $this->input->post("selectperiode");
        
        // Catatan: Pastikan fungsi 'datatransaksi' ada di dalam Modelrecoveryspace.php 
        // jika fungsi ini memang ingin Anda gunakan nantinya.
        $result  = $this->md->datatransaksi($periode);

        if(!empty($result)){
            $json["responCode"]   = "00";
            $json["responHead"]   = "success";
            $json["responDesc"]   = "Data Di Temukan";
            $json['responResult'] = $result;
        }else{
            $json["responCode"] = "01";
            $json["responHead"] = "info";
            $json["responDesc"] = "Data Tidak Di Temukan";
        }

        echo json_encode($json);
    }
}
?>