<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelInventoriFarmasi extends CI_Model
{
    // Mengambil data stok berdasarkan kueri yang Anda berikan
    public function get_stok_obat()
    {
        $query = "
            SELECT 
                OBAT_ID,
                MAX(NAMA_OBAT) AS NAMA_OBAT,
                MAX(SATUAN_JL) AS SATUAN,
                SUM(STOK_PER_LOKASI) AS TOTAL_STOK_KESELURUHAN
            FROM (
                -- Subquery: Menghitung stok per lokasi dengan function
                SELECT 
                    LOKASI_ID,
                    OBAT_ID,
                    MAX(NAMA_OBAT) AS NAMA_OBAT,
                    MAX(SATUAN_JL) AS SATUAN_JL,
                    SIMRS_MANAGER.SR01_STOK_OBAT(OBAT_ID, LOKASI_ID) AS STOK_PER_LOKASI
                FROM 
                    SIMRS_MANAGER.SR01_FRM_GUDANG_STOK
                WHERE 
                    AKTIF = '1' 
                GROUP BY 
                    LOKASI_ID,
                    OBAT_ID
            )
            GROUP BY 
                OBAT_ID
            ORDER BY 
                TOTAL_STOK_KESELURUHAN DESC
        ";   
                        
        $recordset = $this->db->query($query);
        return $recordset->result_array(); 
    }


    // Tambahkan fungsi ini di bawah fungsi get_stok_obat() yang sudah ada
    public function get_stok_per_depo()
    {
        $query = "
            SELECT 
                LOKASI_ID,
                OBAT_ID,
                MAX(NAMA_OBAT) AS NAMA_OBAT,
                MAX(SATUAN_JL) AS SATUAN,
                SIMRS_MANAGER.SR01_STOK_OBAT(OBAT_ID, LOKASI_ID) AS STOK_AKHIR
            FROM 
                SIMRS_MANAGER.SR01_FRM_GUDANG_STOK
            WHERE 
                AKTIF = '1' 
            GROUP BY 
                LOKASI_ID,
                OBAT_ID
            ORDER BY 
                LOKASI_ID ASC, 
                NAMA_OBAT ASC,
                STOK_AKHIR DESC
        ";   
                        
        $recordset = $this->db->query($query);
        return $recordset->result_array(); 
    }
}
?>