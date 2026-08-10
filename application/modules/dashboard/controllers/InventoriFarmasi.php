<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventorifarmasi extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        // Memanggil fungsi sistem keamanan/autentikasi bawaan aplikasi Anda
        rootsystem::system();
        
        // Memuat Model
        // Jika model berada di folder yang sama (modules/dashboard/models), kodingan ini sudah cukup.
        // Jika model diletakkan di luar folder module (di application/models), gunakan ini juga.
        $this->load->model("ModelInventoriFarmasi", "md");
    }

    public function index(){
        // 1. Ambil data stok obat real-time dari Model
        $data['stok_farmasi'] = $this->md->get_stok_obat();
        // --- KODE BARU UNTUK TAB DETAIL PER DEPO ---
        $data['stok_per_depo'] = $this->md->get_stok_per_depo();
        // 2. Injeksi AI Smart Insights (dibuat menggunakan private function di bawah)
        $data['smart_insight'] = $this->generate_smart_insight($data['stok_farmasi']);

        // 3. Data untuk Chart (Mengambil Top 10 Obat dengan Stok Tertinggi)
        $top_10 = array_slice($data['stok_farmasi'], 0, 10);
        $data['chart_labels'] = json_encode(array_column($top_10, 'NAMA_OBAT'));
        $data['chart_values'] = json_encode(array_column($top_10, 'TOTAL_STOK_KESELURUHAN'));

        // 4. Load ke view menggunakan template sidebar Anda
        $this->template->load("template/template-sidebar", "v_InventoriFarmasi", $data);
    }

    // Fungsi private untuk Logika "AI" Sederhana pada View
    private function generate_smart_insight($data_stok) {
        if (empty($data_stok)) {
            return "Tidak ada data obat aktif yang terdeteksi di dalam sistem.";
        }

        $total_item = count($data_stok);
        $stok_kosong = 0;
        $total_kuantitas = 0;

        foreach ($data_stok as $row) {
            $total_kuantitas += $row['TOTAL_STOK_KESELURUHAN'];
            
            if ($row['TOTAL_STOK_KESELURUHAN'] <= 0) {
                $stok_kosong++;
            }
        }

        $narasi = "Saat ini terdapat <b>$total_item jenis obat aktif</b> dengan total keseluruhan kuantitas mencapai <b>" . number_format($total_kuantitas, 0, ',', '.') . " unit</b> di seluruh depo/gudang. ";

        if ($stok_kosong > 0) {
            $narasi .= "Sebagai catatan, terdapat <b class='text-danger'>$stok_kosong item obat</b> yang stoknya saat ini kosong (0 atau minus). Petugas farmasi disarankan segera melakukan pengecekan fisik atau membuat pengajuan restock ke pihak pengadaan untuk meminimalisir kekosongan layanan medis.";
        } else {
            $narasi .= "Kondisi stok secara umum terpantau aman tanpa ada item yang kosong secara total di seluruh depo. Kinerja manajemen inventaris berjalan sangat baik.";
        }

        return $narasi;
    }

   public function export_excel() {
        // 1. SEMENTARA KITA AKTIFKAN ERROR UNTUK DEBUGGING
        // Jika nanti sudah berhasil download, baris ini boleh dikembalikan menjadi error_reporting(0);
        error_reporting(E_ALL); 
        
        // 2. Kuras semua output buffer yang mungkin tertinggal dari file lain
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // 3. Tarik data dari model
        $data['stok_farmasi'] = $this->md->get_stok_obat();

        // 4. Format penamaan file
        $tanggal_clean = date('dmY');
        $waktu = date('His');
        $filename = "Laporan_Inventori_Farmasi_" . $tanggal_clean . "_" . $waktu . ".xls";

        // 5. Set Header PHP
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        // 6. Load View khusus Excel
        // PASTIKAN file 'v_excel_inventorifarmasi.php' benar-benar ada di dalam folder:
        // application/modules/dashboard/views/cetakpdf/
        $this->load->view('v_excel_inventorifarmasi', $data);
    }
}
?>