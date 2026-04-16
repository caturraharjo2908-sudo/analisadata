<?php
    class Modelpsikotropika extends CI_Model{

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
                            A.OBAT_ID, 
                            A.NAMA,

                            T1.KETERANGAN AS TERAPI,
                            T2.KETERANGAN AS SUBTERAPI,

                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '01' THEN B.QTY END),0) AS JAN,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '02' THEN B.QTY END),0) AS FEB,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '03' THEN B.QTY END),0) AS MAR,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '04' THEN B.QTY END),0) AS APR,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '05' THEN B.QTY END),0) AS MEI,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '06' THEN B.QTY END),0) AS JUN,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '07' THEN B.QTY END),0) AS JUL,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '08' THEN B.QTY END),0) AS AGS,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '09' THEN B.QTY END),0) AS SEP,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '10' THEN B.QTY END),0) AS OKT,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '11' THEN B.QTY END),0) AS NOV,
                            NVL(SUM(CASE WHEN TO_CHAR(B.TANGGAL,'MM') = '12' THEN B.QTY END),0) AS DES

                        FROM SR01_FRM_OBAT_MS A

                        LEFT JOIN SR01_FRM_VALIDASI_IT B 
                            ON B.OBAT_ID = A.OBAT_ID
                            AND B.LOKASI_ID = '001'
                            AND B.SHOW_ITEM = '1'
                            AND B.TRANS_VALID_ID IS NOT NULL
                            AND TO_CHAR(B.TANGGAL,'YYYY') = '".$periode."'

                        LEFT JOIN SR01_FRM_TERAPI_MS T1 
                            ON T1.TERAPI_ID = A.TERAPI_ID
                            AND T1.AKTIF = '1'

                        LEFT JOIN SR01_FRM_TERAPI_MS T2 
                            ON T2.TERAPI_ID = A.SUB_TERAPI_ID
                            AND T2.AKTIF = '1'

                        WHERE A.GOL_OBAT = 'P'

                        GROUP BY 
                            A.OBAT_ID, 
                            A.NAMA,
                            T1.KETERANGAN,
                            T2.KETERANGAN

                        ORDER BY A.NAMA ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }






    }
?>