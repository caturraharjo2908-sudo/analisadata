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
                        SELECT A.*, TO_CHAR(A.LAST_UPDATE,'DD.MM.YYYY HH24:MI:SS')LASTUPDATE
                        FROM MV_JUMLAH_RESEP_PER_PROVIDER A
                        WHERE TAHUN='".$periode."'
                        ORDER BY A.PROVIDER ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datatransaksidepo($periode){
            $query =
                    "
                        SELECT A.*, TO_CHAR(A.LAST_UPDATE,'DD.MM.YYYY HH24:MI:SS')LASTUPDATE
                        FROM MV_JUMLAH_RESEP_PER_DEPO A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.NAMAGUDANG ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }






    }
?>