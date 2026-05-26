<?php
class Modelrekapperdokter extends CI_Model {
    
    public function periode() {
        $query = "
            SELECT 
                TO_CHAR(dt,'FMMonth YYYY','NLS_DATE_LANGUAGE=INDONESIAN') AS PERIODE,
                TO_CHAR(dt, 'MM.YYYY') AS PERIODE_KEY
            FROM (
                SELECT ADD_MONTHS(DATE '2015-01-01', LEVEL-1) dt
                FROM DUAL
                CONNECT BY ADD_MONTHS(DATE '2015-01-01', LEVEL-1) <= TRUNC(SYSDATE, 'MM')
            )
            ORDER BY dt DESC
        ";

        $recordset = $this->db->query($query);
        return $recordset->result();
    }

    // public function get_list_dokter() {
    //     $query = "SELECT  DISTINCT   
    //     A.DOKTER_ID,
    //     A.NAMA 
    //     FROM WEB_CO_DOKTER_MS A
    //     WHERE A.AKTIF = '1' 
    //     AND A.JENIS_DOKTER = '1' 
    //     AND A.DOKTER_ID IS NOT NULL 
    //     -- PERBAIKAN: Menggunakan operator <> (tidak sama dengan)
    //     AND A.DOKTER_ID <> 'DRRSUD' 
    //     ORDER BY A.NAMA ASC";
    //     $recordset = $this->db->query($query);
    //     return $recordset->result();
    //     }


    public function get_list_dokter() {
    $query = "SELECT DISTINCT 
            A.DOKTER_ID, 
            UPPER(A.NAMA) as NAMA 
        FROM WEB_CO_DOKTER_MS A
        LEFT JOIN SR01_GEN_USER_DATA B ON B.DOKTER_ID = A.DOKTER_ID
        LEFT JOIN HRD_KARYAWAN_MS C ON B.NIK = C.NIK
        WHERE A.AKTIF = '1' 
            AND A.JENIS_DOKTER = '1' 
            AND A.DOKTER_ID IS NOT NULL 
            AND A.DOKTER_ID <> 'DRRSUD' 
            AND C.STAT_PEK = 'T'
        ORDER BY A.DOKTER_ID ASC ";
    
    $recordset = $this->db->query($query);
    return $recordset->result();
}

    public function datarekapaktivitasdokter($startdate, $endate, $dokter_id) {
        $query = "SELECT 
            A.LAYAN_ID, 
            SUM(A.QTY) as JML,
            B.NAMA_LAYAN1 as NAMAPELAYANAN
        FROM SR01_KEU_TRANSCTR_IT A
        JOIN SR01_KEU_LAYAN_MS B ON A.LAYAN_ID = B.LAYAN_ID AND B.LOKASI_ID = '001'
        WHERE A.LOKASI_ID = '001'
        AND A.AKTIF = '1'
        AND B.AKTIF = '1'
        AND A.CREATED_BY = ? 
        AND B.NAMA_LAYAN1 IS NOT NULL 
        AND B.NAMA_LAYAN1 NOT LIKE '%Pendaftaran%'
        AND A.CREATED_DATE >= TO_DATE(?, 'DD-MM-YYYY')
        AND A.CREATED_DATE <  TO_DATE(?, 'DD-MM-YYYY')
        GROUP BY 
            A.LAYAN_ID, 
            B.NAMA_LAYAN1
        ORDER BY JML DESC";

        // Urutan binding array harus mengikat $dokter_id ke tanda tanya (?) pertama pada query (A.CREATED_BY)
        $queryExec = $this->db->query($query, array($dokter_id, $startdate, $endate));
        $rows = $queryExec->result_array();

        return $rows;
    }
    

    public function datarincianpasien($startdate, $endate, $dokter_id) {
    $query = "SELECT 
        TO_CHAR(A.CREATED_DATE, 'DD-MM-YYYY') AS TANGGAL,
        B.NAMA AS NAMA_DOKTER,
        COUNT(DISTINCT A.EPISODE_ID) AS JMLPASIEN
    FROM SR01_KEU_TRANSCTR_IT A
    LEFT JOIN WEB_CO_DOKTER_MS B ON A.CREATED_BY = B.DOKTER_ID
    LEFT JOIN SR01_GEN_PASIEN_MS C ON A.PASIEN_ID = C.PASIEN_ID
    WHERE A.LOKASI_ID = '001'
        AND A.AKTIF = '1'  
        AND B.AKTIF = '1'
        AND A.CREATED_BY = ?    
        AND A.CREATED_DATE >= TO_DATE(?, 'DD-MM-YYYY')
        AND A.CREATED_DATE <  TO_DATE(?, 'DD-MM-YYYY')
    GROUP BY 
        TO_CHAR(A.CREATED_DATE, 'DD-MM-YYYY'),
        B.NAMA
    ORDER BY 
        TO_DATE(TO_CHAR(A.CREATED_DATE, 'DD-MM-YYYY'), 'DD-MM-YYYY') ASC";

    // Urutan binding: ID Dokter, Tanggal Awal, Tanggal Akhir
    $queryExec = $this->db->query($query, array($dokter_id, $startdate, $endate));
    return $queryExec->result_array();
    }
}
?>