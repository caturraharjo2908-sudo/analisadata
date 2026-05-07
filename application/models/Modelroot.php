<?php
    class Modelroot extends CI_Model{
        function menu($userid){
            $query =
                    "
                        SELECT A.*
                        FROM SR01_GEN_MODULES_MS A
                        WHERE A.LOKASI_ID='001'
                        AND   A.AKTIF='1'
                        AND   A.SOURCECODE='ANALISA'
                        AND   A.MODULES_ID IN (SELECT MODULES_ID FROM SR01_GEN_ROLE_ACCESS_DT WHERE LOKASI_ID='001' AND AKTIF='1' AND USER_ID='".$userid."')
                        ORDER BY URUT ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }
    }
?>