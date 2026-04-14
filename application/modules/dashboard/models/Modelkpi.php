<?php
    class Modelkpi extends CI_Model{

        function periode(){
            $query =
                    "
                        SELECT (2014 + LEVEL) AS PERIODE
                        FROM DUAL
                        CONNECT BY LEVEL <= EXTRACT(YEAR FROM SYSDATE) - 2014
                        ORDER BY PERIODE DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        
        function dataoperasielektif($periode){
            $query =
                    "
                    SELECT 
                        X.*,
                        (X.BATAL / NULLIF(X.TOTAL, 0)) * 100 AS PERSENTASI
                    FROM (
                        SELECT 
                            TO_CHAR(A.TGL_TINDAKAN, 'MM') AS PERIODE,
                            COUNT(*) AS TOTAL,
                            SUM(CASE WHEN A.STATUS_ID = '99' THEN 1 ELSE 0 END) AS BATAL
                        FROM SR01_MED_OK_LOG A
                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.CITO = '0'
                        AND TO_CHAR(A.TGL_TINDAKAN,'YYYY')='".$periode."'
                        GROUP BY TO_CHAR(A.TGL_TINDAKAN, 'MM')
                    ) X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangpasien($periode){
            $query =
                    "
                        SELECT 
                            X.*,
                            (X.SEBELUM_12 / NULLIF(X.TOTAL, 0)) * 100 AS PERSENTASI
                        FROM(
                            SELECT 
                                TO_CHAR(A.TGL_KELUAR,'MM') AS PERIODE,

                                COUNT(*) AS TOTAL,

                                SUM(CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 12 
                                        THEN 1 ELSE 0 
                                    END) AS SEBELUM_12,

                                SUM(CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                        THEN 1 ELSE 0 
                                    END) AS SESUDAH_12

                            FROM SR01_KEU_EPISODE A
                            WHERE A.LOKASI_ID = '001'
                            AND A.AKTIF = '1'
                            AND A.JENIS_EPISODE = 'I'
                            AND TO_CHAR(A.TGL_KELUAR,'YYYY')='".$periode."'

                            GROUP BY TO_CHAR(A.TGL_KELUAR,'MM')
                            ORDER BY PERIODE
                        )X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        
        
    }
?>