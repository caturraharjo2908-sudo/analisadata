<?php
    class Modelworkloadrad extends CI_Model{

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

        function datatransaksi($periode){
            $query =
                    "
                        SELECT 
                            X.KODE_RADIOLOG,
                            X.NAMADOKTER,
                            X.JAM,
                            ROUND(AVG(X.JUMLAH_PASIEN),0) AS AVG_PASIEN
                        FROM (
                            SELECT 
                                A.KODE_RADIOLOG,
                                UPPER(A.NAMA_RADIOLOG) AS NAMADOKTER,
                                TO_CHAR(A.RADIOLOG_DATETIME_END, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.RADIOLOG_DATETIME_END, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                            FROM RAD_MANAGER.RIS_OUT A
                            WHERE A.KODE_RADIOLOG IS NOT NULL
                            AND TO_CHAR(A.RADIOLOG_DATETIME_END, 'YYYY') = '".$periode."'
                            GROUP BY 
                                A.KODE_RADIOLOG,
                                A.NAMA_RADIOLOG,
                                TO_CHAR(A.RADIOLOG_DATETIME_END, 'YYYY-MM-DD'),
                                TO_CHAR(A.RADIOLOG_DATETIME_END, 'HH24')
                        ) X
                        GROUP BY 
                            X.KODE_RADIOLOG,
                            X.NAMADOKTER,
                            X.JAM
                        ORDER BY 
                            X.NAMADOKTER,
                            X.JAM
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

    }
?>