<?php
    class Modelresep extends CI_Model{

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
                            E.REKANAN_ID,
                            
                            (SELECT NAMA FROM SR01_KEU_REKANAN_MS WHERE REKANAN_ID=E.REKANAN_ID)PROVIDER,
                            
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '01' THEN 1 END), 0) AS JAN,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '02' THEN 1 END), 0) AS FEB,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '03' THEN 1 END), 0) AS MAR,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '04' THEN 1 END), 0) AS APR,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '05' THEN 1 END), 0) AS MEI,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '06' THEN 1 END), 0) AS JUN,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '07' THEN 1 END), 0) AS JUL,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '08' THEN 1 END), 0) AS AGS,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '09' THEN 1 END), 0) AS SEP,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '10' THEN 1 END), 0) AS OKT,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '11' THEN 1 END), 0) AS NOV,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '12' THEN 1 END), 0) AS DES

                        FROM SR01_FRM_VALIDASI_HD A

                        JOIN SR01_KEU_EPISODE E 
                            ON E.PASIEN_ID = A.PASIEN_ID
                            AND E.EPISODE_ID = A.EPISODE_ID
                            AND E.STATUS_EPISODE <> '99'

                        WHERE A.LOKASI_ID = '001'
                        AND   A.AKTIF = '1'
                        AND   A.STATUS_TR = '00'
                        AND   A.PASIEN_ID IS NOT NULL
                        AND   A.EPISODE_ID IS NOT NULL
                        AND   A.TRANS_VALID_ID IS NOT NULL

                        AND TO_CHAR(A.TANGGAL,'YYYY') = '".$periode."'

                        GROUP BY E.REKANAN_ID
                        ORDER BY PROVIDER ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datatransaksidepo($periode){
            $query =
                    "
                        SELECT 
                            A.GUDANG_OBAT,
                            
                            (SELECT KETERANGAN FROM SR01_FRM_GUDANG_MS WHERE LOKASI_ID=A.GUDANG_OBAT)GUDANG,
                            
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '01' THEN 1 END), 0) AS JAN,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '02' THEN 1 END), 0) AS FEB,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '03' THEN 1 END), 0) AS MAR,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '04' THEN 1 END), 0) AS APR,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '05' THEN 1 END), 0) AS MEI,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '06' THEN 1 END), 0) AS JUN,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '07' THEN 1 END), 0) AS JUL,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '08' THEN 1 END), 0) AS AGS,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '09' THEN 1 END), 0) AS SEP,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '10' THEN 1 END), 0) AS OKT,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '11' THEN 1 END), 0) AS NOV,
                            NVL(SUM(CASE WHEN TO_CHAR(A.TANGGAL,'MM') = '12' THEN 1 END), 0) AS DES

                        FROM SR01_FRM_VALIDASI_HD A

                        JOIN SR01_KEU_EPISODE E 
                            ON E.PASIEN_ID = A.PASIEN_ID
                            AND E.EPISODE_ID = A.EPISODE_ID
                            AND E.STATUS_EPISODE <> '99'

                        WHERE A.LOKASI_ID = '001'
                        AND   A.AKTIF = '1'
                        AND   A.STATUS_TR = '00'
                        AND   A.PASIEN_ID IS NOT NULL
                        AND   A.EPISODE_ID IS NOT NULL
                        AND   A.TRANS_VALID_ID IS NOT NULL
                        AND   A.GUDANG_OBAT IS NOT NULL

                        AND TO_CHAR(A.TANGGAL,'YYYY') = '".$periode."'

                        GROUP BY A.GUDANG_OBAT
                        ORDER BY GUDANG
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }






    }
?>