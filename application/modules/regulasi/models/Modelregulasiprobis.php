<?php
class Modelregulasiprobis extends CI_Model {


// =================================================================================
    // 1. TAMPIL DATA (SEMUA AKUN BISA MELIHAT SEMUA DOKUMEN)
    // =================================================================================
    public function get_all_regulasi_with_departemen() {
        
        $query = "
            SELECT 
                A.*, 
                C.NAMA_JABATAN AS BAGIAN
            FROM SIMRS_MANAGER.SR01_REGULASI A
            LEFT JOIN HRIS_MANAGER.HRD_KARYAWAN_MS B ON A.USER_ID = B.NIK
            LEFT JOIN SIMRS_MANAGER.SR01_STRUKTUR_RS C ON C.KODE_JABATAN = B.BAGIAN_ID
            WHERE A.AKTIF = '1'
            ORDER BY A.CREATED_DATE DESC
        ";
        
        return $this->db->query($query)->result();
    }
    // // 1. Menampilkan semua data regulasi aktif
    // public function get_all_regulasi() {
    //     $query = "
    //         SELECT 
    //             ID_REGULASI, 
    //             NOMOR_DOKUMEN, 
    //             JUDUL_DOKUMEN, 
    //             DESKRIPSI, 
    //             JENIS_DOKUMEN, 
    //             TO_CHAR(TANGGAL_BERLAKU, 'DD-MM-YYYY') AS TANGGAL_BERLAKU, 
    //             FILE_DOKUMEN, 
    //             CREATED_DATE, 
    //             CREATED_BY
    //         FROM SIMRS_MANAGER.SR01_REGULASI
    //         WHERE AKTIF = '1'
    //         ORDER BY CREATED_DATE DESC
    //     ";
        
    //     $recordset = $this->db->query($query);
    //     return $recordset->result();
    // }

    // 2. Menampilkan data spesifik berdasarkan ID
    public function get_regulasi_by_id($id_regulasi) {
        $query = "
            SELECT * FROM SIMRS_MANAGER.SR01_REGULASI
            WHERE ID_REGULASI = ? 
              AND AKTIF = '1'
        ";
        
        $recordset = $this->db->query($query, array($id_regulasi));
        return $recordset->row();
    }

    // 3. Fungsi bantuan untuk membuat ID otomatis (REG0000001)
    public function generate_id() {
        $query = "
            SELECT MAX(ID_REGULASI) AS MAX_ID 
            FROM SIMRS_MANAGER.SR01_REGULASI
        ";
        
        $recordset = $this->db->query($query);
        $row = $recordset->row();

        if (isset($row->MAX_ID) && $row->MAX_ID != '') {
            $last_number = (int) substr($row->MAX_ID, 3);
            $new_number = $last_number + 1;
            $new_id = 'REG' . sprintf("%07d", $new_number);
        } else {
            $new_id = 'REG0000001'; 
        }

        return $new_id;
    }

   // Tambahkan parameter $user_id di dalam kurung

    // public function insert_regulasi($nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $file, $nama_user, $user_id) {
    //     $id_regulasi = $this->generate_id();
        
    //     // Tambahkan USER_ID pada kolom insert dan tanda tanya (?) pada VALUES
    //     $query = "
    //         INSERT INTO SIMRS_MANAGER.SR01_REGULASI (
    //             ID_REGULASI, NOMOR_DOKUMEN, JUDUL_DOKUMEN, DESKRIPSI, 
    //             JENIS_DOKUMEN, TANGGAL_BERLAKU, FILE_DOKUMEN, CREATED_BY, USER_ID
    //         ) VALUES (
    //             ?, ?, ?, ?, ?, TO_DATE(?, 'YYYY-MM-DD'), ?, ?, ?
    //         )
    //     ";
        
    //     // Masukkan $user_id ke dalam urutan array paling belakang
    //     return $this->db->query($query, array(
    //         $id_regulasi, $nomor, $judul, $deskripsi, 
    //         $jenis, $tgl_berlaku, $file, $nama_user, $user_id
    //     ));
    // }
   public function insert_regulasi($nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $file, $nama_user, $user_id, $status_riwayat, $kata_kunci) {
        $id_regulasi = $this->generate_id();
        
        $query = "
            INSERT INTO SIMRS_MANAGER.SR01_REGULASI (
                ID_REGULASI, NOMOR_DOKUMEN, JUDUL_DOKUMEN, DESKRIPSI, 
                JENIS_DOKUMEN, TANGGAL_BERLAKU, FILE_DOKUMEN, CREATED_BY, USER_ID,
                STATUS_RIWAYAT, KATA_KUNCI
            ) VALUES (
                ?, ?, ?, ?, ?, TO_DATE(?, 'YYYY-MM-DD'), ?, ?, ?, ?, ?
            )
        ";
        
        return $this->db->query($query, array(
            $id_regulasi, $nomor, $judul, $deskripsi, 
            $jenis, $tgl_berlaku, $file, $nama_user, $user_id, 
            $status_riwayat, $kata_kunci
        ));
    }

    // 5. Mengubah data
    public function update_regulasi($id, $nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $file, $user) {
        $query = "
            UPDATE SIMRS_MANAGER.SR01_REGULASI 
            SET NOMOR_DOKUMEN = ?,
                JUDUL_DOKUMEN = ?,
                DESKRIPSI = ?,
                JENIS_DOKUMEN = ?,
                TANGGAL_BERLAKU = TO_DATE(?, 'YYYY-MM-DD'),
                FILE_DOKUMEN = ?,
                UPDATED_BY = ?,
                UPDATED_DATE = SYSDATE
            WHERE ID_REGULASI = ?
        ";
        
        return $this->db->query($query, array(
            $nomor, $judul, $deskripsi, $jenis, $tgl_berlaku, $file, $user, $id
        ));
    }

    // 6. Menghapus data (Soft Delete)
    public function delete_regulasi($id_regulasi, $user_id) {
        $query = "
            UPDATE SIMRS_MANAGER.SR01_REGULASI 
            SET AKTIF = '0', 
                UPDATED_BY = ?, 
                UPDATED_DATE = SYSDATE 
            WHERE ID_REGULASI = ?
        ";
        
        return $this->db->query($query, array($user_id, $id_regulasi));
    }
}
?>