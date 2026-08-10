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
        AND A.CREATED_DATE <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
        GROUP BY 
            A.LAYAN_ID, 
            B.NAMA_LAYAN1
        ORDER BY JML DESC";

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
                AND A.CREATED_DATE <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
            GROUP BY 
                TO_CHAR(A.CREATED_DATE, 'DD-MM-YYYY'),
                B.NAMA
            ORDER BY 
                TO_DATE(TO_CHAR(A.CREATED_DATE, 'DD-MM-YYYY'), 'DD-MM-YYYY') ASC";

        $queryExec = $this->db->query($query, array($dokter_id, $startdate, $endate));
        return $queryExec->result_array();
    }

    public function datarincianpasien_bykeuepisode($dokter_idrajal, $startdaterajal, $endaterajal, $dokter_idranap, $startdateranap, $endateranap) {
        $query = "SELECT 'RAWAT JALAN' JENIS, DOKTER_ID, TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') PERIODE, COUNT(*) AS TOTAL_KUNJUNGAN
            FROM SR01_KEU_EPISODE A
            WHERE A.LOKASI_ID = '001'
            AND A.AKTIF = '1'
            AND A.JENIS_EPISODE = 'O'
            AND A.STATUS_EPISODE <> '99'
            AND A.DOKTER_ID=?
            AND  A.TGL_MASUK >= TO_DATE(?, 'DD-MM-YYYY')
            AND  A.TGL_MASUK <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
            AND (
                    (
                        A.POLI_ID NOT IN (
                            'UGD01',
                            'APS R0000000001',
                            'POLIFISIO',
                            'POLIFISOKUP',
                            'POLIFISWICARA',
                            'HEMOD0000000000'
                        )
                        AND EXISTS (
                            SELECT 1
                            FROM SR01_MED_PRWT_TR T
                            WHERE T.LOKASI_ID  = '001'
                            AND   T.AKTIF      = '1'
                            AND   T.DONE_STATUS= '01'
                            AND   T.STATUS     = '1'
                            AND   T.PASIEN_ID  = A.PASIEN_ID
                            AND   T.EPISODE_ID = A.EPISODE_ID
                        )
                    )
                    OR A.POLI_ID IN (
                        'POLIFISIO',
                        'POLIFISOKUP',
                        'POLIFISWICARA',
                        'HEMOD0000000000'
                    )
            )

            GROUP BY DOKTER_ID, TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') 

            UNION

            SELECT 'RAWAT INAP' JENIS, DOKTER_ID, TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') PERIODE, COUNT(*) AS TOTAL_KUNJUNGAN
            FROM SR01_KEU_EPISODE A
            WHERE A.LOKASI_ID = '001'
            AND A.AKTIF = '1'
            AND A.JENIS_EPISODE = 'I'
            AND A.STATUS_EPISODE <> '99'
            AND A.DOKTER_ID=?
            AND  A.TGL_MASUK >= TO_DATE(?, 'DD-MM-YYYY')
            AND  A.TGL_MASUK <  (TO_DATE(?, 'DD-MM-YYYY') + 1)

            GROUP BY DOKTER_ID, TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY')";

        $queryExec = $this->db->query($query, array($dokter_idrajal, $startdaterajal, $endaterajal, $dokter_idranap, $startdateranap, $endateranap));
        return $queryExec->result_array();
    }

    public function datarekapaktivitasdokter_jenisPelayanan($startdate, $endate, $dokter_id) {
        $query = "SELECT X.*,
               (SELECT NAMA_LAYAN1 FROM SR01_KEU_LAYAN_MS WHERE LAYAN_ID=X.LAYAN_ID)NAMAPELAYANAN,
               (SELECT NAMA FROM SR01_MED_DOKTER_MS WHERE DOKTER_ID=X.DOKTERID)NAMADOKTER
        FROM(
            SELECT 'TINDAKAN RAWAT INAP'JENIS, CREATED_BY DOKTERID, LAYAN_ID, SUM(QTY) AS TOTAL_QTY
            FROM SR01_KEU_TRANSCTR_IT A
            WHERE A.LOKASI_ID='001'
            AND   A.AKTIF='1'
            AND  A.CREATED_DATE >= TO_DATE(?, 'DD-MM-YYYY')
            AND  A.CREATED_DATE <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
            AND A.LAYAN_ID IS NOT NULL 
            AND A.LAYAN_ID NOT IN ('ADM03','ADM04','ADM01','ADM02','ADM00','ADM05','ADMPC01','XPENDA000000002','XPENDA000000001','XPENDA000000003')
            AND   A.CREATED_BY = ?
            GROUP BY CREATED_BY, LAYAN_ID

            UNION

            SELECT 'TINDAKAN ANASTESI'JENIS, ANS_DOKTER_ID DOKTERID, LAYAN_ID, SUM(QTY)
            FROM SR01_KEU_TRANSCTR_IT A
            WHERE A.LOKASI_ID='001'
            AND   A.AKTIF='1'
            AND  A.CREATED_DATE >= TO_DATE(?, 'DD-MM-YYYY')
            AND  A.CREATED_DATE <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
            AND A.LAYAN_ID IS NOT NULL 
            AND A.LAYAN_ID NOT IN ('ADM03','ADM04','ADM01','ADM02','ADM00','ADM05','ADMPC01','XPENDA000000002','XPENDA000000001','XPENDA000000003')
            AND   A.ANS_DOKTER_ID = ?
            GROUP BY ANS_DOKTER_ID, LAYAN_ID

            UNION

            SELECT 'TINDAKAN ANAK'JENIS, ANK_DOKTER_ID DOKTERID, LAYAN_ID, SUM(QTY)
            FROM SR01_KEU_TRANSCTR_IT A
            WHERE A.LOKASI_ID='001'
            AND   A.AKTIF='1'
            AND  A.CREATED_DATE >= TO_DATE(?, 'DD-MM-YYYY')
            AND  A.CREATED_DATE <  (TO_DATE(?, 'DD-MM-YYYY') + 1)
            AND A.LAYAN_ID IS NOT NULL 
            AND A.LAYAN_ID NOT IN ('ADM03','ADM04','ADM01','ADM02','ADM00','ADM05','ADMPC01','XPENDA000000002','XPENDA000000001','XPENDA000000003')
            AND   A.ANK_DOKTER_ID= ?
            GROUP BY ANK_DOKTER_ID, LAYAN_ID
        )X
        ORDER BY X.TOTAL_QTY DESC, NAMADOKTER, JENIS, NAMAPELAYANAN";

        $bind_params = array(
            $startdate, $endate, $dokter_id, 
            $startdate, $endate, $dokter_id, 
            $startdate, $endate, $dokter_id  
        );

        $queryExec = $this->db->query($query, $bind_params);
        return $queryExec->result_array();
    }
}
?>