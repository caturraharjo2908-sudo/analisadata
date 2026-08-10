<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regulasiprobis extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Tambahkan baris ini jika sistem Anda mewajibkannya untuk otentikasi
        rootsystem::system(); 
        
        // Load model dan library
        $this->load->model('Modelregulasiprobis');
        $this->load->helper(array('form', 'url', 'file'));
        $this->load->library('session');
    }

    // // 1. Menampilkan Halaman Utama (Tabel Data)
    // public function index() {
    //     // Tarik data regulasi dari database
    //     $data['list_regulasi'] = $this->Modelregulasiprobis->get_all_regulasi();
        
    //     // Gunakan metode pemanggilan template yang SAMA persis dengan DashboardTB
    //     $this->template->load("template/template-sidebar", "v_regulasiprobis", $data); 
    // }

    public function index() {
        // Langsung panggil tanpa mengirimkan $user_id ke dalam kurung
        $data['list_regulasi'] = $this->Modelregulasiprobis->get_all_regulasi_with_departemen();
        
        // Load ke template
        $this->template->load("template/template-sidebar", "v_regulasiprobis", $data); 
    }

    // 2. Proses Menyimpan Data dan Upload PDF
    public function simpan_regulasi() {
        $this->load->library('upload');

        // Pastikan folder ini sudah ada dan XAMPP/Apache memiliki akses read/write
        $config['upload_path']   = 'D:/upload/REGULASI/'; 
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 10240; // Maksimal 10 MB

        // Bersihkan nama file dari spasi dan tambahkan timestamp agar unik
        $nama_asli = $_FILES['file_dokumen']['name'];
        $nama_bersih = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $nama_asli);
        $config['file_name'] = time() . '_' . $nama_bersih;

        $this->upload->initialize($config);

        if ($this->upload->do_upload('file_dokumen')) {
            // Jika berhasil upload ke Drive D
            $fileData = $this->upload->data();
            $nama_file_tersimpan = $fileData['file_name'];

        //     // Tangkap inputan dari form
        //     $nomor       = $this->input->post('nomor_dokumen', TRUE);
        //     $judul       = $this->input->post('judul_dokumen', TRUE);
        //     $deskripsi   = $this->input->post('deskripsi', TRUE);
        //     $jenis       = $this->input->post('jenis_dokumen', TRUE);
        //     $tgl_berlaku = $this->input->post('tanggal_berlaku', TRUE); // Format: YYYY-MM-DD
            
        //    // Tangkap session sesuai dengan yang di-set pada Sign.php
        //     $nama_user = $this->session->userdata('name') ? $this->session->userdata('name') : 'ADMIN_RS';
        //     $user_id   = $this->session->userdata('userid') ? $this->session->userdata('userid') : 'SYSTEM';

        //     // Eksekusi insert ke database (Tambahkan parameter $user_id di urutan paling belakang)
        //     $this->Modelregulasiprobis->insert_regulasi(
        //         $nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $nama_file_tersimpan, $nama_user, $user_id
        //     );

       // Tangkap inputan dari form secara lengkap
            $nomor          = $this->input->post('nomor_dokumen', TRUE);
            $judul          = $this->input->post('judul_dokumen', TRUE);
            $deskripsi      = $this->input->post('deskripsi', TRUE);
            $jenis          = $this->input->post('jenis_dokumen', TRUE);
            $tgl_berlaku    = $this->input->post('tanggal_berlaku', TRUE); // Format: YYYY-MM-DD
            $status_riwayat = $this->input->post('status_riwayat', TRUE); 
            $kata_kunci     = $this->input->post('kata_kunci', TRUE);
            
            // Tangkap session
            $nama_user = $this->session->userdata('name') ? $this->session->userdata('name') : 'ADMIN_RS';
            $user_id   = $this->session->userdata('userid') ? $this->session->userdata('userid') : 'SYSTEM';

            // Eksekusi insert ke database (PILIH YANG INI, DENGAN 10 PARAMETER)
            $this->Modelregulasiprobis->insert_regulasi(
                $nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $nama_file_tersimpan, $nama_user, $user_id, $status_riwayat, $kata_kunci
            );

            $this->session->set_flashdata('pesan_sukses', 'Dokumen berhasil diunggah dan disimpan.');
            redirect('regulasi/regulasiprobis');

            

        } else {
            // Jika upload gagal (misal bukan PDF atau ukuran terlalu besar)
            $error = $this->upload->display_errors('', '');
            $this->session->set_flashdata('pesan_error', 'Gagal unggah: ' . $error);
           // UBAH BARIS INI
            redirect('regulasi/regulasiprobis');
        }
    }

    // 3. Fungsi untuk Menghapus Data (Soft Delete)
    public function hapus_regulasi($id_regulasi) {
        // Tangkap nama dari session (gunakan 'name' seperti yang diatur di Sign.php)
        $nama_user = $this->session->userdata('name') ? $this->session->userdata('name') : 'SYSTEM';
        
        // (Opsional) Jika Anda lebih ingin menyimpan ID-nya (misal 'U001') ke UPDATED_BY, gunakan 'userid':
        // $nama_user = $this->session->userdata('userid') ? $this->session->userdata('userid') : 'SYSTEM';

        // Lempar nama user ke dalam model
        $this->Modelregulasiprobis->delete_regulasi($id_regulasi, $nama_user);
        
        $this->session->set_flashdata('pesan_sukses', 'Dokumen berhasil dinonaktifkan.');
        
        // Pastikan redirect tetap menggunakan modul
        redirect('regulasi/regulasiprobis');
    }

    // ========================================================================
    // 4. FUNGSI KRUSIAL: Menampilkan File PDF dari Drive D ke Browser
    // ========================================================================
    public function lihat_dokumen($nama_file) {
        // Lokasi absolut di server
        $path_file = 'D:/upload/REGULASI/' . $nama_file;

        // Cek apakah file fisik benar-benar ada di Drive D
        if (file_exists($path_file)) {
            // Beri tahu browser bahwa ini adalah file PDF yang harus ditampilkan di tab baru
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $nama_file . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            
            // Baca dan keluarkan isi file
            readfile($path_file);
        } else {
            // Jika file tidak ditemukan di server
            echo "Maaf, file fisik dokumen tidak ditemukan di server.";
        }
    }
}
?>